<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class WalletController extends Controller
{
    /**
     * Display the user's wallet.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get wallet balance
        $balance = $user->wallet->balance;
        
        // Get wallet transactions
        $transactions = $user->wallet->transactions()->orderBy('created_at', 'desc')->paginate(10);
        
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
     * Process the deposit.
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);
        
        $user = Auth::user();
        $amount = $request->input('amount');
        
        // Deposit to the wallet
        $transaction = $user->deposit($amount);
        
        return redirect()->route('wallet.index')
            ->with('success', "Successfully deposited {$amount} to your wallet");
    }
    
    /**
     * Display the withdrawal form.
     */
    public function showWithdrawForm()
    {
        return view('wallet.withdraw');
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
        return view('wallet.transfer');
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
        
        // Transfer the amount
        $transfer = $sender->transfer($recipient, $amount);
        
        return redirect()->route('wallet.index')
            ->with('success', "Successfully transferred {$amount} to {$recipient->name}");
    }
    
    /**
     * Display transaction history.
     */
    public function transactions()
    {
        $user = Auth::user();
        $transactions = $user->wallet->transactions()->orderBy('created_at', 'desc')->paginate(15);
        
        return view('wallet.transactions', compact('transactions'));
    }
} 