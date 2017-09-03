{%extend base.inc.php%}
{%block main%}
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
                            <option value="<?php echo $k ?>">{%$v%}</option>
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
        <input type="hidden" name="psize" value="{%$psize%}"/>
        <input type="hidden" name="page" value="{%$page%}"/>
        <input type="hidden" id="total_page" value="{%$count/$psize|ceil%}"/>
    </form>
    <div class="js-pager">

        <div class="total">总数：{%$count|number_format%}</div>
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
                <td>{%$_mission['id']|default:''%}</td>
                <td>{%$_mission['name']|default:''%}</td>
                <td align="center">{%$_mission['cron_spec']%}</td>
                <td>{%$_mission['script']%}</td>
                <td>{%$_mission['node_list']|nl2br|raw|default:''%}</td>
                <td>{%$_mission['proc_num']|default:''%}</td>
                <td>{%$_mission['operator']|default:''%}</td>
                <td><?php echo mission_model::$status[$_mission['status']] ?></td>
                <td class="text-center">
                    <?php if ($_mission['status'] == mission_model::STATUS_RUNNING): ?>
                        <a class="btn btn-danger ajax-post" href="/mission/stop/{%$_mission['id']%}" data-confirm="确定要停止此任务吗？">
                            <span class="glyphicon glyphicon-stop"></span> 停止
                        </a>
                        <a class="btn btn-success ajax-post" href="/mission/restart/{%$_mission['id']%}" data-confirm="确定要重启此任务吗">
                            <span class="glyphicon glyphicon-retweet"></span> 重启
                        </a>
                    <?php else: ?>
                        <a class="btn btn-success ajax-post" href="/mission/start/{%$_mission['id']%}" data-confirm="确定要运行此任务吗">
                            <span class="glyphicon glyphicon-play"></span> 运行
                        </a>
                    <?php endif; ?>
                    <a href="/mission/form/{%$_mission['id']%}" class="btn btn-warning">
                        <span class="glyphicon glyphicon-edit"></span> 编辑
                    </a>
                    <a href="/mission/loglist?mission_id={%$_mission['id']%}" class="btn btn-info">
                        <span class="glyphicon glyphicon-info-sign"></span> 日志
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
{%endblock%}
