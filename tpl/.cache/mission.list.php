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
        <li class="current"><a href="/mission/list">cron列表</a></li>
        <li><a href="/mission/form">添加cron</a></li>
        <li><a href="/mission/loglist">cron日志</a></li>
    </ul>
</div>
<div class="contentpanel">
    <form class="search-form">
        <table class="search-table" style="width:888px">
            <tr>
                <th>任务名</th>
                <td><input type="text" class="form-control" name="name"/></td>
                <th>状态</th>
                <td>
                    <select class="form-control" name="status">
                        <option value="">全部</option>
                        <?php foreach (mission_model::$status as $k => $v): ?>
                            <option value="<?php echo $k ?>"><?php echo htmlspecialchars($v);?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>部署机器</th>
                <td><input type="text" class="form-control" name="node_list"/></td>
            </tr>
            <tr>
                <th></th>
                <td><button type="submit" class="btn btn-primary">查 询</button></td> </tr>
        </table>
        <input type="hidden" name="psize" value="<?php echo htmlspecialchars($psize);?>"/>
        <input type="hidden" name="page" value="<?php echo htmlspecialchars($page);?>"/>
        <input type="hidden" id="total_page" value="<?php echo htmlspecialchars(ceil($count/$psize));?>"/>
    </form>
    <div class="js-pager">

        <div class="total">总数：<?php echo htmlspecialchars(number_format($count));?></div>
    </div>
    <div class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            批量操作 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a href="#">运行</a></li>
            <li><a href="#">停止</a></li>
            <li><a href="#">重启</a></li>
        </ul>
    </div>
    <table class="table table-bordered table-striped">
        <tr>
            <th><input type="checkbox" class="checkall"/></th>
            <th>id</th>
            <th>任务名</th>
            <th>时间表达式</th>
            <th>部署程序</th>
            <th>部署机器</th>
            <th>单机进程数</th>
            <th>负责人</th>
            <th>状态</th>
            <th width="320">操作</th>
        </tr>
        <?php foreach ($mission_list as $_mission): ?>
            <tr>
                <td align="center"><input type="checkbox" name="ids[]" value=""/></td>
                <td><?php echo htmlspecialchars(empty($_mission['id'])?'':$_mission['id']);?></td>
                <td><?php echo htmlspecialchars(empty($_mission['name'])?'':$_mission['name']);?></td>
                <td align="center"><?php echo htmlspecialchars($_mission['cron_spec']);?></td>
                <td><?php echo htmlspecialchars($_mission['script']);?></td>
                <td><?php echo empty(nl2br($_mission['node_list']))?'':nl2br($_mission['node_list']);?></td>
                <td><?php echo htmlspecialchars(empty($_mission['proc_num'])?'':$_mission['proc_num']);?></td>
                <td><?php echo htmlspecialchars(empty($_mission['operator'])?'':$_mission['operator']);?></td>
                <td><?php echo mission_model::$status[$_mission['status']] ?></td>
                <td class="text-center">
                    <?php if ($_mission['status'] == mission_model::STATUS_RUNNING): ?>
                        <a class="btn btn-danger ajax-post" href="/mission/stop/<?php echo htmlspecialchars($_mission['id']);?>" data-confirm="确定要停止此任务吗？">
                            <span class="glyphicon glyphicon-stop"></span> 停止
                        </a>
                        <a class="btn btn-success ajax-post" href="/mission/restart/<?php echo htmlspecialchars($_mission['id']);?>" data-confirm="确定要重启此任务吗">
                            <span class="glyphicon glyphicon-retweet"></span> 重启
                        </a>
                    <?php else: ?>
                        <a class="btn btn-success ajax-post" href="/mission/start/<?php echo htmlspecialchars($_mission['id']);?>" data-confirm="确定要运行此任务吗">
                            <span class="glyphicon glyphicon-play"></span> 运行
                        </a>
                    <?php endif; ?>
                    <a href="/mission/form/<?php echo htmlspecialchars($_mission['id']);?>" class="btn btn-warning">
                        <span class="glyphicon glyphicon-edit"></span> 编辑
                    </a>
                    <a href="/mission/loglist?mission_id=<?php echo htmlspecialchars($_mission['id']);?>" class="btn btn-info">
                        <span class="glyphicon glyphicon-info-sign"></span> 日志
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
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