{%extend base.inc.php%}
{%block main%}
<div class="pageheader">
    <h1 class="pagetitle">错误日志</h1>
    <ul class="hornav">
        <li class="current"><a href="/errlog/list">日志列表</a></li>
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
        <input type="hidden" name="psize" value="{%$psize%}"/>
        <input type="hidden" name="page" value="{%$page%}"/>
        <input type="hidden" id="total_page" value="{%$total/$psize|ceil%}"/>
    </form>
    <div class="js-pager pull-right"><span class="total">总数：{%$total|number_format%}</span></div>
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
                <td align="center">{%$_logger['id']%}</td>
                <td>{%$_logger['project']%}</td>
                <td>{%$level[$_logger['errno']]|default:''%}</td>
                <td>{%$_logger['uname']%}</td>
                <td>{%$_logger['errfile']%}</td>
                <td>{%$_logger['errline']%}</td>
                <td>{%$_logger['errstr']%}</td>
                <td align="center">{%$_logger['ctime']%}</td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
{%endblock%}

