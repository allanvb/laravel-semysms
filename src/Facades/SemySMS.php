<?php

namespace Allanvb\LaravelSemysms\Facades;

use \Illuminate\Support\Facades\Facade;

class SemySMS extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'semy-sms';
    }

}
