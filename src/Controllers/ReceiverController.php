<?php

namespace Allanvb\LaravelSemysms\Controllers;

use Allanvb\LaravelSemysms\Traits\SmsEventDispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReceiverController extends Controller
{
    use SmsEventDispatcher;

    private const USSD_PATTERN = "/^\\*[0-9*]+#$/";


    /**
     * @param Request $request
     */
    public function receiveSMS(Request $request)
    {
        if (config('semy-sms.catch_incoming')) {

            $data['message_id'] = $request['id'];
            $data['device_id'] = $request['id_device'];
            $data['sender'] = $request['phone'];
            $data['text'] = $request['msg'];

            $data = collect($data);

            if (preg_match(self::USSD_PATTERN, $request['phone'])) {
                $this->dispatch('semy-sms.ussd-response', $data);
            } else {
                $this->dispatch('semy-sms.received', $data);
            }
        }
    }
}
