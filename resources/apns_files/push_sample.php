<?php

$dev = true;

if ($dev) {
    $apnsHost = 'gateway.sandbox.push.apple.com';
    $apnsCert = 'masarap-dev.pem';
    $apnsPass = 'masarap';
} else {
    $apnsHost = 'gateway.push.apple.com';
    $apnsCert = 'MasarapPushProd.pem';
    $apnsPass = 'tric rullanek';
}

$apnsPort = 2195;
#$token = '0d6625e51ae6ca1cb7d419ea80096808a42a0b45c463bc660bf336e3f0e86e86';
$token = '8f3eff644d5019b2617a66dd029056395a608c36d0173c19a324fdcad81c510c';

$payload['aps'] = array('alert' => 'Hi! I\'m from a sample script. デス', 'badge' => 1, 'sound' => 'default');
$output = json_encode($payload);
$token = pack('H*', str_replace(' ', '', $token));
$apnsMessage = chr(0).chr(0).chr(32).$token.chr(0).chr(strlen($output)).$output;

$streamContext = stream_context_create();
stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
stream_context_set_option($streamContext, 'ssl', 'passphrase', $apnsPass);

$apns = stream_socket_client('ssl://'.$apnsHost.':'.$apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
$ret = fwrite($apns, $apnsMessage);
var_dump($ret);
fclose($apns);
