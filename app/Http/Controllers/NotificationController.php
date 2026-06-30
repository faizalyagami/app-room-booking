<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return back();
    }
}
