<?php

//每隔3000ms触发一次
$timer_id = swoole_timer_tick(3000, function () {
    echo "tick 3000ms - ".date('Y-m-d H:i:s')."\n";
});

var_dump(Swoole\Timer::info($timer_id));

foreach (Swoole\Timer::list() as $timeid) {
    var_dump(Swoole\Timer::info($timeid));
}

var_dump(Swoole\Timer::stats());

//9000ms后删除定时器
swoole_timer_after(9000, function () use ($timer_id) {
    echo "after 9000ms - ".date('Y-m-d H:i:s')."\n";
    swoole_timer_clear($timer_id);
});