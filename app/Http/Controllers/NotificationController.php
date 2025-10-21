<?php

namespace App\Http\Controllers;

use App\Mail\GeneralNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function sendGeneralNotification(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Mail::to($request->recipient_email)
            ->send(new GeneralNotificationMail(
                $request->subject, 
                $request->body
            )
        );

        return redirect()->back()->with(
            'success', 
            'Notification email sent successfully.'
        );
    }
}
