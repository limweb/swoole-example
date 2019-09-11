<?php
require dirname(__FILE__) . '/MySQLPool.php';

use Swoole\Coroutine\Pool\MySQLPool;

$connect_1 = [
    'host' => '1',
    'user' => 'root',
    'password' => '123456',
    'database' => 'db1',
    'charset' => 'utf8mb4', //指定字符集
];

$connect_2 = [
    'host' => '2',
    'user' => 'root',
    'password' => '123456',
    'database' => 'db2',
    'charset' => 'utf8mb4', //指定字符集
];

$connect_3 = [
    'host' => '3',
    'user' => 'root',
    'password' => '123456',
    'database' => 'db3',
    'charset' => 'utf8mb4', //指定字符集
];

$server = new \Swoole\Server("127.0.0.1", 9510, SWOOLE_BASE);
$server->set([
    'worker_num' => 8,
    'daemonize' => 1,
    'max_coro_num' => 16000,
    'log_file' => '/var/www/mysql.log',
]);
$server->on('connect', function ($server, $fd){});
$server->on('receive', function ($server, $fd, $from_id,$data) use($connect_1,$connect_2,$connect_3){
    $data = json_decode($data,true);
    if (isset($data['database']) && $data['query']){
        MySQLPool::init([
            '1_connect' => [
                'serverInfo' => $connect_1,
                'maxSpareConns' => 10,
                'maxConns' => 20
            ],
            '2_connect' => [
                'serverInfo' => $connect_2,
                'maxSpareConns' => 10,
                'maxConns' => 20
            ],
            '3_connect' => [
                'serverInfo' => $connect_3,
                'maxSpareConns' => 10,
                'maxConns' => 20
            ],
        ]);
        $swoole_mysql = MySQLPool::fetch($data['database']);
        $swoole_mysql = MySQLPool::reconnect($swoole_mysql,$data['database']);
        $ret = $swoole_mysql->query($data['query'],60);
        MySQLPool::recycle($swoole_mysql);
        if ($server->exist($fd)){
            $server->send($fd, json_encode($ret));
        }
    }
});

$server->on('close', function ($server, $fd) {});
$server->start();
