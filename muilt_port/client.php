<?php

class Client
{
    private $client;

    public function __construct() {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $this->client->on('Connect', [$this, 'onConnect']);
        $this->client->on('Close', [$this, 'onClose']);
        $this->client->on('Error', [$this, 'onError']);
    }

    public function connect() {
        if(!$fp = $this->client->connect("127.0.0.1", 9501, 1)) {
            echo "Error: {$fp->errMsg}[{$fp->errCode}]".PHP_EOL;
            return;
        }
    }

    public function onConnect() {

        fwrite(STDOUT, "发送测试数据(Y or N):");
        swoole_event_add(STDIN, function() {
            $msg = trim(fgets(STDIN));
            if ($msg == 'y') {
                $this->send();
            }
            fwrite(STDOUT, "发送测试数据(Y or N):");
        });
    }

    public function send() {
        $msg = '客户端发的信息...';
        $msg_info = pack('N', strlen($msg)).$msg;

        $i = 0;
        while ($i < 50) {
            var_dump($msg_info);
            $this->client->send($msg_info);
            $i++;
        }
    }

    public function onClose() {
        echo "Client close connection".PHP_EOL;
    }

    public function onError() {

    }
}

$client = new Client();
$client->connect();