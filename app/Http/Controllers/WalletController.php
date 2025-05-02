<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Payment;
use App\Facades\Zibal;

class WalletController extends Controller
{
    /**
     * Display the user's wallet.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get or create wallet
        $wallet = $user->getOrCreateWallet();
        
        // Get wallet balance
        $balance = $wallet->balance;
        
        // Get wallet transactions
        $transactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('wallet.index', compact('balance', 'transactions'));
    }
    
    /**
     * Display the deposit form.
     */
    public function showDepositForm()
    {
        return view('wallet.deposit');
    }
    
    /**
     * Process the deposit using Zibal payment gateway.
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000', // Minimum 10,000 Rials (100 Tomans)
        ]);
        
        $user = Auth::user();
        $amount = $request->input('amount');
        
        // Create a new payment record
        $payment = new Payment([
            'user_id' => Auth::id(),
            'amount' => $amount,
            'gateway' => 'zibal',
            'status' => 'pending',
            'description' => 'Wallet deposit',
            'metadata' => ['type' => 'wallet_deposit'],
        ]);
        $payment->save();
        
        // Callback URL
        $callbackUrl = route('wallet.deposit.callback');
        
        // Request payment from Zibal
        $response = Zibal::request(
            $payment->amount,
            $callbackUrl,
            $payment->id, // Use payment ID as order ID
            $payment->description,
            ['payment_id' => $payment->id, 'type' => 'wallet_deposit']
        );
        
        // Check if request was successful
        if ($response && isset($response['trackId'])) {
            // Update payment with track ID
            $payment->track_id = $response['trackId'];
            $payment->save();
            
            // Redirect to payment gateway
            return redirect()->away(Zibal::getPaymentUrl($response['trackId']));
        }
        
        // If request failed, update payment status
        $payment->status = 'failed';
        $payment->save();
        
        return redirect()->route('wallet.index')
            ->with('error', 'Error creating payment transaction. Please try again.');
    }
    
    /**
     * Handle payment callback for wallet deposits.
     */
    public function depositCallback(Request $request)
    {
        // Get track ID from request
        $trackId = $request->input('trackId');
        $success = $request->input('success') == 1;
        
        // Find payment by track ID
        $payment = Payment::where('track_id', $trackId)->first();
        
        // If payment not found, redirect with error
        if (!$payment) {
            return redirect()->route('wallet.index')
                ->with('error', 'Payment transaction not found.');
        }
        
        // If payment already verified, redirect with success
        if ($payment->status === 'verified') {
            return redirect()->route('wallet.index')
                ->with('success', 'Payment was already successfully processed.');
        }
        
        // If callback indicates failure and payment is still pending, update status
        if (!$success && $payment->status === 'pending') {
            $payment->status = 'failed';
            $payment->save();
            
            return redirect()->route('wallet.index')
                ->with('error', 'Payment was unsuccessful.');
        }
        
        // Verify payment with Zibal
        $verification = Zibal::verify($trackId);
        
        // If verification successful
        if ($verification && isset($verification['result']) && $verification['result'] == 100) {
            // Update payment details
            $payment->status = 'verified';
            $payment->ref_id = $verification['refNumber'] ?? null;
            $payment->payment_date = now();
            
            // Add card info if available
            if (isset($verification['cardNumber'])) {
                $payment->card_number = $verification['cardNumber'];
            }
            
            if (isset($verification['cardHash'])) {
                $payment->card_hash = $verification['cardHash'];
            }
            
            $payment->save();
            
            // Get user
            $user = User::find($payment->user_id);
            
            // Process the deposit to the wallet
            $transaction = $user->deposit($payment->amount / 10); // Convert Rials to Tomans or your app's currency
            
            // Redirect to wallet with success message
            return redirect()->route('wallet.index')
                ->with('success', 'Your payment was successful. The amount has been added to your wallet.');
        }
        
        // If verification failed, update status
        $payment->status = 'failed';
        $payment->save();
        
        // Get error message from result code if available
        $errorMessage = isset($verification['result']) 
            ? Zibal::getResultMessage($verification['result']) 
            : 'Error verifying payment. Please contact support.';
        
        return redirect()->route('wallet.index')
            ->with('error', $errorMessage);
    }
    
    /**
     * Display the withdrawal form.
     */
    public function showWithdrawForm()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        return view('wallet.withdraw', compact('wallet'));
    }
    
    /**
     * Process the withdrawal.
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);
        
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $amount = $request->input('amount');
        
        // Check if the user has enough balance
        if (!$user->canWithdraw($amount)) {
            return redirect()->back()
                ->with('error', 'Insufficient funds in your wallet');
        }
        
        // Withdraw from the wallet
        $transaction = $user->withdraw($amount);
        
        return redirect()->route('wallet.index')
            ->with('success', "Successfully withdrawn {$amount} from your wallet");
    }
    
    /**
     * Display the transfer form.
     */
    public function showTransferForm()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        return view('wallet.transfer', compact('wallet'));
    }
    
    /**
     * Process the transfer.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:1',
        ]);
        
        $sender = Auth::user();
        $recipient = User::where('email', $request->input('email'))->first();
        $amount = $request->input('amount');
        
        // Check if user is trying to transfer to self
        if ($sender->id === $recipient->id) {
            return redirect()->back()
                ->with('error', 'You cannot transfer to yourself');
        }
        
        // Check if the sender has enough balance
        if (!$sender->canWithdraw($amount)) {
            return redirect()->back()
                ->with('error', 'Insufficient funds in your wallet');
        }
        
        try {
            // Transfer the amount
            $transfer = $sender->transfer($recipient, $amount);
            
            return redirect()->route('wallet.index')
                ->with('success', "Successfully transferred {$amount} to {$recipient->name}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Display transaction history.
     */
    public function transactions()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $transactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(15);
        
        return view('wallet.transactions', compact('transactions'));
    }
} 