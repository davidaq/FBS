<?php
class MAP {
    private $data = array();

    private $cache = array();

    public function __construct($data = NULL) {
        $this->set($data);
    }
    
    public function set($data) {
        if(!is_array($data))
            return;
        $this->cache = array();
        foreach($data as $k=>$v) {
            $path = explode('.', str_replace('_', '.', $k));
            $expr = '$this->data';
            foreach($path as $f) {
                $f = str_replace('\'', '', $f);
                $expr .= '[\'' . $f . '\']';
            }
            $expr .= '=$v;';
            eval($expr);
        }
    }

    public function get($key) {
        $key = str_replace('\'', '', $key);
        if(!isset($this->cache[$key])) {
            $this->cache[$key] = $this->_get($key);
        }
        return $this->cache[$key];
    }

    private function _get($key) {
        $path = explode('.', $key);
        $expr = '$this->data';
        foreach($path as $f) {
            $expr .= '[\'' . $f . '\']';
        }
        $expr = 'if(isset(' . $expr . ' )){return ' . $expr . ';}else{return NULL;}';
        return eval($expr);
    }

    public function dump($json = false) {
        if($json)
            return json_encode($this->data);
        else
            var_dump($this->data);
    }
}
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}
$R = new MAP();

class RequestSave {
    private $data = array();
    public function encode($data) {
        $data = base64_encode(json_encode($data));
        $c = strlen($data);
        for($i = 0; $i < $c; $i++) {
            $m = ord($data{$i});
            $m = $m % 2 == 0 ? $m + 1 : $m - 1;
            $data{$i} = chr($m);
        }
        return $data;
    }
    private function decode($data) {
        $c = strlen($data);
        for($i = 0; $i < $c; $i++) {
            $m = ord($data{$i});
            $m = $m % 2 == 0 ? $m + 1 : $m - 1;
            $data{$i} = chr($m);
        }
        $data = base64_decode($data);
        return json_decode($data, true);
    }
    public function __construct() {
        if(isset($_COOKIE['saved']))
            $this->data = $this->decode($_COOKIE['saved']);
    }
    public function __destruct() {
        //setcookie('saved', $this->encode($this->data), strtotime('+1 year'), CWD);
        setcookie('saved', $this->encode($this->data), strtotime('+1 year'));
    }
    public function data() {
        return $this->data;
    }
    public function save($key) {
        global $R;
        $this->data[$key] = $R->get($key);
    }
    public function unsave($key) {
        if(isset($this->data[$key]))
            unset($this->data[$key]);
    }
}
$RS = new RequestSave();
$R->set($RS->data());
$R->set($_GET);
$R->set($_POST);
function R($key) {
    global $R;
    return $R->get($key);
}
function RS($key) {
    global $RS;
    if($RS != NULL)
        $RS->save($key);
}
function RS_($key) {
    global $RS;
    if($RS != NULL)
        $RS->unsave($key);
}
