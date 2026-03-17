<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * all notifications
     */
    public function index()
    {
        // get all notifications for delivery person (auth()->user())
        $notifications = auth()->user()->notifications;

        return view('delivery-person.notifications.index', compact('notifications'));
    }

    /**
     * to mark all as read
     */
    public function markRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
}
