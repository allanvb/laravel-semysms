<h2 align="center">
    Laravel package for SMS mailing service SemySMS
</h2>

<p align="center">
    <a href="https://packagist.org/packages/allanvb/laravel-semysms"><img src="https://img.shields.io/packagist/v/allanvb/laravel-semysms?color=orange&style=flat-square" alt="Packagist Version"></a>
    <a href="https://packagist.org/packages/allanvb/laravel-semysms"><img src="https://img.shields.io/github/last-commit/allanvb/laravel-semysms?color=blue&style=flat-square" alt="GitHub last commit"></a>
    <a href="https://packagist.org/packages/allanvb/laravel-semysms"><img src="https://img.shields.io/packagist/l/allanvb/laravel-semysms?color=brightgreen&style=flat-square" alt="License"></a>
    <a href="https://sonarcloud.io/dashboard/index/allanvb_laravel-semysms"><img src="https://sonarcloud.io/api/project_badges/measure?project=allanvb_laravel-semysms&metric=alert_status" alt="Sonar"/></a>
</p>

Package that integrates [SemySMS](http:/semysms.net) API into your Laravel 5 app.

## Installation:

```bash
composer require allanvb/laravel-semysms
```

#### Laravel 5.5+

If you're using Laravel 5.5 or above, the package will automatically register provider and facade.

#### Laravel 5.4 and below

Add `Allanvb\LaravelSemysms\SemySmsServiceProvider` to the `providers` array in your `config/app.php`:

```php
'providers' => [
    // Other service providers...

    Allanvb\LaravelSemysms\SemySmsServiceProvider::class,
],
```

Add an alias in your `config/app.php`:

```php
'aliases' => [
    ...
    'SemySMS' => Allanvb\LaravelSemysms\Facades\SemySMS::class,
],
```

Or you can `use` the facade class when needed:

```php
use Allanvb\Semysms\Facades\SemySMS;
```

## Overview

Look at one of the following topics to learn more about SemySMS package.

