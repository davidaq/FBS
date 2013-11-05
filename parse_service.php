<?php
$_SRC = $argv[1];
$_PATH = 'cc/ccme/mar/inf/action';
$files = scandir("$_SRC/$_PATH");
function stackedContent($context, $start, $o, $e) {
    $pos = $start;
    $s = 1;
    while($s > 0) {
        if($context{$pos} == $e) {
            $s--;
        } elseif ($context{$pos} == $o) {
            $s++;
        }
        $pos++;
    }
    return substr($context, $start, $pos - $start - 1);
}
$classes = array();
foreach($files as $f) {
    if('.java' == substr($f, -5)) {
        $c = file_get_contents("$_SRC/$_PATH/$f");
        preg_match_all('/@RequestMapping\(.*?(".+?").*?\)[\s\n\r]*?public\s+.*?\s+([a-zA-Z0-9_]+?)\s*\(/', $c, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $functions = array();
        foreach($m as $m) {
            $pos = $m[0][1] + strlen($m[0][0]);
            $params = stackedContent($c, $pos, '(', ')');
            $params = preg_replace('/[\s\n\r]+/', ' ', trim($params));
            $pos = strpos($c, '{', $pos) + 1;
            $body = stackedContent($c, $pos, '{', '}');
            $functions[] = array(
                'name' => $m[2][0],
                'path' => $m[1][0],
                'params' => $params,
                'body' => $body
            );
        }
        $classes[$f] = $functions;
    }
}
function findDeclareType(&$function, $var) {
    $found = false;
    if(strpos($var, '.') === false) {
        if(preg_match("/([a-zA-Z0-9_\<\>]+)\s+$var\b/", "($function[params]) { $function[body] }", $m)) {
            $found = $m[1];
        } else {
            echo ">$var\n";
        }
    } else {
        //print_r(explode('.', $args));
    }
    return $found;
}
foreach($classes as $classkey=>$class) {
    echo "$classkey\n";
    foreach($class as $funckey=>$function) {
        $body = &$function['body'];
        $params = &$function['params'];
        if(strpos($body, 'ResMsg') !== false) {
            $found = false;
            if(preg_match('/ResMsg\s+?([a-zA-Z0-9_]+?)\b/', $body, $b)) {
                if(preg_match("/$b[1]\s*\.\s*setObj\((.+?)\)/", $body, $b, PREG_OFFSET_CAPTURE)) {
                    $args = stackedContent($body, $b[1][1], '(', ')');
                    $found = findDeclareType($function, $args);
                }
            } 
            if(!$found && preg_match('/new\s+ResMsg\s*\((.+?)\)/', $body, $b, PREG_OFFSET_CAPTURE)) {
                $args = stackedContent($body, $b[1][1], '(', ')') . "\n";
                $args = explode(',', $args);
                if(isset($args[2])) {
                    unset($args[0]);
                    unset($args[1]);
                    $args = implode(',', $args);
                    findDeclareType($function, $args);
                }
            }
            if(!$found) {
                echo "        $function[name]: can not find return type\n";
            } else {
                echo "        $function[name]: return type --> $found\n";
            }
        }
        $class[$funckey] = $function;
    }
    $classes[$classkey] = $class;
}
