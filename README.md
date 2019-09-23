
## Laravel package for SMS mailing service SemySMS

Simple package that integrates [SemySMS](http:/semysms.net) API into your Laravel 5.4+ app.

## Installation:

```bash
composer require allanvb/laravel-semysms
```

#### Laravel 5.5+

If you're using Laravel 5.5 or above, the package will automatically register provider and facade.

#### Laravel 5.4

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

* [Configuration](#configuration)
* [Usage](#usage)
* [Examples](#examples)
* [Exceptions](#exceptions)
* [Events](#events)
* [Extra](#extra)

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
SemySMS::send([
    'to' => '+1234567890',
    'text' => 'My first message.'
]);
```

Or

```php
app('semy-sms')->send([
    'to' => '+1234567890',
    'text' => 'My first message.'
]);
```
* All numbers must have international format.

All methods and events will return a [Collection](https://laravel.com/docs/collections), so you can use all available methods to manipulate response.
For example: `SemySMS::getInbox()->sortByDesc('date');`

## Examples

#### Sending simple message

```php
SemySMS::send([
    'to' => '+1234567890',
    'text' => 'Test message'
]);
```

You can use specific device by adding `'device_id' => '012345'` to array.

#### Sending multiple messages

```php
SemySMS::sendMultiple([
    'to' => ['+1234567890','+1567890234','+1902345678'],
    'text' => 'Test message'
]);
```

You can also use multiple devices by adding `'devices' => ['123456','234567','345678']` to array.

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
SemySMS::deleteOutbox();
```

Optional, you can use filtering for `getOutbox()`, `getInbox()`, `deleteOutbox()` and `deleteInbox()` methods by passing `interval`, `device_id` and `phone` to array

#### List of devices

```php
SemySMS::getDevices();
```

By default this method will return list of all devices connected to account.

As an option you can get only active devices by passing `'active'`, or `'archived'` for archived devices.

#### Cancel sending SMS

```php
SemySMS::cancelSMS();
```

This method will cancel all messages which was not sent to your devices.

You can request canceling for specific device by passing `device_id`, or specific message by adding `sms_id` to array.


## Events

The package have events build in. There are three events available for you to listen for. 

| Event                   | Fired                          | Parameter                                          |
| ----------------------- | ------------------------------ | -------------------------------------------------- |
| semy-sms.sent           | When message sent.             | 'To' and 'text' parameters of SMS that was sent.   |
| semy-sms.sent-multiple  | When multiple message sent.    | List of phones, devices and message that was sent. |
| semy-sms.received       | When new message income.       | DeviceID, Sender and Text of income message.       |

## Exceptions

The package can throw the following exceptions:

| Exception                    | Reason                                                                             |
| ---------------------------- | ---------------------------------------------------------------------------------- |
| *RequestException*           | When HTTP response will be different than 200.                                     |
| *SmsNotSentException*        | When something went wrong with the request to SemySMS servers.                     |
| *InvalidIntervalException*   | When you pass invalid Interval                                              |

## Extra

#### Receiving message from device

If you want to get incoming messages from your devices, you can use 

`https://yourdomain.com/semy-sms/receive` route in your [SemySMS](https://semysms.net) control panel.

To get this route working you need to change `catch_incoming` to `true` in your config file.

When you will get a new message, an `semy-sms.received` [Event](https://laravel.com/docs/events) will be fired.

#### Intervals

Interval class offers the following methods: `days()`, `weeks()`, `months()` and `years()`.

If you want to have more control on `Interval` you can pass a `startDate` and an `endDate` to the object.

```php
$startDate = Carbon::yesterday()->subDays(1);
$endDate = Carbon::yesterday();

Interval::create($startDate, $endDate);
```

## License

The MIT License (MIT). Please see [License File](LICENCE) for more information.
