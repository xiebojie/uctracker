{%extend base.inc.php%}
{%block main%}
<div class="pageheader">
    <h1 class="pagetitle">系统日志</h1>
    <ul class="hornav">
        <li class="current"><a href="/mission/list">错误日至</a></li>
    </ul>
</div>
<div class="contentpanel">
    <form class="search-form">
        <table class="search-table" style="width:888px">
            <tr>
                <th>任务名</th>
                <td><input type="text" class="form-control" name="name"/></td>
                <th>所属项目</th>
                <td>
                    <select class="form-control" name="project_id">
                        <option value="">全部</option>
                        <?php foreach ($project_list as $_project): ?>
                            <option value="{%$_project['id']%}"><?php echo $_project['project_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <th>日期</th>
                <td></td>
            </tr>
            <tr>
                <th></th>
                <td><button type="submit" class="btn btn-primary">查 询</button></td> </tr>
        </table>
        <input type="hidden" name="psize" value="{%$psize%}"/>
        <input type="hidden" name="page" value="{%$page%}"/>
        <input type="hidden" id="total_page" value="{%$total/$psize|ceil%}"/>
    </form>
    <div class="js-pager">
        <div class="total">总数：{%$total|number_format%}</div>
    </div>
    <table class="table table-bordered table-striped">
        <tr>
            <th>id</th>
            <th>remote addr</th>
            <th>remote user</th>
            <th>request</th>
            <th>status</th>
            <th>http refer</th>
            <th>user agent</th>
        </tr>
        <?php foreach ($logger_list as $_logger): ?>
            <tr>
                <td>{%$_mission['id']|default:''%}</td>
                <td>{%$_mission['project_name']%}</td>
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
