<?php 
/*
	root      22576   5413  0 16:45 pts/0    00:00:00 php test.php
	root      22586  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22587  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22588  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22589  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22590  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22591  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22592  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22593  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22594  22576  6 16:45 pts/0    00:00:05 php test.php
	root      22595  22576  6 16:45 pts/0    00:00:05 php test.php
 */

//一次开启10个进程去消费队列
/*$workerNum = 10;
$pool = new Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started\n";
    $redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);
    $key = "key1";
    while (true) {
         $msgs = $redis->rpop($key);
         if ( $msgs == null) continue;
         var_dump($msgs);
     }
});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();*/



//携程方式：一次开启10个进程去消费队列
/*$pool = new Swoole\Process\Pool(10, SWOOLE_IPC_NONE, 0, true);

$pool->on('workerStart', function (Swoole\Process\Pool $pool, int $workerId) {
    while (true) {
        Co::sleep(0.5);
        $redis = new Redis();
	    $redis->pconnect('127.0.0.1', 6379);
	    $key = "key1";
	    while (true) {
	        $msgs = $redis->rpop($key);
	        if ( $msgs == null) continue;
	        var_dump($msgs);
	    }
    }
});

$pool->start();*/



/*信号处理   
	root      47000   5413  0 17:22 pts/0    00:00:00 php test.php
	root      47001  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47002  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47003  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47004  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47005  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47006  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47007  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47008  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47009  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47010  47000  6 17:22 pts/0    00:00:01 php test.php
	root      47073   9444  0 17:22 pts/1    00:00:00 grep --color test
	[root@localhost wwwroot]# kill -s SIGUSR1 47000
	[root@localhost wwwroot]# ps -ef|grep test
	root      47000   5413  0 17:22 pts/0    00:00:00 php test.php
	root      47118  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47119  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47120  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47121  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47122  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47123  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47124  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47125  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47126  47000  6 17:23 pts/0    00:00:03 php test.php
	root      47127  47000  6 17:23 pts/0    00:00:03 php test.php

	[root@localhost wwwroot]# kill -s SIGTERM 47000
 */

//一次开启10个进程去消费队列
$workerNum = 10;
$pool = new Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
    $running = true;
    pcntl_signal(SIGTERM, function () use (&$running) {
        $running = false;
    });

    echo "Worker#{$workerId} is started\n";
    $redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);
    $key = "key1";
    while ($running) {
		$msg = $redis->rpop($key);
		pcntl_signal_dispatch();
		if ( $msg == null) continue;
		var_dump($msg);
    }
});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();