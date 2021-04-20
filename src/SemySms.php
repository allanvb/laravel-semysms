<?php


namespace Allanvb\LaravelSemysms;


use Allanvb\LaravelSemysms\Exceptions\RequestException;
use Allanvb\LaravelSemysms\Exceptions\SmsNotSentException;
use Allanvb\LaravelSemysms\Traits\SmsEventDispatcher;
use Allanvb\LaravelSemysms\Validators\ListRequestValidation;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

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
     * @var Repository|mixed
     */
    protected $device_id;

    /**
     * @var Repository|mixed
     */
    protected $token;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $recipients;

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
     * @return Collection|MessageBag
     * @throws RequestException
     * @throws SmsNotSentException
     * @throws InvalidIntervalException
     */
    protected function createListRequest(string $requestUrl, array $data = null)
    {
        if (isset($data)) {
            $validator = ListRequestValidation::validate($data);

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
        $this->validateResponse($request);

        return collect(
            json_decode($request['body'], true)['data']
        );
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

        return [
            'statusCode' => $request->getStatusCode(),
            'body' => $request->getBody()->getContents()
        ];
    }

    /**
     * @param array $request
     * @throws RequestException
     * @throws SmsNotSentException
     * @return void
     */
    protected function validateResponse(array $request): void
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
     * @return Collection
     */
    protected abstract function sendOne(array $data);

    /**
     * @param array $data
     * @return Collection
     */
    protected abstract function sendMultiple(array $data);

    /**
     * @return object
     */
    protected abstract function multiple();

    /**
     * @param array $data
     * @return object
     */
    protected abstract function addRecipient(array $data);

    /**
     * @return Collection
     */
    protected abstract function send();

    /**
     * @param array $data
     * @return Collection
     */
    protected abstract function ussd(array $data);

    /**
     * @param array|null $data
     * @return Collection
     */
    protected abstract function getOutbox(array $data = null);

    /**
     * @param array|null $data
     * @return Collection
     */
    protected abstract function deleteOutbox(array $data = null);

    /**
     * @param array|null $data
     * @return Collection
     */
    protected abstract function getInbox(array $data = null);

    /**
     * @param array|null $data
     * @return Collection
     */
    protected abstract function deleteInbox(array $data = null);

    /**
     * @param array|null $data
     * @return Collection
     */
    protected abstract function getDevices(array $data = null);

    /**
     * @param array|null $data
     * @return Collection
     */
    protected abstract function cancelSMS(array $data = null);

}
