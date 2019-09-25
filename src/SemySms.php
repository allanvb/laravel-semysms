<?php


namespace Allanvb\LaravelSemysms;


use Allanvb\LaravelSemysms\Exceptions\RequestException;
use Allanvb\LaravelSemysms\Exceptions\SmsNotSentException;
use Allanvb\LaravelSemysms\Rules\IntervalRule;
use Allanvb\LaravelSemysms\Traits\SmsEventDispatcher;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

abstract class SemySms
{
    use SmsEventDispatcher;

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
     * @var Client
     */
    protected $client;

    /**
     * SemySms constructor.
     * @param Dispatcher $events
     */
    public function __construct(Client $client)
    {
        $this->token = config('semy-sms.token');
        $this->device_id = config('semy-sms.device_id');
        $this->client = $client;
    }

    /**
     * @param array|null $data
     * @param string $requestUrl
     * @return \Illuminate\Support\Collection|\Illuminate\Support\MessageBag
     * @throws RequestException
     * @throws SmsNotSentException
     * @throws \Allanvb\LaravelSemysms\Exceptions\InvalidIntervalException
     */
    protected function createListRequest(array $data = null, string $requestUrl)
    {
        if (isset($data)) {
            $validator = Validator::make($data, [
                'interval' => new IntervalRule(),
                'device_id' => 'numeric|digits_between:1,10',
                'start_id' => 'numeric|required_with:end_id',
                'end_id' => 'numeric|required_with:start_id',
                'list_id' => 'array',
                'phone' => 'max:40'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors());
            }
        }

        $postData = [
            'token' => $this->token
        ];

        $postData['device'] = $data['device_id'] ?? $this->device_id;

        if (isset($data['interval'])) {
            $postData['date_start'] = $data['interval']->startDate;
            $postData['date_end'] = $data['interval']->endDate;
        }

        if (isset($data['start_id'])) {
            $postData['start_id'] = $data['start_id'];
            $postData['end_id'] = $data['end_id'];
        }

        if (isset($data['phone'])) {
            $postData['phone'] = $data['phone'];
        }

        if (isset($data['list_id'])) {
            $postData['list_id'] = implode(',', $data['list_id']);
        }

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
        $content = $multiple ? ['body' => json_encode($postData)] : ['form_params' => $postData];

        $request = $this->client->request('POST', $requestUrl, $content);

        $response = [
            'statusCode' => $request->getStatusCode(),
            'body' => $request->getBody()->getContents()
        ];

        return $response;
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
     * @param array $data
     * @return mixed
     */
    protected abstract function ussd(array $data);

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
     * @param array|null $data
     * @return mixed
     */
    protected abstract function getDevices(array $data = null);

    /**
     * @param array|null $data
     * @return mixed
     */
    protected abstract function cancelSMS(array $data = null);

}
