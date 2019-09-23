<?php

namespace Allanvb\LaravelSemysms\Controllers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReceiverController extends Controller
{
    /**
     * @param Request $request
     * @param Dispatcher $events
     */
    public function receiveSMS(Request $request, Dispatcher $events)
    {
        if (config('semy-sms.catch_incoming')) {

            $data['message_id'] = $request['id'];
            $data['device_id'] = $request['id_device'];
            $data['sender'] = $request['phone'];
            $data['text'] = $request['msg'];

            $data = collect($data);

            $events->dispatch('semy-sms.received', $data);
        }
    }
}
