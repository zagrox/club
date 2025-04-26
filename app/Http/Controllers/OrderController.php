<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function list()
    {
        // Get order statistics
        $pendingCount = Order::where('payment_status', 'Pending')->count();
        $completedCount = Order::where('delivery_status', 'Delivered')->count();
        $refundedCount = Order::where('payment_status', 'Refunded')->count();
        $failedCount = Order::where('payment_status', 'Failed')->count();
        
        // Get all orders with their users
        $orders = Order::with('user')
            ->orderByDesc('created_at')
            ->paginate(10);
        
        return view('pages.orders.list', compact(
            'orders', 
            'pendingCount', 
            'completedCount', 
            'refundedCount', 
            'failedCount'
        ));
    }
    
    /**
     * Display the specified order details.
     */
    public function details($id)
    {
        $order = Order::with('user')->findOrFail($id);
        
        // In a real application, you would also fetch order items and other related data
        // $orderItems = $order->items()->get();
        
        return view('pages.orders.details', compact('order'));
    }
    
    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'sometimes|required|string|in:Pending,Paid,Failed,Refunded,Cancelled',
            'delivery_status' => 'sometimes|required|string|in:Pending,Processing,Shipped,Delivered,Cancelled,Out for Delivery,Ready to Pickup,Dispatched',
        ]);
        
        $order = Order::findOrFail($id);
        
        if ($request->has('payment_status')) {
            $order->payment_status = $request->payment_status;
        }
        
        if ($request->has('delivery_status')) {
            $order->delivery_status = $request->delivery_status;
        }
        
        $order->save();
        
        return redirect()->back()->with('success', 'Order status updated successfully');
    }
    
    /**
     * Store a new order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'subtotal_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        // Generate a unique order number
        $orderNumber = 'ORD-' . strtoupper(uniqid());
        
        $order = new Order();
        $order->order_number = $orderNumber;
        $order->user_id = $request->user_id;
        $order->total_amount = $request->total_amount;
        $order->tax_amount = $request->tax_amount;
        $order->discount_amount = $request->discount_amount;
        $order->subtotal_amount = $request->subtotal_amount;
        $order->payment_method = $request->payment_method;
        $order->notes = $request->notes;
        $order->payment_status = 'Pending';
        $order->delivery_status = 'Pending';
        $order->save();
        
        return redirect()->route('orders.details', $order->id)
            ->with('success', 'Order created successfully');
    }
    
    /**
     * Delete an order.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        
        return redirect()->route('orders.list')
            ->with('success', 'Order deleted successfully');
    }
} 