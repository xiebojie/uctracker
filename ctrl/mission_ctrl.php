<?php
/*
 * uctracker project
 *
 * Copyright 2017 xiebojie@qq.com
 * Licensed under the Apache License v2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 */
class mission_ctrl extends ctrl
{
  public function __construct()
    {   
        parent::__construct();
        $this->model = new mission_model();
    }

    public function index()
    {
        $filter_rules = array(
            'id'=>'column:id|compare:equal',
            'project_id'=>'column:project_id|compare:equal',
            'name'=>'column:name|compare:like',
            'operator'=>'column:operator|compare:like',
            'node_list'=>'column:node_list|compare:like',
            'status'=>'column:status|compare:equal'
        );
        $filter_where = form_filter_parse($filter_rules, $_GET);
        list($page, $psize) = $this->fetch_paging_param();
        list($mission_list, $total) = $this->model->search_list($filter_where, ($page-1)*$psize, $psize);
        $this->assign('mission_list', $mission_list,'count',$total);
        $this->display('mission.list.php');
    }
    public function form($mission_id=-1)
    {
        $mission = $this->model->fetch($mission_id);
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $valid_fields = array(
                'name'=>'required',
                'script'=>'required',
                'cron_spec'=>'required',
                'node_list'=>'required',
                'proc_num'=>'required'
            );
            $validator = new validator();
            list($valid_data, $valid_error) = $validator->validate($_POST, $valid_fields);
            if (empty($valid_error))
            {
                $valid_data['operator'] = $this->username;
                $valid_data['utime'] = 'timestamp';
                if (empty($mission))
                {
                    $valid_data['ctime'] = 'timestamp';
                    $mission_id = $this->model->insert($valid_data);
                }else
                {
                    $this->model->update($mission_id, $valid_data);
                    $this->model->delete_mission_node($mission_id);
                }
                foreach (explode("\n", trim($valid_data['node_list'])) as $node)
                {
                    for($i=0;$i<$valid_data['proc_num'];$i++)
                    {
                        $this->model->add_mission_node($mission_id, $node);
                    }
                }
                return array('error'=>0,'message'=>'修?~T??~H~P?~J~_','redirect'=>'/mission/list');
            }else
            {
                return array('error'=>1,'message'=>  implode("\n", $valid_error));
            }
        }else
 {
            $this->assign('mission', $mission);
            $this->display('mission.form.php');
        }
    }

    public function start($mission_id=-1)
    {
        $mission = $this->model->fetch($mission_id);
        if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($mission))
        {
            $this->model->start_running($mission_id);
            return array('error'=>0,'message'=>'');
        }
        return array('error'=>1,'message'=>'invalid request');
    }

    public function restart($mission_id=-1)
    {
        $mission = $this->model->fetch($mission_id);
        if(!empty($mission) && $_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->model->restart_running($mission_id);
            return array('error'=>0,'message'=>'');
        }
        return array('error'=>1,'message'=>'invalid request');
    }
public function stop($mission_id=-1)
    {
        $mission = $this->model->fetch($mission_id);
        if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($mission))
        {
            $this->model->stop_running($mission_id);
            return array('error'=>0,'message'=>'');
        }
        return array('error'=>1,'message'=>'invalid request');
    }

    public function loglist()
    {
        $filter_rules = array(
            'mission_id'=>'column:mission_id|compare:equal',
            'node'=>'column:node|compare:like',
            'stime'=>'column:deploy.ctime|compare:date_start',
            'etime'=>'column:deploy.ctime|compare:date_end'
        );
        $filter_where = form_filter_parse($filter_rules, $_GET);
        list($page, $psize) = $this->fetch_paging_param();
        list($loglist, $total) = $this->model->search_loglist($filter_where, ($page-1)*$psize, $psize);
        $this->assign('loglist', $loglist,'total', $total);
        $this->display('mission.loglist.php');
    }


}

