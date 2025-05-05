<?php

namespace App\Console\Commands;

use App\Facades\Zibal;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;

class TestZibalPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zibal:test {user_id?} {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Zibal payment gateway integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id') ?? 1;
        $amount = $this->argument('amount') ?? 10000;
        
        // Check if user exists
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }
        
        $this->info("Testing Zibal payment for user: {$user->name} (ID: {$user->id})");
        $this->info("Amount: {$amount} Rials");
        
        // Create a test payment
        $payment = new Payment([
            'user_id' => $user->id,
            'amount' => $amount,
            'gateway' => 'zibal',
            'status' => 'pending',
            'description' => 'Test payment',
            'metadata' => ['test' => true, 'timestamp' => now()->timestamp],
        ]);
        $payment->save();
        
        $this->info("Created test payment record with ID: {$payment->id}");
        
        // Call Zibal API
        $callbackUrl = url('/payments/callback');
        $this->info("Callback URL: {$callbackUrl}");
        
        $response = Zibal::request(
            $payment->amount,
            $callbackUrl,
            $payment->id,
            $payment->description,
            ['payment_id' => $payment->id, 'test' => true]
        );
        
        if ($response && isset($response['trackId'])) {
            $payment->track_id = $response['trackId'];
            $payment->save();
            
            $this->info("Zibal request successful!");
            $this->table(
                ['Key', 'Value'],
                collect($response)->map(function ($value, $key) {
                    return [$key, is_array($value) ? json_encode($value) : $value];
                })->toArray()
            );
            
            $paymentUrl = Zibal::getPaymentUrl($response['trackId']);
            $this->info("Payment URL: {$paymentUrl}");
            
            return 0;
        }
        
        $this->error("Zibal request failed!");
        $payment->status = 'failed';
        $payment->save();
        
        return 1;
    }
} 