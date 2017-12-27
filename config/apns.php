<?php
// app/config/apns.php

// Apple Push Notification Service app Config
return array(
    'apns_host' => env('APNS_HOST'),
    'apns_pem_file' => base_path() . env('APNS_PEM_FILE') ,
    'apns_passphrase' => env('APNS_PASSPHRASE')
);