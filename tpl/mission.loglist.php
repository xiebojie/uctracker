{%extend base.inc.php%}
{%block main%}
<div class="pageheader">
    <h1 class="pagetitle">cron管理</h1>
    <ul class="hornav">
        <li><a href="/mission/list">cron列表</a></li>
        <li><a href="/mission/form">添加cron</a></li>
        <li class="current"><a href="/mission/loglist">cron日志</a></li>
    </ul>
</div>
<div class="contentpanel">
    <form class="search-form">
        <table class="search-table" style="width:788px">
            <tr>
                <th>任务id</th>
                <td><input type="text" class="form-control" name="mission_id"/></td>
                <th>日期</th>
                <td>
                    <div class="input-group">
                        <input type="text" name="stime" class="datepicker form-control"/>
                        <span class="input-group-addon">至</span>
                        <input type="text" name="etime" class="datepicker form-control"/>
                    </div>
                </td>
            </tr>
            <tr>
                <th>机器</th>
                <td><input type="text" class="form-control" name="node"/></td>
            </tr>
            <tr>
                <th></th>
                <td><button type="submit" class="btn btn-primary">查 询</button></td>
            </tr>
        </table>
        <input type="hidden" name="psize" value="{%$psize%}"/>
        <input type="hidden" name="page" value="{%$page%}"/>
        <input type="hidden" id="total_page" value="{%$total/$psize|ceil%}"/>
    </form>
    <div class="js-pager pull-right"><span class="total">总数：{%$total|number_format%}</span></div>
    <table class="table table-bordered table-striped">
        <tr>
            <th>id</th>
            <th>任务名</th>
            <th>部署机器</th>
            <th>pid</th>
            <th width="30%">标准输出</th>
            <th width="30%">标准错误</th>
            <th>添加日期</th>
            <th>更新日期</th>
        </tr>
        <?php foreach ($loglist as $_log): ?>
            <tr>
                <td>{%$_log['id']%}</td>
                <td align="center"><a href="/mission/list?id={%$_log['mission_id']%}"><?php echo $_log['mission_id'] ?></a></td>
                <td>{%$_log['node']%}</td>
                <td>{%$_log['pid']%}</td>
                <td>{%$_log['stdout']|nl2br|raw%}</td>
                <td>{%$_log['stderr']|nl2br|raw%}</td>
                <td>{%$_log['ctime']%}</td>
                <td>{%$_log['utime']%}</td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
{%endblock%}