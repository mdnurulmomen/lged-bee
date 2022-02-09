<?php

namespace App\Http\Controllers;

use App\Services\NotificationServices;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendMail(Request $request, NotificationServices $notificationServices): \Illuminate\Http\JsonResponse
    {
        try {
            \Validator::make($request->all(), [
                'notifiable_application' => 'required',
                'notifiable_type' => 'required',
                'meta_data' => 'required',
            ])->validate();

            $send_mail_response = responseFormat('error', 'Not Initialized!');

            if ($request->notifiable_application == 'rpu') {
                $send_mail_response = $notificationServices->sendMailToRpu($request);
            }

            if (isSuccess($send_mail_response, 'status', 'error')) {
                throw new \Exception($send_mail_response['message']);
            }
            return response()->json(responseFormat('status', 'Sent Mail to RPU'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }
}
