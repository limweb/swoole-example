<?php

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 9501); 

//监听连接进入事件-接收客户端链接
$serv->on('connect', function ($serv, $fd) {  
    echo "Client: Connect.\n";
});

//监听数据接收事件-服务端发送数据包给客户端
/**
 * $serv  		服务器对象
 * $fd  		客户端标识
 * $from_id  	代表：ReactorThreadId
 * $data  		客户端数据包
 */
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Serversss: ".$data.'----------'.$fd.'=========='.$from_id);
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start(); 