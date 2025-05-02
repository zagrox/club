# Zibal Payment Gateway Integration

This document describes how to use the Zibal payment gateway integration in your application.

## Configuration

1. Create a Zibal merchant account at [zibal.ir](https://zibal.ir)
2. Get your merchant ID from the Zibal dashboard
3. Add the following environment variables to your `.env` file:

```
ZIBAL_MERCHANT=your-merchant-id-here
ZIBAL_CALLBACK_URL=/payments/callback
ZIBAL_DESCRIPTION_PREFIX="Payment for order: "
ZIBAL_SANDBOX=true  # Set to false in production
ZIBAL_LOG_ENABLED=true
ZIBAL_LOG_CHANNEL=daily
```

## Mock Mode for Development

During development, you can use the mock implementation of the Zibal payment gateway, which doesn't require a real Zibal merchant account. There are several ways to enable mock mode:

1. **Leave the default merchant ID**: The default `zibal-test-merchant-id` will automatically enable mock mode
2. **Set the ZIBAL_MOCK environment variable**: Add `ZIBAL_MOCK=true` to your `.env` file
3. **Set the config directly**: In `config/zibal.php`, set `'mock' => true,`

In mock mode:
- All payment requests will succeed
- You'll be redirected to a simulated payment page
- You can choose to simulate successful or failed payments
- All verification requests will succeed

### Testing with Mock Mode

The command to test Zibal integration works with mock mode:

```bash
php artisan zibal:test [user_id] [amount]
```

This will create a test payment record and output the mock payment URL.

## Usage

### Basic Payment Process

```php
use App\Facades\Zibal;
use App\Models\Payment;

// Create a payment record
$payment = new Payment([
    'user_id' => auth()->id(),
    'amount' => 10000, // Amount in Rials
    'gateway' => 'zibal',
    'status' => 'pending',
    'description' => 'Payment for order #123',
    'metadata' => ['order_id' => 123],
]);
$payment->save();

// Request payment from Zibal
$response = Zibal::request(
    $payment->amount,
    route('payments.callback'), // Callback URL
    $payment->id, // Order ID
    $payment->description,
    ['payment_id' => $payment->id]
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

return redirect()->back()->with('error', 'Error creating payment transaction');
```

### Handling Callbacks

In your callback controller method:

```php
use App\Models\Payment;
use App\Facades\Zibal;

public function callback(Request $request)
{
    // Get track ID from request
    $trackId = $request->input('trackId');
    $success = $request->input('success') == 1;
    
    // Find payment by track ID
    $payment = Payment::where('track_id', $trackId)->first();
    
    // If payment not found, redirect with error
    if (!$payment) {
        return redirect()->route('payments.index')
            ->with('error', 'Payment transaction not found.');
    }
    
    // If payment already verified, redirect with success
    if ($payment->status === 'verified') {
        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Payment was already successfully processed.');
    }
    
    // If callback indicates failure and payment is still pending, update status
    if (!$success && $payment->status === 'pending') {
        $payment->status = 'failed';
        $payment->save();
        
        return redirect()->route('payments.index')
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
        
        // Process the successful payment
        // E.g., update order status, add credits to user account, etc.
        
        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Payment was successful.');
    }
    
    // If verification failed, update status
    $payment->status = 'failed';
    $payment->save();
    
    // Get error message from result code if available
    $errorMessage = isset($verification['result']) 
        ? Zibal::getResultMessage($verification['result']) 
        : 'Error verifying payment. Please contact support.';
    
    return redirect()->route('payments.index')
        ->with('error', $errorMessage);
}
```

## Moving to Production

When you're ready to move to production:

1. Replace the test merchant ID with your real Zibal merchant ID
2. Set `ZIBAL_SANDBOX=false` in your `.env` file
3. Set `ZIBAL_MOCK=false` in your `.env` file
4. Make sure your callback URLs are properly configured and accessible

## Common Error Codes

- 102: Merchant ID not found
- 103: Merchant is inactive
- 104: Invalid merchant
- 105: Amount must be greater than 1,000 Rials
- 106: Invalid callback URL (must start with http or https)
- 201: Payment already verified
- 202: Transaction not found
- 203: Invalid track ID

For a full list of error codes, see the Zibal API documentation. 