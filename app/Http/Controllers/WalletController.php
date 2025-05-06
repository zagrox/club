<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Payment;
use App\Models\WalletTransactionAdapter;
use App\Facades\Zibal;
use Illuminate\Support\Facades\Log;

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
        
        // Get wallet balance - now using bavix wallet format (stored as integer)
        $balance = $wallet->balance / 100; // Convert from cents
        
        // Get wallet transactions
        $bavixTransactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(10);
        
        // Adapt transactions for views
        $adapter = new WalletTransactionAdapter();
        $adaptedCollection = collect();
        
        foreach ($bavixTransactions->items() as $transaction) {
            $adaptedCollection->push($adapter->adapt($transaction));
        }
        
        // Replace the items in the paginator with adapted items
        $transactions = $bavixTransactions->setCollection($adaptedCollection);
        
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
        
        // Save amount in session for mock payment use
        session(['payment_amount' => $amount]);
        
        try {
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
                
                // Log payment request
                Log::info('Payment request successful', [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'track_id' => $response['trackId']
                ]);
                
                // Redirect to payment gateway
                return redirect()->away(Zibal::getPaymentUrl($response['trackId']));
            }
            
            // If request failed, update payment status
            $payment->status = 'failed';
            $payment->save();
            
            Log::error('Failed to get track ID from payment gateway', [
                'payment_id' => $payment->id,
                'response' => $response ?? 'No response'
            ]);
            
            return redirect()->route('wallet.index')
                ->with('error', __('Payment transaction failed. Please try again.'));
        } catch (\Exception $e) {
            Log::error('Exception during payment creation', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'amount' => $amount
            ]);
            
            return redirect()->route('wallet.index')
                ->with('error', __('System error in payment process. Please try again.'));
        }
    }
    
    /**
     * Handle payment callback for wallet deposits.
     */
    public function depositCallback(Request $request)
    {
        // Get track ID from request
        $trackId = $request->input('trackId');
        $success = $request->input('success') == 1;
        
        // Log all parameters received
        Log::info('Wallet deposit callback received', [
            'trackId' => $trackId,
            'success' => $success,
            'all_params' => $request->all()
        ]);
        
        // Find payment by track ID
        $payment = Payment::where('track_id', $trackId)->first();
        
        // If payment not found, redirect with error
        if (!$payment) {
            Log::error('Payment not found in deposit callback', ['trackId' => $trackId]);
            return redirect()->route('wallet.index')
                ->with('error', __('Error: Payment transaction not found. Please contact support.'));
        }
        
        // If payment already verified, redirect with success
        if ($payment->status === 'verified') {
            Log::info('Payment already verified', ['payment_id' => $payment->id, 'trackId' => $trackId]);
            return redirect()->route('wallet.index')
                ->with('success', __('Payment was already successful and amount has been added to your wallet.'));
        }
        
        // If callback indicates failure and payment is still pending, update status
        if (!$success && $payment->status === 'pending') {
            $payment->status = 'failed';
            $payment->save();
            
            Log::warning('Payment failed based on callback success parameter', ['payment_id' => $payment->id]);
            return redirect()->route('wallet.index')
                ->with('error', __('Payment was unsuccessful. Please try again.'));
        }
        
        try {
            // Verify payment with Zibal
            $verification = Zibal::verify($trackId);
            Log::info('Zibal verification response', ['verification' => $verification, 'payment_id' => $payment->id]);
            
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
                
                if (!$user) {
                    Log::error('User not found for payment', ['payment_id' => $payment->id, 'user_id' => $payment->user_id]);
                    return redirect()->route('wallet.index')
                        ->with('error', __('Error: User associated with this payment not found.'));
                }
                
                // Get the original amount in Rials
                $amountInRials = $payment->amount;
                
                // Calculate Tomans (divide by 10)
                $amountInTomans = $amountInRials / 10;
                
                // Format amounts for display
                $formattedRials = number_format($amountInRials) . ' ریال';
                $formattedTomans = number_format($amountInTomans) . ' تومان';
                
                // Process the deposit to the wallet (wallet stores values in Tomans)
                try {
                    // Deposit to the wallet with bavix/wallet (amount in cents/lowest denomination)
                    $intAmount = (int)($amountInTomans * 100);
                    
                    $transaction = $user->depositWithDescription($amountInTomans, 'شارژ کیف پول از درگاه پرداخت', [
                        'payment_id' => $payment->id,
                        'track_id' => $trackId,
                        'ref_id' => $payment->ref_id
                    ]);
                    
                    Log::info('Wallet successfully charged', [
                        'user_id' => $user->id,
                        'payment_id' => $payment->id,
                        'amount_rials' => $amountInRials,
                        'amount_tomans' => $amountInTomans,
                        'transaction_id' => $transaction->id
                    ]);
                    
                    // Redirect to wallet with success message
                    return redirect()->route('wallet.index')
                        ->with('success', __('Your payment was successful. Amount :amount (:amount_rials) has been added to your wallet.', [
                            'amount' => $formattedTomans,
                            'amount_rials' => $formattedRials
                        ]));
                } catch (\Exception $e) {
                    Log::error('Error adding funds to wallet', [
                        'error' => $e->getMessage(),
                        'payment_id' => $payment->id,
                        'user_id' => $user->id
                    ]);
                    
                    return redirect()->route('wallet.index')
                        ->with('error', __('Error: Payment verified but adding amount to wallet failed. Please contact support.'));
                }
            }
            
            // If verification failed, update status
            $payment->status = 'failed';
            $payment->save();
            
            // Get error message from result code if available
            $errorMessage = isset($verification['result']) 
                ? Zibal::getResultMessage($verification['result']) 
                : 'خطا در تایید پرداخت. لطفا با پشتیبانی تماس بگیرید.';
            
            Log::warning('Payment verification failed', [
                'payment_id' => $payment->id,
                'error_code' => $verification['result'] ?? 'unknown',
                'error_message' => $errorMessage
            ]);
            
            return redirect()->route('wallet.index')
                ->with('error', $errorMessage);
                
        } catch (\Exception $e) {
            Log::error('Exception during payment verification', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
                'track_id' => $trackId
            ]);
            
            $payment->status = 'failed';
            $payment->save();
        
        return redirect()->route('wallet.index')
                ->with('error', 'خطای سیستمی در بررسی پرداخت. لطفا با پشتیبانی تماس بگیرید.');
        }
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
        if (!$user->canWithdrawAmount($amount)) {
            return redirect()->back()
                ->with('error', 'Insufficient funds in your wallet');
        }
        
        // Withdraw from the wallet
        $transaction = $user->withdrawWithDescription($amount, 'Withdrawal from wallet');
        
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
            'recipient_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:1',
        ]);
        
        $sender = Auth::user();
        $recipient = User::where('email', $request->input('recipient_email'))->first();
        $amount = $request->input('amount');
        
        // Check if user is trying to transfer to self
        if ($sender->id === $recipient->id) {
            return redirect()->back()
                ->with('error', __('You cannot transfer to yourself'));
        }
        
        // Check if the sender has enough balance
        if (!$sender->canWithdrawAmount($amount)) {
            return redirect()->back()
                ->with('error', __('Insufficient funds in your wallet'));
        }
        
        try {
            // Transfer the amount using the user model method (which now uses bavix/wallet)
            $transfer = $sender->transferToUser($recipient, $amount);
            
            return redirect()->route('wallet.index')
                ->with('success', __('Successfully transferred :amount to :name', [
                    'amount' => $amount,
                    'name' => $recipient->name
                ]));
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
        $bavixTransactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(15);
        
        // Adapt transactions for views
        $adapter = new WalletTransactionAdapter();
        $adaptedCollection = collect();
        
        foreach ($bavixTransactions->items() as $transaction) {
            $adaptedCollection->push($adapter->adapt($transaction));
        }
        
        // Replace the items in the paginator with adapted items
        $transactions = $bavixTransactions->setCollection($adaptedCollection);
        
        return view('wallet.transactions', compact('transactions'));
    }
} 