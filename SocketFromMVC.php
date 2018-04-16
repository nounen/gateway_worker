<?php
/**
 * MVC 框架主动发起 socket 通信
 *
 * 借助 GatewayClient 在 mvc 框架内主动发起 socket 通信
 */
require_once __DIR__ . '/vendor/autoload.php';

use GatewayClient\Gateway;

// register 地址与 Applications/*/start_register.php 内一致
Gateway::$registerAddress = '127.0.0.1:1238';

function getSendData($event = '', $data = '', $from = '', $to = '')
{
    $sendData = [
        'event' => $event,
        'data'  => $data,
        'from'  => $from,
        'to'    => $to,
    ];

    return json_encode($sendData);
}

//
$message = '这是一条来自 MVC 推送的 socket 消息!';

$targetGroup = 'teacher_1';

$sendData = getSendData('send_from_mvc', $message, 'mvc_faker', $targetGroup);

Gateway::sendToGroup($targetGroup, $sendData);
