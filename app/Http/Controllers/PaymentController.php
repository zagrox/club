<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\ZibalPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $zibal;

    /**
     * Create a new controller instance.
     *
     * @param ZibalPayment $zibal
     */
    public function __construct(ZibalPayment $zibal)
    {
        $this->middleware('auth');
        // Remove the middleware for now, since we're using the route-level authorization
        // We'll use our custom admin route instead
        $this->zibal = $zibal;
    }

    /**
     * Show payment history - admin only dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Check if the user is an admin
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        // Build query with filters
        $query = Payment::with('user')->orderByDesc('created_at');
        
        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search in id, amount, description, or user name/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('track_id', 'like', "%{$search}%")
                  ->orWhere('ref_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Paginate results
        $payments = $query->paginate(20)->withQueryString();
        
        return view('payments.index', compact('payments'));
    }

    /**
     * Show payment details - admin only view.
     *
     * @param Payment $payment
     * @return \Illuminate\View\View
     */
    public function show(Payment $payment)
    {
        // Check if the user is an admin
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        // Only admins can see payment details
        return view('payments.show', compact('payment'));
    }

    /**
     * Direct payment requests are disabled for regular users - redirects to wallet deposit.
     * This is kept for backwards compatibility with existing code.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function request(Request $request)
    {
        return redirect()->route('wallet.showDepositForm')
            ->with('info', 'Direct payments are now processed through wallet deposits.');
    }

    /**
     * Handle payment callback from gateway - still needed for processing all callbacks.
     * This method remains accessible to both users and admin to process gateway callbacks.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        // Get track ID from request
        $trackId = $request->input('trackId');
        $success = $request->input('success') == 1;
        $status = $request->input('status');
        $orderId = $request->input('orderId');

        Log::info('Payment callback received', [
            'trackId' => $trackId,
            'success' => $success,
            'status' => $status,
            'orderId' => $orderId,
            'all_params' => $request->all()
        ]);

        // Find payment by track ID
        $payment = Payment::where('track_id', $trackId)->first();

        // If payment not found, check if orderId is provided and try to find payment by order_id
        if (!$payment && $orderId) {
            $payment = Payment::where('order_id', $orderId)->first();
            Log::info('Trying to find payment by orderId', [
                'orderId' => $orderId,
                'found' => $payment ? true : false
            ]);
        }

        // If payment still not found, redirect with error
        if (!$payment) {
            Log::error('Payment not found for trackId: ' . $trackId . ' or orderId: ' . $orderId);
            return redirect()->route('admin.payments.index')
                ->with('error', 'تراکنش یافت نشد.');
        }

        // If payment already verified, redirect with success
        if ($payment->status === 'verified') {
            return redirect()->route('admin.payments.index')
                ->with('success', 'پرداخت قبلا با موفقیت انجام و تایید شده است.');
        }

        // If callback indicates failure and payment is still pending, update status
        if (!$success && $payment->status === 'pending') {
            $payment->status = 'failed';
            $payment->save();
            
            Log::info('Payment marked as failed', ['payment_id' => $payment->id]);

            return redirect()->route('admin.payments.index')
                ->with('error', 'پرداخت ناموفق بود.');
        }

        // Verify payment with Zibal
        $verification = $this->zibal->verify($trackId);
        Log::info('Zibal verification response', ['verification' => $verification]);

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
            Log::info('Payment verified successfully', ['payment_id' => $payment->id]);

            // Update associated order if exists
            if ($payment->order_id) {
                try {
                    $order = \App\Models\Order::find($payment->order_id);
                    if ($order) {
                        $order->payment_status = 'Paid';
                        $order->save();
                        
                        Log::info('Order status updated to Paid', [
                            'order_id' => $order->id,
                            'payment_id' => $payment->id
                        ]);
                    } else {
                        // Try with the orderId from request
                        if ($orderId && $orderId != $payment->order_id) {
                            $order = \App\Models\Order::find($orderId);
                            if ($order) {
                                $order->payment_status = 'Paid';
                                $order->save();
                                
                                // Update payment with correct order_id
                                $payment->order_id = $orderId;
                                $payment->save();
                                
                                Log::info('Order status updated to Paid using request orderId', [
                                    'order_id' => $order->id,
                                    'payment_id' => $payment->id
                                ]);
                            }
                        }
                        
                        if (!$order) {
                            Log::warning('Order not found for payment', [
                                'payment_id' => $payment->id,
                                'order_id' => $payment->order_id,
                                'request_orderId' => $orderId
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to update order status', [
                        'payment_id' => $payment->id,
                        'order_id' => $payment->order_id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else if ($orderId) {
                // If payment doesn't have order_id but request has it
                try {
                    $order = \App\Models\Order::find($orderId);
                    if ($order) {
                        $order->payment_status = 'Paid';
                        $order->save();
                        
                        // Update payment with correct order_id
                        $payment->order_id = $orderId;
                        $payment->save();
                        
                        Log::info('Order status updated to Paid from request orderId', [
                            'order_id' => $order->id,
                            'payment_id' => $payment->id
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to update order status from request orderId', [
                        'payment_id' => $payment->id,
                        'request_orderId' => $orderId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Redirect to payment details with success message
            return redirect()->route('admin.payments.index')
                ->with('success', 'پرداخت با موفقیت انجام و تایید شد.');
        }

        // If verification failed, update status
        $payment->status = 'failed';
        $payment->save();
        Log::warning('Payment verification failed', ['payment_id' => $payment->id, 'verification' => $verification]);

        // Get error message from result code if available
        $errorMessage = isset($verification['result']) 
            ? $this->zibal->getResultMessage($verification['result']) 
            : 'خطا در تایید پرداخت. لطفا با پشتیبانی تماس بگیرید.';

        // Once verification is complete, check if this is a wallet deposit
        if ($payment && isset($payment->metadata) && is_array($payment->metadata) && isset($payment->metadata['type']) && $payment->metadata['type'] === 'wallet_deposit') {
            return redirect()->route('wallet.index')
                ->with('success', 'Your payment was successful. Amount added to your wallet.');
        }
        
        // For admin direct view of payment if needed
        return redirect()->route('admin.payments.index')
            ->with('error', $errorMessage);
    }
} 