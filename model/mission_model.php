<?php
/**
 * 进程model
 * 一个进程可以部署在多个机器上
 * CREATE TABLE `mission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '分组ID',
  `mission_name` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名称',
  `mission_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '任务类型',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '任务描述',
  `cron_spec` varchar(100) NOT NULL DEFAULT '' COMMENT '时间表达式',
  `concurrent` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否只允许一个实例',
  `command` text NOT NULL COMMENT '命令详情',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0停用 1启用',
  `notify` tinyint(4) NOT NULL DEFAULT '0' COMMENT '通知设置',
  `notify_email` text NOT NULL COMMENT '通知人列表',
  `timeout` smallint(6) NOT NULL DEFAULT '0' COMMENT '超时设置',
  `execute_times` int(11) NOT NULL DEFAULT '0' COMMENT '累计执行次数',
  `prev_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次执行时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  )

  CREATE TABLE `mission_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `output` mediumtext NOT NULL COMMENT '任务输出',
  `error` text NOT NULL COMMENT '错误信息',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  `process_time` int(11) NOT NULL DEFAULT '0' COMMENT '消耗时间/毫秒',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_task_id` (`task_id`,`create_time`)
  )
 */
class mission_model extends model
{

    protected $primary_table = 'uc_mission';
    protected $primary_key = 'id';

    const STATUS_RUNNING = 1;
    const STATUS_STOPED = 0;
    const STATUS_RESTART = 2;

    public static $status = array(
        self::STATUS_RUNNING => '运行中',
        self::STATUS_STOPED => '已停止',
        self::STATUS_RESTART => '重启中'
    );

    public function search_list($filter_where, $offset = 0, $limit_size = 20)
    {
        $offset = abs($offset);
        $limit_size = abs($limit_size);
        $sql = "SELECT SQL_CALC_FOUND_ROWS uc_mission.* FROM uc_mission ";
        if (!empty($filter_where))
        {
            $sql .=' WHERE ' . implode(' AND ', $filter_where);
        }
        $sql .=sprintf(" ORDER BY %s DESC ", $this->primary_key);
        if ($limit_size > 0)
        {
            $sql.=" LIMIT $offset,$limit_size";
        }
        $row_list = self::$db->fetch_all($sql);
        $count = self::$db->fetch_col('SELECT FOUND_ROWS()');
        return array($row_list, $count);
    }

    public function search_loglist($filter_where, $offset = 0, $limit_size = 20)
    {
        $offset = abs($offset);
        $limit_size = abs($limit_size);
        $sql = sprintf("SELECT SQL_CALC_FOUND_ROWS * FROM uc_mission_log ");
        if (!empty($filter_where))
        {
            $sql .=' WHERE ' . implode(' AND ', $filter_where);
        }
        $sql .=" ORDER BY id DESC ";
        if ($limit_size > 0)
        {
            $sql.=" LIMIT $offset,$limit_size";
        }
        $row_list = self::$db->fetch_all($sql);
        $count = self::$db->fetch_col('SELECT FOUND_ROWS()');
        return array($row_list, $count);
    }

    public function search_by_node($nodename)
    {
        $nodename = addslashes($nodename);
        $sql = "SELECT *,uc_mission.id AS mission_id,mission_node.id AS mission_node_id FROM "
                . " uc_mission_node LEFT JOIN uc_mission ON mission_node.mission_id=mission.id WHERE node='$nodename'
                AND status=" . self::STATUS_RUNNING;
        return self::$db->fetch_all($sql);
    }

    public function remove_signal($mission_id, $nodename)
    {
        $mission_id = intval($mission_id);
        $nodename = addslashes($nodename);
        $sql = "UPDATE uc_mission_node SET `signal`=NULL WHERE mission_id=$mission_id AND node='$nodename'";
        return self::$db->replace($sql);
    }

    public function stop_running($mission_id)
    {
        $mission_id = intval($mission_id);
        $sql = sprintf("UPDATE uc_mission SET status=%d WHERE id=$mission_id", self::STATUS_STOPED);
        return self::$db->replace($sql);
    }

    public function start_running($mission_id)
    {
        $mission_id = intval($mission_id);
        $sql = sprintf("UPDATE uc_mission SET status=%d WHERE id=$mission_id", self::STATUS_RUNNING);
        return self::$db->replace($sql);
    }

    public function append_mission_log($mission_id, $node, $pid, $stdout, $stderr)
    {
        $mission_id = intval($mission_id);
        $node = addslashes($node);
        $pid = intval($pid);
        $stdout = addslashes($stdout);
        $stderr = addslashes($stderr);
        $lastlog = $this->fetch_last_mission_log($mission_id, $node, $pid);
        if (empty($lastlog))
        {
            $sql = "INSERT INTO uc_mission_log SET mission_id=$mission_id,node='$node',pid=$pid,stdout='$stdout',stderr='$stderr',ctime=NOW(),utime=NOW()";
            return self::$db->insert($sql);
        } else
        {
            $stderr = $lastlog['stderr'] . "\n" . $stderr;
            $stdout = $lastlog['stdout'] . "\n" . $stdout;
            $sql = "UPDATE uc_mission_log SET stdout='$stdout',stderr='$stderr',utime=NOW() WHERE id={$lastlog['id']}";
            echo $sql;
            return self::$db->replace($sql);
        }
    }

    public function delete_mission_node($mission_id)
    {
        $mission_id = intval($mission_id);
        $sql = "DELETE FROM uc_mission_node WHERE mission_id=$mission_id";
        return self::$db->delete($sql);
    }

    public function add_mission_node($mission_id, $node)
    {
        $mission_id = intval($mission_id);
        $node = addslashes($node);
        $sql = "INSERT INTO uc_mission_node SET mission_id=$mission_id,node='$node'";
        return self::$db->insert($sql);
    }

    public function restart_running($mission_id)
    {
        $mission_id = intval($mission_id);
        $sql = sprintf("UPDATE uc_mission SET status=%d WHERE id=$mission_id", self::STATUS_RESTART);
        self::$db->replace($sql);
        $sql = "UPDATE uc_mission_node SET `signal`='kill' WHERE mission_id=$mission_id";
        return self::$db->replace($sql);
    }

    public function fetch_last_mission_log($mission_id, $node, $pid)
    {
        $mission_id = intval($mission_id);
        $node = addslashes($node);
        $pid = intval($pid);
        $last_time = date("Y-m-d H:i:s", time() - 60);
        $sql = "SELECT * FROM uc_mission_log WHERE mission_id=$mission_id AND pid=$pid AND node='$node' AND utime>'$last_time' ORDER BY id DESC LIMIT 1";
        return self::$db->fetch($sql);
    }

}
