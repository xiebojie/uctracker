<?php
/**
 * 监控日志输出目录将最近更新的文件的内容读出根据上次传输的行号，将内容传输到服务器
 * @date 2017-06-14
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
