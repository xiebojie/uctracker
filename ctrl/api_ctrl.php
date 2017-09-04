<?php
/*
 * uctracker project
 *
 * Copyright 2017 xiebojie@qq.com
 * Licensed under the Apache License v2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 */
class api_ctrl extends ctrl
{

    //private $private_key ='';
    public function __construct()
    {
        $this->auth_pass = true;
        parent::__construct();
    }

    public function mission_list()
    {
        $mission_model = new mission_model();
        $nodename = isset($_REQUEST['nodename']) ? $_REQUEST['nodename'] : 0;
        $list = array();
        foreach ($mission_model->search_by_node($nodename) as $row)
        {
            $list[$row['mission_node_id']] = array(
                'node_mission_id' => $row['mission_node_id'],
                'script' => $row['script'],
                'mission_id' => $row['mission_id'],
                'name' => $row['name'],
                'cron_spec' => $row['cron_spec'],
                'signal' => $row['signal']
            );
        }
        return $list;
    }

    public function mission_trace()
    {
        $mission_model = new mission_model();
        $nodename = isset($_REQUEST['nodename']) ? $_REQUEST['nodename'] : 0;
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $trace = isset($_REQUEST['trace']) ? json_decode($_REQUEST['trace'], true) : array();

            $mission_model->append_mission_log($trace['mission_id'], $nodename, $trace['pid'], $trace['stdout'], $trace['stderr']);
            var_dump($trace);
            if (trim($trace['stdout']) == 'stopped')
            {
                $mission = $mission_model->fetch($trace['mission_id']);
                $mission_model->remove_signal($trace['mission_id'], $nodename);
                if ($mission['status'] == mission_model::STATUS_RESTART && !$mission_model->has_signal($trace['mission_id']))
                {
                    $mission_model->start_running($trace['mission_id'], mission_model::STATUS_RUNNING);
                }
            }
            return array('status' => 'ok');
        }
    }

    public function errlog()
    {
        $uname = isset($_REQUEST['uname']) ? $_REQUEST['uname'] : '';
        $project = isset($_REQUEST['project']) ? $_REQUEST['project'] : '';
        $loglist = isset($_REQUEST['loglist']) ? $_REQUEST['loglist'] : '';
        $logger_model = new errlog_model();
        foreach (explode("\n", $loglist) as $_line)
        {

            $_log = json_decode($_line, true);
            var_dump($_log);
            if (!empty($_log))
            {
                $valid_fields = array(
                    'uname' => $uname,
                    'project' => $project,
                    'errno' => $_log['errno'],
                    'errstr' => $_log['errstr'],
                    'errfile' => $_log['errfile'],
                    'errline' => $_log['errline'],
                    'ctime' => date('Y-m-d H:i:s', $_log['datetime'])
                );
                $logger_model->insert($valid_fields);
            }
        }
    }


    //接收nginx日志
    public function nginx()
    {
        $uname = isset($_REQUEST['uname']) ? $_REQUEST['uname'] : '';
        $project = isset($_REQUEST['project']) ? $_REQUEST['project'] : '';
        $loglist = isset($_REQUEST['loglist']) ? $_REQUEST['loglist'] : '';
        foreach (explode("\n", $loglist) as $_line)
        {
            //@todo 解析nginx日志
        }
    }
}
