<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        // log the action.
        logger()->info("[app\Http\Controllers\Vendor\NotificationController@index] Seeing All notifications incoming");

        return view('vendor.notifications.index');
    }

    public function markAllRead()
    {
        // log the action.
        logger()->info("[app\Http\Controllers\Vendor\NotificationController@markAllRead] Currently marking all notifications as read");
        auth()->user()->unreadNotifications->markAsRead();

        // log the status
        logger()->info('Notifications mark as read!');

        return back()->with('success', 'All notifications cleared!');
    }
}
