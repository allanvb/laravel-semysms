<?php


namespace Allanvb\LaravelSemysms\Traits;


use Illuminate\Contracts\Events\Dispatcher;

trait SmsEventDispatcher
{
    /**
     * @param $eventName
     * @param $eventData
     * @return void
     */
    public function dispatch($eventName, $eventData) : void
    {
        if (app()->version() > '5.1' && app()->version() < '5.8') {
            app('events')->fire($eventName, $eventData);
        } elseif (app()->version() >= '5.8') {
            app('events')->dispatch($eventName, $eventData);
        }
    }
}
