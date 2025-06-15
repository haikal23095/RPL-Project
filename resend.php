<?php
session_start();
require_once './vendor/autoload.php';
use Twilio\Rest\Client;

$sid    = 'AC4fcb38388f5c58c449b150823cf9b4eb';
$token  = 'cced338e264b32598d3adca6acdb624b';
$verifySid = 'VA1c3e751033905756f2848f4aeb7f4b0c';

$userPhone = $_SESSION['user']['no_tlp'] ?? '';

if (!empty($userPhone)) {
    $twilio = new Client($sid, $token);
    $twilio->verify->v2->services($verifySid)
        ->verifications
        ->create($userPhone, 'sms'); // atau 'email'
}

header('Location: verification.php');
exit();
