<?php
/*
 * uctracker project
 *
 * Copyright 2017 xiebojie@qq.com
 * Licensed under the Apache License v2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 */
include dirname(__FILE__) . '/uboot.inc.php';
class crontab
{

    private $worker_list = array();
    private $hostname;
    private $read_pipe;
    private $write_pipe;

    public function __construct()
    {
        $this->hostname = $_SERVER['HOSTNAME'];
        $this->ip = get_local_ip();
    }

    public function start()
    {
        $this->start_sync_slave();
        while (true)
        {
            $this->load_mission();
            $now = time();
            foreach ($this->worker_list as $_id => $_woker)
            {
                $_proc = $_woker['proc'];
                if ($_proc->is_running())
                {
                    $task = array('misson_id' => $_id, 'pid' => $_proc->pid, 'output' => $_proc->get_output(), 'error' => $_proc->get_error());
                    $this->send_sync_task($task);
                    //@todo 将数据写入到后台写会服务器的进程管道
                    echo $_proc->pid, "\t", $_proc->get_output(), "\n";
                } else
                {
                    $nextrun = self::parse_cron_time($_woker['time'], $_proc->starttime);
                    var_dump($nextrun);
                    if (!is_null($nextrun) && $now >= $nextrun)
                    {
                        $_proc->start();
                    }
                }
            }
            usleep(60000);
        }
    }

    public function load_mission()
    {
        // $query = http_get('http://dev3v.white.corp.qihoo.net:666/api/get_misssion?'.$host);
        //$resp = json_decode($query,true);
        $resp = array(
            'mission_list' => array(
                1 => array(
                    'name' => 'ls',
                    'cmd' => "ls -al",
                    'out' => '/tmp/php_crontab.log',
                    'timestr' => '*/2 * * * *',
                    'script' => 'test.php'
                )
        ));

        //@todo 检查可能被删除的记录，从
        foreach ($resp['mission_list'] as $_id => $_mission)
        {
            if (!isset($this->worker_list[$_id]))
            {
                $proc = new mission($_mission['script']);
                $task = array(); //@todo 进程信息传回到服务器
                $this->send_sync_task($task);
                $this->worker_list[$_id] = array(
                    'proc' => $proc,
                    'name' => $_mission['name'],
                    'time' => $_mission['time']
                );
            } else if ($_mission['cmd'] == 'kill')
            {
                $this->worker_list[$_id]->stop();
            }
        }
    }

    /**
     * 创建一个自进程，负责处理和服务器端的通信（）
     */
    private function start_sync_slave()
    {
        $this->read_pipe = new pipe('/tmp/php-cron.write.pipe');
        $this->write_pipe = new pipe('/tmp/php-cron.read.pipe');
        $pid = pcntl_fork();
        if ($pid == 0)
        {
            $this->write_pipe->write_line("test");
        } else
        {
            while (true)
            {
                $result = $this->write_pipe->read_line();
                if (!empty($result))
                {
                    //@todo 写回服务器
                    echo $result . PHP_EOL;
                }
                sleep(120);
            }
        }
    }

    //将需要同步的数据写入到管道
    private function send_sync_task($task)
    {
        $line = str_replace('\n', '', json_encode($task));
        $this->write_pipe->write_line($line);
    }

    //解析cron的时间间隔，返回下一次的运行时间戳
    public static function parse_cron_time($timestr, $last_timestamp = null)
    {
        if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i', trim($timestr)))
        {
            throw new Exception("Invalid cron string: " . $timestr);
        }
        $cron = preg_split("/[\s]+/i", trim($timestr));
        $start = empty($last_timestamp) || is_numeric($last_timestamp) ? time() : $last_timestamp;
        $date = array(
            'minutes' => self::parse_cron_num($cron[0], 0, 59),
            'hours' => self::parse_cron_num($cron[1], 0, 23),
            'dom' => self::parse_cron_num($cron[2], 1, 31),
            'month' => self::parse_cron_num($cron[3], 1, 12),
            'dow' => self::parse_cron_num($cron[4], 0, 6),
        );
        // limited to time()+366 - no need to check more than 1year ahead
        for ($step = 0; $step <= 60 * 60 * 24 * 366; $step += 60)
        {
            list($j, $n, $w, $G, $i) = explode('-', date('j-n-w-G-i'), $start + $step);

            if (in_array($j, $date['dom']) && in_array($n, $date['month']) && in_array($w, $date['dow']) &&
                    in_array($G, $date['hours']) && in_array($i, $date['minutes']))
            {
                return $start + $step;
            }
        }
        return null;
    }

    /**
     * get a single cron style notation and parse it into numeric value
     *
     * @param string $s cron string element
     * @param int $min minimum possible value
     * @param int $max maximum possible value
     * @return int parsed number
     */
    protected static function parse_cron_num($s, $min, $max)
    {
        $result = array();
        $v = explode(',', $s);
        foreach ($v as $vv)
        {
            $vvv = explode('/', $vv);
            $step = empty($vvv[1]) ? 1 : $vvv[1];
            $vvvv = explode('-', $vvv[0]);
            $_min = count($vvvv) == 2 ? $vvvv[0] : ($vvv[0] == '*' ? $min : $vvv[0]);
            $_max = count($vvvv) == 2 ? $vvvv[1] : ($vvv[0] == '*' ? $max : $vvv[0]);
            for ($i = $_min; $i <= $_max; $i += $step)
            {
                $result[$i] = intval($i);
            }
        }
        ksort($result);
        return $result;
    }

}

$crontab = new crontab();
$crontab->start();