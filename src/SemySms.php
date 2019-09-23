<?php


namespace Allanvb\LaravelSemysms\Helpers;


use Allanvb\LaravelSemysms\Exceptions\RequestException;
use Allanvb\LaravelSemysms\Exceptions\SmsNotSentException;
use Allanvb\LaravelSemysms\Rules\IntervalRule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Validator;

abstract class SemySms
{
    protected const SEND_URL = "https://semysms.net/api/3/sms.php";

    protected const SEND_MULTIPLE_URL = "https://semysms.net/api/3/sms_more.php";

    protected const GET_OUTBOX_LIST_URL = "https://semysms.net/api/3/outbox_sms.php";

    protected const DELETE_OUTBOX_LIST_URL = "https://semysms.net/api/3/del_outbox_sms.php";

    protected const GET_INBOX_LIST_URL = "https://semysms.net/api/3/inbox_sms.php";

    protected const DELETE_INBOX_LIST_URL = "https://semysms.net/api/3/del_inbox_sms.php";

    protected const GET_DEVICES_LIST_URL = "https://semysms.net/api/3/devices.php";

    protected const CANCEL_SMS_URL = "https://semysms.net/api/3/cancel_sms.php";

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $device_id;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $token;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * SemySms constructor.
     * @param Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->token = config('semy-sms.token');
        $this->device_id = config('semy-sms.device_id');
        $this->events = $events;
    }

    /**
     * @param array|null $data
     * @param string $requestUrl
     * @return \Illuminate\Support\Collection|\Illuminate\Support\MessageBag
     * @throws RequestException
     * @throws SmsNotSentException
     * @throws \Allanvb\LaravelSemysms\Exceptions\InvalidIntervalException
     */
    protected function createListRequest(array $data = null, string $requestUrl) {
        if (isset($data)) {
            $validator = Validator::make($data, [
                'interval' => new IntervalRule(),
                'device_id' => 'numeric|digits_between:1,10',
                'phone' => 'max:30'
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }
        }

        $period = $data['interval'] ?? Interval::weeks(1);
        $deviceID = $data['device_id'] ?? $this->device_id;
        $phone = $data['phone'] ?? null;

        $postData = [
            'token' => $this->token,
            'device' => $deviceID,
            'phone' => $phone,
            'date_start' => $period->startDate,
            'date_end' => $period->endDate
        ];

        $request = $this->performRequest($postData, $requestUrl);

        $this->validateRequest($request);

        $response = collect(json_decode($request['body'], true)['data']);

        return $response;
    }

    /**
     * @param array $postData
     * @param string $requestUrl
     * @param bool $multiple
     * @return array
     */
    protected function performRequest(array $postData, string $requestUrl, bool $multiple = false): array
    {
        $postData = $multiple ? json_encode($postData) : $postData;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        if ($multiple) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($postData)]);
        }

        $output = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $request = [
            'statusCode' => $httpCode,
            'body' => $output
        ];

        return $request;
    }

    /**
     * @param array $request
     * @throws RequestException
     * @throws SmsNotSentException
     */
    protected function validateRequest(array $request): void
    {
        if ($request['statusCode'] != 200) {
            throw RequestException::create($request['statusCode']);
        }

        $response = json_decode($request['body']);

        if ($response->code != '0') {
            throw SmsNotSentException::create($response->code, $response->error);
        }
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected abstract function send(array $data);

    /**
     * @param array $data
     * @return mixed
     */
    protected abstract function sendMultiple(array $data);

    /**
     * @param array|null $data
     * @return mixed
     */
    protected abstract function getOutbox(array $data = null);

    /**
     * @param array|null $data
     * @return mixed
     */
    protected abstract function deleteOutbox(array $data = null);

    /**
     * @param array|null $data
     * @return mixed
     */
    protected abstract function getInbox(array $data = null);

    /**
     * @param array|null $data
     * @return mixed
     */
    protected abstract function deleteInbox(array $data = null);

    /**
     * @param string|null $devices
     * @return mixed
     */
    protected abstract function getDevices(string $devices = null);

    /**
     * @param array|null $data
     * @return mixed
     */
    protected abstract function cancelSMS(array $data = null);

}
