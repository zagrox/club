<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display the user's notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get both Laravel's database notifications and custom notifications
        $dbNotifications = $user->notifications()->paginate(5);
        
        // Get wallet notifications
        $walletNotifications = $user->walletNotifications()->paginate(5);
        
        return view('inbox.index', compact('dbNotifications', 'walletNotifications'));
    }
    
    /**
     * Mark a notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        
        // Check notification type from request
        $type = $request->input('type', 'standard');
        
        if ($type === 'wallet') {
            // Mark wallet notification as read
            $notification = $user->walletNotifications()->findOrFail($request->id);
        } else {
            // Mark standard notification as read
            $notification = $user->notifications()->findOrFail($request->id);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Mark all standard notifications as read
        $user->unreadNotifications->markAsRead();
        
        // Mark all wallet notifications as read
        $user->walletNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        // Check if it's an AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تمام اعلان‌ها خوانده شدند.'
            ]);
        }
        
        return redirect()->back()->with('success', 'تمام اعلان‌ها خوانده شدند.');
    }
    
    /**
     * Mark a notification as read and redirect to its URL.
     *
     * @param  string  $id
     * @param  string  $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function readAndRedirect($id, $type = 'standard')
    {
        $user = Auth::user();
        
        if ($type === 'wallet') {
            // Handle wallet notification
            $notification = $user->walletNotifications()->findOrFail($id);
        } else {
            // Handle standard notification
            $notification = $user->notifications()->findOrFail($id);
        }
        
        $notification->markAsRead();
        
        $data = $notification->data;
        $url = $data['url'] ?? '/';
        
        return redirect($url);
    }
}
