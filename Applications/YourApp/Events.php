<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 *
 * 更多文档: http://doc2.workerman.net/326111
 */
class Events
{
    const EVENT_JOIN = 'join_group';

    /**
     * 发送数据到客户端格式封装
     *
     * @param string $event
     * @param string $data
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function getSendData($event = '', $data = '', $from = '', $to = '') {
        $sendData = [
            'event' => $event,
            'data'  => $data,
            'from'  => $from,
            'to'    => $to,
        ];

        return json_encode($sendData);
    }

    public static function onWorkerStart($businessWorker)
    {
        echo "onWorkerStart: \n\r";
    }

    /**
     * 每当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $clientId 连接id
     * @throws Exception
     */
    public static function onConnect($clientId) {
        echo "onConnect: \n\r";

        // 向当前client_id发送数据
        // Gateway::sendToClient($clientId, self::getSendData('on_connect', "Hello $clientId\n"));

        // 向所有人发送
        // Gateway::sendToAll(self::getSendData('on_connect', "$clientId login"));
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $clientId 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($clientId, $message) {
       echo "onMessage: \n\r";

       // TODO: 接收数据时需要针对 $message 不是 json 字符串的情况做处理, 不能报错
       $clientData = json_decode($message, true);

       switch ($clientData['event']) {
           // 客户端自定义分组
           case self::EVENT_JOIN:
               self::clientJoinGroups($clientId, $clientData['data']);
               Gateway::sendToClient($clientId, self::getSendData('join_group_response', '加入分组成功'));
               break;
       }

        // 向所有人发送
//        Gateway::sendToAll("$clientId said = $message");
   }

    /**
     * 将 client_id 加入某个组，以便通过 Gateway::sendToGroup 发送数据
     *
     * @param $clientId
     * @param $groups
     */
   public static function clientJoinGroups($clientId, $groups) {
       foreach ($groups as $group) {
           Gateway::joinGroup($clientId, $group);
       }
   }

   /**
    * 当用户断开连接时触发
    * @param int $clientId 连接id
    * @throws Exception
    */
   public static function onClose($clientId) {
       echo "onClose: \n\r";

       // 向所有人发送
       // Gateway::sendToAll(self::getSendData('on_close', "$clientId logout"));
   }

    public static function onWorkerStop($businessWorker)
    {
        echo "onWorkerStop: \n\r";
    }
}
