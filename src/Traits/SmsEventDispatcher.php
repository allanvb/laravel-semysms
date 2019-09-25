<?php


namespace Allanvb\LaravelSemysms\Traits;


use Illuminate\Contracts\Events\Dispatcher;

trait SmsEventDispatcher
{
    public function dispatch($eventName, $eventData) : void
    {
        switch (app()->version()) {
            case (app()->version() > '5.1' && app()->version() < '5.8'):
               app('events')->fire($eventName, $eventData);
                break;
            case (app()->version() >= '5.8'):
                app('events')->dispatch($eventName, $eventData);
                break;
        }
    }
}
