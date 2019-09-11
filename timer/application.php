<?php

$api_url  = 'xxx'; //接口地址
$exec_num = 0;     //执行次数

//每5隔分钟再发送一次请求，最多尝试5次，在5次内成功停止该任务，5次仍失败也停止该任务。
swoole_timer_tick(1*60*1000, function($timer_id) use ($api_url, &$exec_num) {
    $exec_num ++ ;
    $result = 0;//$this->requestUrl($api_url);
    echo date('Y-m-d H:i:s'). " 执行任务中...(".$exec_num.")\n";
    if ($result) {
        //业务代码...
        swoole_timer_clear($timer_id); // 停止定时器
        echo date('Y-m-d H:i:s'). " 第（".$exec_num."）次请求接口任务执行成功\n";
    } else {
        if ($exec_num >= 5) {
            swoole_timer_clear($timer_id); // 停止定时器
            echo date('Y-m-d H:i:s'). " 请求接口失败，已失败5次，停止执行\n";
        } else {
            echo date('Y-m-d H:i:s'). " 请求接口失败，5分钟后再次尝试\n";
        }
    }
});