<?php 

(new class{
    public $mpid=0;
    public $works=[];
    public $max_precess=5;
    public $new_index=0;

    //初始化
    public function __construct(){
        try {
            swoole_set_process_name(sprintf('php-ps:%s', 'master'));
            //主进程id
            $this->mpid = posix_getpid();
            #var_dump($this->mpid);  
            $this->run();
            $this->processWait();
        }catch (\Exception $e){
            die('ALL ERROR: '.$e->getMessage());
        }
    }

    /**
     * 开始
     */
    public function run(){
        for ($i=0; $i < $this->max_precess; $i++) {
            $this->CreateProcess();
        }
    }

    /**
     * 创建进程
     */
    public function CreateProcess($index=null){
        $process = new swoole_process(function(swoole_process $worker)use($index){
            if(is_null($index)){
                $index=$this->new_index;
                $this->new_index++;
            }
            swoole_set_process_name(sprintf('php-ps:%s',$index));
            for ($j = 0; $j < 16000; $j++) {
                //检测进程是否存在
                $this->checkMpid($worker);
                echo "msg: {$j}\n";
                sleep(1);
            }

            //TODO 消耗的任务在这里执行
            //执行redis队列任务
            
        }, false, false);

        //启动子进程开始并返回子进程id (worker进程)
        $pid=$process->start();
        #var_dump($pid);  
        $this->works[$index]=$pid;
        return $pid;
    }

    /**
     * 检测进程是否存在
     */
    public function checkMpid(&$worker){
        //$signo=0，可以检测进程是否存在，不会发送信号
        if(!swoole_process::kill($this->mpid,0)){
            //主进程退出
            $worker->exit();
            // 这句提示,实际是看不到的.需要写到日志中
            echo "Master process exited, I [{$worker['pid']}] also quit\n";
        }
    }

    /**
     * 重启进程
     */
    public function rebootProcess($ret){
        $pid=$ret['pid'];
        $index=array_search($pid, $this->works);
        if($index!==false){
            $index=intval($index);
            $new_pid=$this->CreateProcess($index);
            echo "rebootProcess: {$index}={$new_pid} Done\n";
            return;
        }
        throw new \Exception('rebootProcess Error: no pid');
    }

    /**
     * 检测进程信号变化
     */
    public function processWait(){
        while(1) {
            if(count($this->works)){
                //回收结束运行的子进程。当子进程异常或被kill掉，会返回一个数组包含子进程的PID、退出状态码、被哪种信号KILL
                //var_dump($ret);   
                //array(3) {
                //  ["pid"]=> int(12629)
                //  ["code"]=> int(0)
                //  ["signal"]=> int(15)
                //}

                $ret = swoole_process::wait();
                if ($ret) {
                    #var_dump($ret);
                    $this->rebootProcess($ret);
                }
            }else{
                break;
            }
        }
    }
});