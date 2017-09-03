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
    <h1 class="pagetitle">错误日志</h1>
    <ul class="hornav">
        <li class="current"><a href="/errlog/list">日志列表</a></li>
    </ul>
</div>
<div class="contentpanel">
    <form class="search-form">
        <table class="search-table" style="width:788px">
            <tr>
                <th>项目</th>
                <td><select name="project" class="form-control">
                        <option value="">全部</option>
                        <?php foreach ($project_list as $_project): ?>
                            <option value="<?php echo $_project['project_name'] ?>"><?php echo $_project['project_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <th>errno</th>
                <td><select name="errno" class="form-control">
                        <option value="">全部</option>
                        <?php foreach ($level as $k => $v): ?>
                            <option value="<?php echo $k ?>"><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <th>errfile</th>
                <td><input type="text" class="form-control" name="errfile"/></td>
            </tr>
            <tr>
                <th>日期</th>
                <td colspan="3">
                    <div class="input-group">
                        <input type="text" name="stime" class="datepicker form-control"/>
                        <span class="input-group-addon">至</span>
                        <input type="text" name="etime" class="datepicker form-control"/>
                    </div>
                </td>
            </tr>
            <tr>
                <th></th>
                <td><button type="submit" class="btn btn-primary">查 询</button></td>
            </tr>
        </table>
        <input type="hidden" name="psize" value="<?php echo htmlspecialchars($psize);?>"/>
        <input type="hidden" name="page" value="<?php echo htmlspecialchars($page);?>"/>
        <input type="hidden" id="total_page" value="<?php echo htmlspecialchars(ceil($total/$psize));?>"/>
    </form>
    <div class="js-pager pull-right"><span class="total">总数：<?php echo htmlspecialchars(number_format($total));?></span></div>
    <table class="table table-bordered table-striped">
        <tr>
            <th width="52">id</th>
            <th>project</th>
            <th>errno</th>
            <th>uname</th>
            <th>errfile</th>
            <th>errline</th>
            <th>errstr</th>
            <th width="160">日期</th>
        </tr>
        <?php foreach ($logger_list as $_logger): ?>
            <tr>
                <td align="center"><?php echo htmlspecialchars($_logger['id']);?></td>
                <td><?php echo htmlspecialchars($_logger['project']);?></td>
                <td><?php echo htmlspecialchars(empty($level[$_logger['errno']])?'':$level[$_logger['errno']]);?></td>
                <td><?php echo htmlspecialchars($_logger['uname']);?></td>
                <td><?php echo htmlspecialchars($_logger['errfile']);?></td>
                <td><?php echo htmlspecialchars($_logger['errline']);?></td>
                <td><?php echo htmlspecialchars($_logger['errstr']);?></td>
                <td align="center"><?php echo htmlspecialchars($_logger['ctime']);?></td>
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