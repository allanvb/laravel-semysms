<?php

namespace Allanvb\LaravelSemysms\Controllers;

use Allanvb\LaravelSemysms\Traits\SmsEventDispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReceiverController extends Controller
{
    use SmsEventDispatcher;

    /**
     * @param Request $request
     * @param Dispatcher $events
     */
    public function receiveSMS(Request $request)
    {
        if (config('semy-sms.catch_incoming')) {

            $data['message_id'] = $request['id'];
            $data['device_id'] = $request['id_device'];
            $data['sender'] = $request['phone'];
            $data['text'] = $request['msg'];

            $data = collect($data);

            $this->dispatch('semy-sms.received', $data);
        }
    }
}
