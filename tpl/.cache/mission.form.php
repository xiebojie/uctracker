<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="icon" href="/style/images//favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <title><?php echo htmlspecialchars(empty($title)?'uctracker':$title);?></title>
        <link href="/style/bootstrap.min.css" rel="stylesheet"/>
        <link href="/style/bootstrap.datepicker.css" rel="stylesheet"/>
        <link href="/style/ucbase.css" rel="stylesheet"/>
        <script src="/script/jquery.min.js"></script>
    </head>
    <body>
        <div class="navbar" style="background:#428bca;border-radius: 0px;margin: 0px">
            <div class="navbar-brand" style="font-size:28px;color:#fff">uctracker</div>
              <div class="pull-right whoami">
                   <a class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-user"></span><?php echo htmlspecialchars(empty($username)?'':$username);?>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="/user/logout">退出</a></li>
                </ul>
              </div>
        </div>
        <div>
            <div class="leftpanel">
                <ul class="nav" >
                    <li style="border-top: 0.5px solid #e7e7e7">
                        <a href="/"><i class="glyphicon glyphicon-home"></i>首页</a>
                    </li>
                <li class="parent"><a href="/mission/list">cron列表</a></li>
                <li class="parent"><a href="/mission/form">添加cron</a></li>
                <li class="parent"><a href="/mission/loglist">cron日志</a></li>
                <li class="parent"><a href="/logger/list">错误日志</a></li>
                <li class="parent"><a href="/logger/nginx">nginx日志</a></li>   
                <li class="parent"><a href="/user/list">用户列表</a></li>
                <li class="parent"><a href="/user/form">添加用户</a></li>
            </ul>
        </div>
        <div class="mainpanel">
            <div class="pageheader">
    <h1 class="pagetitle">cron管理</h1>
    <ul class="hornav">
        <li ><a href="/mission/list">cron列表</a></li>
        <li class="current"><a href=""><?php echo empty($mission) ? '添加' : '编辑' ?>任务</a></li>
        <li><a href="/mission/loglist">cron日志</a></li>
    </ul>
</div>
<div class="contentpanel">
    <div class="alert alert-warning " style="width:75%">
        <strong>提示：</strong>程序只有自动退出，默认不会杀掉超时任务，且只有进程退出后，才会启动新的进程，比如程序设置2分钟一次，但本次执行超过2分钟，
        那么两分钟后并不会启动一个新进程，只有进程退出后，才马上启动一个新进程
    </div>
    <form class="form-horizontal mt30 js-ajaxform" method="post">
        <div class="form-group">
            <label  class="col-sm-1 control-label">任务名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" data-rule="required" value="<?php echo htmlspecialchars(empty($mission['name'])?'':$mission['name']);?>"/>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label">任务脚本</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="script" data-rule="required" value="<?php echo htmlspecialchars(empty($mission['script'])?'':$mission['script']);?>" 
                       placeholder="请填程序绝对地址或linux 命令，可包含参数"/>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label">时间表达式</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="cron_spec" data-rule="required" value="<?php echo htmlspecialchars(empty($mission['cron_spec'])?'':$mission['cron_spec']);?>"
                       placeholder="crontab 时间格式" data-rule="/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i"/>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label">单机进程数</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="proc_num" data-rule="required" value="<?php echo htmlspecialchars(empty($mission['proc_num'])?'1':$mission['proc_num']);?>"
                       placeholder="每个机器上的启动进程数"/>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label">部署机器</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="node_list" data-rule="required"><?php echo htmlspecialchars(empty($mission['node_list'])?'':$mission['node_list']);?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label"></label>
            <div class=" col-sm-10">
                <button type="submit" class="btn btn-success">提 交</button>
            </div>
        </div>
    </form>
</div>
        </div>
    </div>
    <script src="/script/bootstrap.js"></script>
    <script src="/script/jquery.pagination.js"></script>
    <script src="/script/jquery.validator.js"></script>
    <script src="/script/jquery.validator.zh.js"></script>
    <script src="/script/bootstrap-datetimepicker.js"></script>
    <script src="/script/bootstrap-datetimepicker.zh.js"></script>
    <script src="/script/jquery.form.js"></script>
    <script src="/script/bootbox.js"></script>
    <script src="/script/ucbase.js"></script>
    <script>
            var path = window.location.pathname.replace(/\/(\d+|index)/,'');
            $('.leftpanel .nav li a').each(function() {
                if (this.href.indexOf(path) !==-1) 
                {
                    $(this).parent('li').addClass('active');
                    $(this).parent('li').parent().parent().addClass('active');
                    return false;
                }
            });
            $('.parent >a').click(function(){
                $('.active').removeClass('active');
                $(this).parent('li').addClass('active');
            });
        </script>
    </body>
</html>