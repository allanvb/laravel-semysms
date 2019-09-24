<?php

Route::post('/semy-sms/receive', ['as' => 'semy-sms.recieve', 'uses' => 'Allanvb\LaravelSemysms\Controllers\ReceiverController@receiveSMS']);

