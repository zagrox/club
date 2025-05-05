<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run the UserSeeder first.');
            return;
        }
        
        // Sample payment methods
        $paymentMethods = ['Mastercard', 'Visa', 'PayPal', 'Bank Transfer'];
        
        // Sample payment statuses
        $paymentStatuses = ['Pending', 'Paid', 'Failed', 'Refunded', 'Cancelled'];
        
        // Sample delivery statuses
        $deliveryStatuses = [
            'Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 
            'Out for Delivery', 'Ready to Pickup', 'Dispatched'
        ];
        
        // Create 20 sample orders
        for ($i = 1; $i <= 20; $i++) {
            $subtotal = rand(100, 2000);
            $tax = round($subtotal * 0.1, 2);
            $discount = rand(0, 50);
            $total = $subtotal + $tax - $discount;
            
            $randomUser = $users->random();
            $orderDate = now()->subDays(rand(1, 60));
            
            Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => $randomUser->id,
                'subtotal_amount' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'delivery_status' => $deliveryStatuses[array_rand($deliveryStatuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }
        
        $this->command->info('Sample orders created successfully.');
    }
} 