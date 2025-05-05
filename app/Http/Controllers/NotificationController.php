<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $notifications = Notification::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        return view('notifications.create');
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'category' => 'nullable|string|max:255',
            'audience_type' => 'required|string|in:all,role,specific,department',
            'audience_ids' => 'required_if:audience_type,role,specific,department|array',
            'delivery_methods' => 'required|array',
            'delivery_methods.*' => 'in:app,email,sms',
            'is_scheduled' => 'boolean',
            'scheduled_at' => 'required_if:is_scheduled,true|nullable|date|after:now',
            'attachments' => 'nullable|array',
            'action' => 'required|string|in:draft,send,preview'
        ]);

        $notification = new Notification();
        $notification->title = $validated['title'];
        $notification->message = $validated['message'];
        $notification->priority = $validated['priority'];
        $notification->category = $validated['category'] ?? null;
        $notification->audience_type = $validated['audience_type'];
        $notification->audience_ids = $validated['audience_ids'] ?? null;
        $notification->delivery_methods = $validated['delivery_methods'];
        $notification->is_scheduled = $validated['is_scheduled'] ?? false;
        $notification->scheduled_at = $validated['is_scheduled'] ? $validated['scheduled_at'] : null;
        $notification->attachments = $validated['attachments'] ?? null;
        $notification->created_by = auth()->id();
        
        // Handle different actions
        if ($validated['action'] === 'send') {
            $notification->is_draft = false;
            if (!$notification->is_scheduled) {
                $notification->sent_at = now();
            }
        } else {
            $notification->is_draft = true;
        }
        
        $notification->save();
        
        // Handle recipients immediately if sending now
        if ($validated['action'] === 'send' && !$notification->is_scheduled) {
            $this->processRecipients($notification);
        }
        
        if ($validated['action'] === 'preview') {
            return view('notifications.preview', compact('notification'));
        }
        
        return redirect()->route('notifications.index')
            ->with('success', $notification->is_draft 
                ? 'Notification saved as draft.' 
                : ($notification->is_scheduled 
                    ? 'Notification scheduled for delivery.' 
                    : 'Notification sent successfully.'));
    }

    /**
     * Display the specified notification.
     */
    public function show(Notification $notification)
    {
        $notification->load('creator', 'recipients');
        return view('notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified notification.
     */
    public function edit(Notification $notification)
    {
        $users = User::select('id', 'name', 'email')->get();
        return view('notifications.edit', compact('notification', 'users'));
    }

    /**
     * Update the specified notification.
     */
    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'category' => 'nullable|string|max:255',
            'audience_type' => 'required|in:all,specific,role',
            'audience_ids' => 'required_if:audience_type,specific,role|array',
            'delivery_methods' => 'required|array',
            'delivery_methods.*' => 'in:web,email,sms,push',
            'is_scheduled' => 'boolean',
            'scheduled_at' => 'required_if:is_scheduled,true|nullable|date',
            'is_draft' => 'boolean',
            'attachments' => 'nullable|array',
        ]);

        $notification->fill($request->all());
        
        // Handle scheduling
        if ($request->is_scheduled && $request->scheduled_at) {
            $notification->is_scheduled = true;
            $notification->scheduled_at = $request->scheduled_at;
        } else {
            $notification->is_scheduled = false;
            $notification->scheduled_at = null;
        }

        // Handle draft status
        if ($request->has('save_draft')) {
            $notification->is_draft = true;
        } else if ($notification->is_draft) {
            $notification->is_draft = false;
            $notification->sent_at = now();
        }

        $notification->save();

        // Process recipients if sending a draft
        if ($notification->wasChanged('is_draft') && !$notification->is_draft && !$notification->is_scheduled) {
            $this->processRecipients($notification);
        }

        return redirect()->route('notifications.index')
            ->with('success', $notification->is_draft ? 'Draft updated successfully!' : 'Notification updated and sent!');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully!');
    }

    /**
     * Preview a notification before sending.
     */
    public function preview(Request $request)
    {
        // This handles AJAX preview requests
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|string',
        ]);
        
        $notification = new Notification();
        $notification->title = $validated['title'];
        $notification->message = $validated['message'];
        $notification->priority = $validated['priority'];
        
        return view('notifications.partials.preview', compact('notification'));
    }

    /**
     * Process the recipients for a notification.
     */
    private function processRecipients(Notification $notification)
    {
        $recipientIds = [];
        
        switch ($notification->audience_type) {
            case 'all':
                $recipientIds = User::pluck('id')->toArray();
                break;
                
            case 'role':
                $recipientIds = User::whereIn('role', $notification->audience_ids)->pluck('id')->toArray();
                break;
                
            case 'specific':
                $recipientIds = $notification->audience_ids;
                break;
                
            case 'department':
                // Assuming users have a department_id
                $recipientIds = User::whereIn('department_id', $notification->audience_ids)->pluck('id')->toArray();
                break;
        }
        
        // Prepare pivot data with timestamps
        $pivotData = [];
        foreach ($recipientIds as $userId) {
            $pivotData[$userId] = [
                'read_at' => null,
                'dismissed_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        // Attach recipients with pivot data
        $notification->recipients()->attach($pivotData);
        
        // TODO: Queue jobs for email and SMS delivery if selected
    }
} 