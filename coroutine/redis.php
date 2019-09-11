<?php 

//方式一   简单实用 
/*
go(function () {
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $val = $redis->get('key1');
    var_dump($val);
});
*/


//方式二   defer特性
/*
const REDIS_SERVER_HOST = '127.0.0.1';
const REDIS_SERVER_PORT = 6379;

go(function () {
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect(REDIS_SERVER_HOST, REDIS_SERVER_PORT);
    $redis->setDefer();
    $redis->set('key1', 'value');

    $redis2 = new Swoole\Coroutine\Redis();
    $redis2->connect(REDIS_SERVER_HOST, REDIS_SERVER_PORT);
    $redis2->setDefer();
    $redis2->get('key1');

    $result1 = $redis->recv();
    $result2 = $redis2->recv();

    var_dump($result1, $result2);
});
*/


//方式三   pipeline
//Redis服务器支持多条指令并发执行。可使用defer特性启用pipeline

/*const REDIS_SERVER_HOST = '127.0.0.1';
const REDIS_SERVER_PORT = 6379;

go(function () {
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect(REDIS_SERVER_HOST, REDIS_SERVER_PORT);
    $redis->setDefer();
    $redis->set('key1', 'value');
    $redis->get('key1');

    $result1 = $redis->recv();
    $result2 = $redis->recv();

    var_dump($result1, $result2);
});
*/

//options方式
/*const REDIS_SERVER_HOST = '127.0.0.1';
const REDIS_SERVER_PORT = 6379;

go(function () {
    $options = [
        'connect_timeout' => 1,
        'timeout' => 5
    ];
    //options方式1
    // $redis = new Swoole\Coroutine\Redis($options);
    
    //options方式2
    $redis = new Swoole\Coroutine\Redis();
    $redis->setOptions($options);

    $redis->connect(REDIS_SERVER_HOST, REDIS_SERVER_PORT);

    $redis->setDefer();
    $redis->set('key1', 'value');
    $redis->get('key1');

    $result1 = $redis->recv();
    $result2 = $redis->recv();

    var_dump($result1, $result2);
});*/


/**/
go(function () {
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $res = $redis->request(['object', 'encoding', 'key1']);
    var_dump($res);
});