- [Configuration](#configuration)
- [Usage](#usage)
- [Examples](#examples)
- [Events](#events)
- [Notification channel](#notification-channel)
- [Exceptions](#exceptions)
- [Extra](#extra)

## Configuration

You can use `php artisan vendor:publish` to copy the configuration file to your app's config directory:

```sh
$ php artisan vendor:publish --provider="Allanvb\LaravelSemysms\SemySmsServiceProvider" --tag="config"
```

Then update `config/semy-sms.php` with your credentials. Also you can update your `.env` file with the following:

```dotenv
SEMYSMS_TOKEN=your_access_token
SEMYSMS_DEVICE_ID=default_device_id
```
All methods uses [Validator](https://laravel.com/docs/validation) to validate passed data. 

By default, methods will try to use for all requests the device you specified in your `SEMYSMS_DEVICE_ID` variable.

## Usage:

To use the SemySMS Library you can access the facade, or request the instance from the service container:

```php
SemySMS::sendOne([
    'to' => '+1234567890',
    'text' => 'My first message.'
]);
```

Or

```php
app('semy-sms')->sendOne([
    'to' => '+1234567890',
    'text' => 'My first message.'
]);
```
- All numbers must have international format.

All methods and events will return a [Collection](https://laravel.com/docs/collections), so you can use all available methods to manipulate response.
For example: `SemySMS::getInbox()->sortByDesc('date');`

## Examples

#### Sending simple message

```php
SemySMS::sendOne([
    'to' => '+1234567890',
    'text' => 'Test message'
]);
```

Available parameters:
- `to` - (string) Phone number in international format **(required)**.
- `text` - (string) SMS Text, max 255 symbols **(required)**.
- `device_id` - (string) Device ID or *active*.

`device_id` parameter can also take the `active` value which means that the service will distribute new SMS between all your active devices.

#### Sending multiple messages

```php
SemySMS::sendMultiple([
    'to' => ['+1234567890','+1567890234','+1902345678'],
    'text' => 'Test message'
]);
```

Available parameters:
- `to` - (array) List of phones in international format **(required)**.
- `text` - (string) SMS Text, max 255 symbols **(required)**.

In case you want to have more control on sending multiple messages you can use chaining methods.

```php
$messages = SemySMS::multiple();

$messages->addRecipient([
    'to' => '+1234567890',
    'text' => 'Test message',
]);

$messages->addRecipient([
    'to' => '+1567890234',
    'text' => 'Test message 2',
]);

$messages->send();
```
Available parameters:
- `to` - (string) Phone number in international format **(required)**.
- `text` - (string) SMS Text, max 255 symbols **(required)**.
- `device_id` - (int) Device ID.
- `my_id` - (string) SMS code from your accounting system


#### Sending USSD requests
- This feature works only on Android 8.0+

```php
SemySMS::ussd([
    'to' => '*123#'
]);
```

You can use `'device_id'` parameter to perform USSD request from specific device.

#### List of outgoing SMS

```php
SemySMS::getOutbox();
```

Optional, you can specify a interval of time you want to get by using `Interval` helper like this.

```php
SemySMS::getOutbox([
    'interval' => Interval::days(3)
]);
```

#### List of incoming SMS

```php
SemySMS::getInbox();
```

#### Delete outgoing SMS
```php
SemySMS::deleteOutbox();
```

#### Delete incoming SMS
```php
SemySMS::deleteInbox();
```

`deleteOutbox()` and `deleteInbox()` methods will return deleted messages.

Optional, you can use filter for `getOutbox()`, `getInbox()`, `deleteOutbox()` and `deleteInbox()` methods.

Available parameters:
- `interval` - (Interval) Interval of time.
- `device_id` - (int) Device ID.
- `start_id` - (int) Start ID of list filter.
- `end_id` - (int) End ID of list filter.
- `list_id` - (array) List of SMS codes.
- `phone` - (string) Phone number.


#### List of devices

```php
SemySMS::getDevices();
```

By default this method will return list of all devices connected to account.

Available parameters:
- `status` - (string) active|archived.
- `list_id` - (array) List of devices.

#### Cancel sending SMS

```php
SemySMS::cancelSMS();
```

This method will cancel all SMS which was not sent to your default device.

You can request canceling of all SMS for specific device by passing `device_id`, or specific message by passing `sms_id` to array.


## Events

The package have events built in. There are three events available for you to listen for. 

| Event                   | Fired                          | Parameter                                          |
| ----------------------- | ------------------------------ | -------------------------------------------------- |
| semy-sms.sent           | When message sent.             | 'To' and 'text' parameters of SMS that was sent.   |
| semy-sms.sent-multiple  | When multiple message sent.    | List of phones, devices and message that was sent. |
| semy-sms.received       | When new message income.       | DeviceID, Sender and Text of income message.       |
| semy-sms.ussd-response  | When ussd response income.     | DeviceID, Sender and Text of income message.       |


## Notification channel

This package also provide notification channel for `SemySMS`.

To use notification channel you must use `SemySmsChannel::class` in `via` method of your notification class.
After that you will be able to use `toSemySms()` method for sending messages. 

See example below.

```php
use Illuminate\Notifications\Notification;
use Allanvb\LaravelSemysms\Channels\SemySmsChannel;
use Allanvb\LaravelSemysms\Channels\SemySmsMessage;

class MyNotification extends Notification
{
    public function via($notifiable)
    {
        return [SemySmsChannel::class];
    }

    public function toSemySms($notifiable)
    {
        return (new SemySmsMessage)
            ->text('My first notification message.');
    }

}

```

You can add recipient in two ways. 
- First is by using `routeNotificationForSemySMS()` in your notifiable model as below.

```php
// User model

public function routeNotificationForSemySMS()
{
    return $this->phone;
}
```
- Second way is to use `to()` method inside your notification.

```php
public function toSemySms($notifiable)
{
    return (new SemySmsMessage)
            ->to('+1234567890')
            ->text('My second notification message.');
}
```

If you'll use both, then `to()` method will be used as primary.


## Exceptions

The package can throw the following exceptions:

| Exception                    | Reason                                                                             |
| ---------------------------- | ---------------------------------------------------------------------------------- |
| *SemySmsValidationException* | When method params don't pass validation.                                          |
| *RequestException*           | When HTTP response will be different than 200.                                     |
| *SmsNotSentException*        | When something went wrong with the request to SemySMS servers.                     |
| *InvalidIntervalException*   | When you pass invalid Interval                                                     |

## Extra

#### Receiving message from devices

If you want to get incoming messages from your devices, you can use 

`https://yourdomain.com/semy-sms/receive` route in your [SemySMS](https://semysms.net) control panel.

To get this route working you need make some actions:
- Change `catch_incoming` to `true` in your config file.
- Add `semy-sms/receive` route to your `$except` variable in `VerifyCsrfToken` middleware.

After that, you will be able to listen for `semy-sms.received` Event.

If you performed an ussd request, you can listen for `semy-sms.ussd-response` Event to process USSD response.

In case that you get USSD response as SMS, you can add sender name to `ussd_senders` in your config file.

You can get more information about Events in [Laravel official documentation](https://laravel.com/docs/events)

#### Intervals

Interval class offers the following methods: `hours()`, `days()`, `weeks()`, `months()` and `years()`.

If you want to have more control on `Interval` you can pass a `startDate` and an `endDate` to the object.

```php
$startDate = Carbon::yesterday()->subDays(1);
$endDate = Carbon::yesterday();

Interval::create($startDate, $endDate);
```

## License

The MIT License (MIT). Please see [License File](LICENCE) for more information.
