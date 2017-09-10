<?php
/*
 * uctracker project
 *
 * Copyright 2017 xiebojie@qq.com
 * Licensed under the Apache License v2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 */
class logger_model extends model
{
    protected $primary_table = 'uc_logger_error';
    protected $primary_key = 'id';
    
    
    public function search_nginx_list($filter_where,$offset=0,$limit=30)
    {
        $offset = abs($offset);
        $limit_size = abs($limit_size);
        $sql = sprintf("SELECT SQL_CALC_FOUND_ROWS * FROM uc_logger_nginx ");
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
    
}