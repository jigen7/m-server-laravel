<?php
return [

    /*
    |--------------------------------------------------------------------------
    | oAuth Config
    |--------------------------------------------------------------------------
    */

    /**
     * Storage
     */
    'storage' => 'Session',

    /**
     * Consumers
     */
    'consumers' => [

        /**
         * Google oAuth
         */
        'Google' => [
            'client_id'     => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_SECRET'),
            'redirect'      => env('GOOGLE_REDIRECT_URI'),
            'client_dev_key'=> env('GOOGLE_KEY'),
            'scope'         => ['userinfo_email', 'userinfo_profile']
        ],

    ]

];