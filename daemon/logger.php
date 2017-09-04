<?php
/*
 * uctracker project
 *
 * Copyright 2017 xiebojie@qq.com
 * Licensed under the Apache License v2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 */
include __DIR__.'/uboot.inc.php';
use_session();
$pathlist = array(
    '/var/web/phpdeploy/var/*.log'
);

    foreach ($pathlist as $_path)
    {   
        $filelist = glob($_path);
        foreach ($filelist as $_file)
        {   
            if(filectime($_file)>$yesterday)
            {   
                $line_list = explode("\n", file_get_contents($_file));
                if(submit_loglist(array_slice($line_list, $last_line_no)))
                {   
                    $_SESSION[$_file] = $last_line_no;
                }   
            }   
        }   
    }   



function submit_loglist($loglist)
{
    $uname = posix_uname();
    $loglist = json_encode($loglist);
    try 
    {   
        http_request('http://dev3v/api/logger_collect?nodename='.$uname['nodename'],array('loglist'=>$loglist));
        return true;
    }catch (Exception $ex)
    {   
        trigger_error($ex->getMessage(),E_USER_ERROR);
        return false;
    }   
}
