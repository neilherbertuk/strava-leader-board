<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Notifications\LeaderChangedPush;
use App\Notifications\WebPushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class PushController extends Controller
{
    /**
     * Store the PushSubscription.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'endpoint'    => 'required',
            'keys.auth'   => 'required',
            'keys.p256dh' => 'required'
        ]);
        $endpoint = $request->endpoint;
        $token = $request->keys['auth'];
        $key = $request->keys['p256dh'];
        $user = $user = Guest::firstOrCreate([
            'endpoint' => $endpoint
        ]);
        $user->uuid = Str::uuid();
        $user->save();
        $user->updatePushSubscription($endpoint, $key, $token);
        return response()->json(['success' => true, 'uuid' => $user->uuid],200);
    }

    public function success($guest_id)
    {
        $guest = Guest::where('uuid', '=', $guest_id)->first();
        if (!empty($guest)) {
            Notification::send($guest, new WebPushNotification('Lockdown Challenge', 'Success! You\'ll receive updates via Push notifications'));
            $guest->uuid = Str::uuid();
            $guest->save();
        }
        return redirect()->back();
    }

}

