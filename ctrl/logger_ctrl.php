<?php
/*
 * uctracker project
 *
 * Copyright 2017 xiebojie@qq.com
 * Licensed under the Apache License v2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 */
class logger_ctrl extends ctrl
{
    private $model;
    public function __construct()
    {
        parent::__construct();
        $this->model = new logger_model();
    }

    public function index()
    {
        $filter_rules = array(
            'id'=>'column:id|compare:equal',
            'errfile'=>'column:name|compare:like',
            'errno'=>'column:operator|compare:like',
            'sdate'=>'column:ctime|compare:date_from',
            'edate'=>'column:ctime|compare:date_end'
        );
        $filter_where = form_filter_parse($filter_rules, $_GET);
        list($page, $psize) = $this->fetch_paging_param();
        list($logger_list, $total) = $this->model->search_list($filter_where, ($page-1)*$psize, $psize);
        $this->assign('logger_list',$logger_list,'total',$total);
        $this->display('logger.list.php');
    }

    public function nginx()
    {
        $logger_list = array();
        $this->assign('logger_list',$logger_list,'total',10);
        $this->display('logger.nginx.php');
    }

}