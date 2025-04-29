<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class NotificationCenterController extends Controller
{
    /**
     * Display the notification center dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.notification-center.index');
    }

    /**
     * Display archived notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function archived()
    {
        return view('pages.notification-center.archived');
    }

    /**
     * Display notification settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        return view('pages.notification-center.settings');
    }

    /**
     * Show the form for creating a new notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $roles = ['admin', 'moderator', 'user', 'subscriber']; // Get these from your system
        
        return view('pages.notification-center.create', compact('users', 'roles'));
    }

    /**
     * Store a newly created notification in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:high,medium,low',
            'category' => 'required|in:system,update,reminder,custom',
            'audience' => 'required|in:all,roles,users',
            'recipients' => 'required_if:audience,roles,users|array',
            'delivery_methods' => 'required|array',
            'delivery_methods.*' => 'in:push,email,sms,web',
            'send_now' => 'required|boolean',
            'scheduled_at' => 'required_if:send_now,0|nullable|date|after:now',
            'expires_at' => 'nullable|date|after:scheduled_at',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('notification-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ];
            }
        }

        // Determine status based on send_now flag
        $status = $request->send_now ? 'sent' : 'scheduled';
        
        // Create notification
        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => $status,
            'audience' => $request->audience,
            'recipients' => $request->recipients,
            'delivery_methods' => $request->delivery_methods,
            'scheduled_at' => $request->send_now ? now() : $request->scheduled_at,
            'expires_at' => $request->expires_at,
            'attachments' => $attachments,
        ]);

        // If send_now is true, we would dispatch the notification here
        // This implementation depends on your notification system

        return redirect()->route('notification-center.index')
            ->with('success', 'Notification ' . ($status === 'sent' ? 'sent' : 'scheduled') . ' successfully.');
    }

    /**
     * Store a notification as draft.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDraft(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('notification-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ];
            }
        }

        // Create draft notification
        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'priority' => $request->priority ?? 'medium',
            'category' => $request->category ?? 'system',
            'status' => 'draft',
            'audience' => $request->audience ?? 'all',
            'recipients' => $request->recipients,
            'delivery_methods' => $request->delivery_methods,
            'scheduled_at' => null,
            'expires_at' => $request->expires_at,
            'attachments' => $attachments,
        ]);

        return redirect()->route('notification-center.index')
            ->with('success', 'Notification saved as draft.');
    }

    /**
     * Mark notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $notification = Notification::findOrFail($request->notification_id);
        $notification->markAsRead(Auth::id());

        return response()->json(['success' => true]);
    }

    /**
     * Dismiss notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dismiss(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $notification = Notification::findOrFail($request->notification_id);
        $notification->markAsDismissed(Auth::id());

        return response()->json(['success' => true]);
    }

    /**
     * Archive notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $notification = Notification::findOrFail($request->notification_id);
        $notification->update(['status' => 'archived']);

        return response()->json(['success' => true]);
    }
    
    /**
     * Preview notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:high,medium,low',
            'category' => 'required|in:system,update,reminder,custom',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $notification = new Notification([
            'title' => $request->title,
            'message' => $request->message,
            'priority' => $request->priority,
            'category' => $request->category,
        ]);

        return response()->json([
            'success' => true,
            'preview' => view('pages.notification-center.preview', compact('notification'))->render()
        ]);
    }
} 