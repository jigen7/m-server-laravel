<?php


// API access key from Google API's Console
#define( 'API_ACCESS_KEY', 'AIzaSyA_4gPfyuIGXkrXffbTLVCxlLLDyD100Ys' );
define( 'API_ACCESS_KEY', 'AIzaSyDXPw1-EpmJ_QTItUnmGvJ-wHguqJ29cwc' );

#Gadwinn's Device ID
$registrationIds = array("APA91bEBOMgPay6wPyQ9hPcuOnwUCL1N32ByMfNmwAl9Dlvyz94_cv-KM6uIiCFgShTxKUMd_fSK8-IOumv55geSe5xStFo8JcxbQzcKOofO3g0geiu4SaZyop0CODN9t2Pb_t_Cupt-rb2a67MrYH2j_SZf7Wd2aQ" );

$msg = array
(
    'message'       => 'Test message.',
    'type'          => 'friend_join',
    'type_id'       => 0,
    'user_id_from'  => 0,
    'user_id_to'    => 0,
    'title'         => 'This is a title... THE title',
    'subtitle'      => 'This is a subtitle... THE subtitle',
    'tickerText'    => 'Ticker message - the message that will appear on the status bar.',
    'vibrate'   => 1,
    'sound'     => 1
);

$fields = array
(
    'registration_ids'  => $registrationIds,
    'data'              => $msg
);

$headers = array
(
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
$result = curl_exec($ch );
curl_close( $ch );

echo $result;
?>
