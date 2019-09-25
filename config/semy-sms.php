<?php

return [

    /**
     * Token
     * You can get it from: https://semysms.net/myprofile/usertoken
     */
    'token' => env('SEMYSMS_TOKEN'),

    /**
     * Device ID
     * This device will be used as default for your requests
     * You can get Device ID on: https://semysms.net/myprofile/devices
     */
    'device_id' => env('SEMYSMS_DEVICE_ID'),

    /**
     * Capture Incoming messages
     * You set capture link on: https://semysms.net/myprofile/devices
     */
    'catch_incoming' => false,

];
