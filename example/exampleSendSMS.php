<?php

require __DIR__."/../vendor/autoload.php";

use Schema31\GCloudSmsSDK\SendSMS;

if ($argc < 4) {
    die("\n\nAttenzione!\n\nNon hai specificato abbastanza parametri: \n" . $argv[0] . " <OpenVOIPUser> <Secret> <To> <Text>\n\n");
}

$OpenVOIPUser = $argv[1];
$Secret = $argv[2];
$To = $argv[3];
$archive = TRUE;

unset($argv[0]);
unset($argv[1]);
unset($argv[2]);
unset($argv[3]);
$Text = implode(" ", $argv);

$sendSMS = new SendSMS($OpenVOIPUser);
$sendSMS->SetSecret($Secret);

echo $sendSMS->SendMessage($To, $Text, $archive);