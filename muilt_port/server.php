<?php

class Server
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server('0.0.0.0', 9501);
        $this->serv->set([
            'worker_num'      => 2, //开启2个worker进程
            'max_request'     => 4, //每个worker进程 max_request设置为4次
            'dispatch_mode'   => 2, //数据包分发策略 - 固定模式

            //固定包头+包体协议
            'open_length_check'     => true,
            'package_max_length'    => '8192',
            'package_length_type'   => 'N',
            'package_length_offset' => '0',
            'package_body_offset'   => '4',
        ]);

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on("Receive", [$this, 'onReceive']);
        $this->serv->on("Close", [$this, 'onClose']);

        $this->serv->start();
    }

    public function onStart($serv) {
        echo "#### onStart ####".PHP_EOL;
        echo "swoole_cpu_num:".swoole_cpu_num().PHP_EOL;
        echo "SWOOLE ".SWOOLE_VERSION . " 服务已启动".PHP_EOL;
        echo "master_pid: {$serv->master_pid}".PHP_EOL;
        echo "manager_pid: {$serv->manager_pid}".PHP_EOL;
        echo "########".PHP_EOL.PHP_EOL;
    }

    public function onConnect($serv, $fd) {
        echo "#### onConnect ####".PHP_EOL;
        echo "客户端:".$fd." 已连接".PHP_EOL;
        echo "########".PHP_EOL.PHP_EOL;
    }

    public function onReceive($serv, $fd, $from_id, $data) {
        echo "#### onReceive ####".PHP_EOL;
        $length = unpack('N', $data)[1];
        echo "Length:".$length.PHP_EOL;
        $msg = substr($data, -$length);
        echo "Msg:".$msg.PHP_EOL;
    }

    public function onClose($serv, $fd) {
        echo "Client Close.".PHP_EOL;
    }
}

$server = new Server();