<?php
include_once 'logic/global/request.php';
class DB {
    private $conn = NULL;
    private $result = NULL;
    private $src = NULL;
    private $sql = '';
    
    public function __destruct() {
        $this->close();
    }

    public function close() {
        if(NULL == $this->conn) {
            return;
        }
        $this->freeResult();
        mysql_close($this->conn);
        $this->conn = NULL;
    }

    public function connect() {
        if($this->conn != NULL)
            return;
        global $R;
        $this->setDataSrc($R);
        $conn = mysql_connect(DBHOST, DBUSER, DBPASS);
        if(!$conn)
            die('Can not connect to database');
        mysql_select_db(DBNAME, $conn);
        mysql_query('SET NAMES \'utf8\'', $conn);
        $this->conn = $conn;
    }

    public function freeResult() {
        if($this->result != NULL) {
            mysql_free_result($this->result);
            $this->result = NULL;
        }
    }

    public function setDataSrc(&$src) {
        $this->src = &$src;
    }

    public function escape($data) {
        if(is_numeric($data)) {
            return $data;
        } else if(is_bool($data)) {
            return $data ? 1 : 0;
        } else if(is_array($data)) {
            $ret = array();
            foreach($data as $f) {
                $ret[] = $this->escape($f);
            }
            return '(' . implode('|', $ret) . ')';
        } else
            return '\'' . mysql_real_escape_string($data) . '\'';
    }

    public function query($sql, $src = NULL) {
        $this->connect();
        $this->freeResult();
        $sql = str_replace('%%', DBPREFIX, $sql);
        $match = NULL;
        if($src == NULL)
            $src = &$this->src;
        else if(is_array($src))
            $src = new Map($src);
        if($src != NULL)
            while(preg_match('/\{\$(.+?)\}/', $sql, $match)) {
                $val = $src->get($match[1]);
                if(NULL == $val)
                    $val = '\'\'';
                else
                    $val = $this->escape($val);
                $sql = str_replace($match[0], $val, $sql);
            }
        $this->sql = $sql;
        $ret = mysql_query($sql, $this->conn);
        if(0 != mysql_errno()) {
            trigger_error(htmlentities(mysql_error()));
            echo '<pre>';
            echo $sql . '<br/>';
            print_r(debug_backtrace());
            echo '</pre>';
        }
        $this->result = is_bool($ret) ? NULL : $ret;
        return $ret;
    }

    public function lastSql() {
        return $this->sql;
    }

    public function count() {
        if(NULL == $this->result)
            return 0;
        return mysql_num_rows($this->result);
    }

    public function next() {
        if(NULL == $this->result)
            return NULL;
        $row = mysql_fetch_assoc($this->result);
        if(!$row) {
            $row = NULL;
            $this->freeResult();
        }
        return $row;
    }
    
    public function flush() {
        $ret = array();
        while(NULL != ($row = $this->next())) {
            $ret[] = $row;
        }
        return $ret;
    }

    public function insertId() {
        return mysql_insert_id($this->conn);
    }

    public function affected() {
        return mysql_affected_rows();
    }
}

$DB = new DB();
