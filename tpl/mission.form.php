{%extend base.inc.php%}
{%block main%}
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
                <input type="text" class="form-control" name="name" data-rule="required" value="{%$mission['name']|default:''%}"/>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label">任务脚本</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="script" data-rule="required" value="{%$mission['script']|default:''%}" 
                       placeholder="请填程序绝对地址或linux 命令，可包含参数"/>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label">时间表达式</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="cron_spec" data-rule="required" value="{%$mission['cron_spec']|default:''%}"
                       placeholder="crontab 时间格式" data-rule="/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i"/>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label">单机进程数</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="proc_num" data-rule="required" value="{%$mission['proc_num']|default:'1'%}"
                       placeholder="每个机器上的启动进程数"/>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-1 control-label">部署机器</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="node_list" data-rule="required">{%$mission['node_list']|default:''%}</textarea>
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
{%endblock%}