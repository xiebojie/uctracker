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
    public function index()
    {
        $logger_list = array();
        $this->assign('logger_list',$logger_list,'total',10);
        $this->display('logger.list.php');
    }

    public function nginx()
    {
        $logger_list = array();
        $this->assign('logger_list',$logger_list,'total',10);
        $this->display('logger.nginx.php');
    }

}