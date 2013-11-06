<?php
$_SRC = isset($argv[1]) ? $argv[1] : '../MarWeb/src/';
// Find beans
exec("find $_SRC -type f -name \*.java | grep -E \"/bean/|PageModel.java\"", $files);
$beans = array();
foreach($files as $f) {
	$p = explode('/', $f);
	$beanName = $p[count($p) - 1];
	$beanName = substr($beanName, 0, -5);
	if(isset($beans[$beanName]))
		die("duplicate $beanName\n");
	$beans[$beanName] = $f;
}

// Parse actions
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
        $c = preg_replace('/\/\*.+?\*\//s', '', $c);
        $c = preg_replace('/\/\/.+?[\n\r]/', '', $c);
        preg_match_all('/@RequestMapping\(.*?(".+?").*?\)[\s\n\r]*?public\s+.*?\s+([a-zA-Z0-9_]+?)\s*\(/', $c, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $functions = array();
        foreach($m as $m) {
            $pos = $m[0][1] + strlen($m[0][0]);
            $params = stackedContent($c, $pos, '(', ')');
            $params = preg_replace('/[\s\n\r]+/', ' ', trim($params));
            $pos = strpos($c, '{', $pos) + 1;
            $body = stackedContent($c, $pos, '{', '}');
            $functions[$m[2][0]] = array(
                'name' => $m[2][0],
                'path' => $m[1][0],
                'params' => $params,
                'body' => $body
            );
        }
        $f = substr($f, 0, -5);
        $classes[$f] = $functions;
    }
}
$known = array(
	'vers.get(0)' => 'AppVersion',
	'contactServiceAdv.getList(userId)' => 'List<Contact>',
	'*.getHeaderPic()' => 'String',
	'*.getUsername()' => 'String',
);
function findDeclareType($function, $var) {
	global $known;
    $found = false;
    $var = trim($var);
    if(strpos($var, '.') === false) {
        if(preg_match("/([a-zA-Z0-9_\<\>]+)\s+$var\b/", "($function[params]) { $function[body] }", $m)) {
            $found = $m[1];
        } else {
            echo ">$var\n";
            die("This is not normal\n");
        }
    } elseif(isset($known[$var])) {
    	return $known[$var];
    } else {
    	$vars = explode('.', $var);
    	if(isset($vars[1]) && substr($vars[1], 0, 9) == 'toString(')
    		return "String";
    	if(isset($known['*.' . trim($vars[1])]))
    		return $known['*.' . trim($vars[1])];
        print_r($vars);
    }
    return $found;
}
// parse return type
foreach($classes as $classkey=>$class) {
    foreach($class as $funckey=>$function) {
        $body = &$function['body'];
        $params = $function['params'];
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
                    $found = findDeclareType($function, $args);
                }
            }
            /*
            if(!$found) {
                echo "        $function[name]: can not find return type\n";
            } else {
                echo "        $function[name]: return type --> $found\n";
            }
            */
            if(preg_match('/List\<(.+?)\>/', $found, $m)) {
	            $found = $m[1] . '[]';
            }
            $function['return'] = $found;
            unset($function['body']);
			$class[$funckey] = $function;
			$found = str_replace('[]', '', $found);
			if($found && $found != 'String' && $found != 'int' && $found != 'boolean' && !isset($beans[$found])) {
				die("Bean $found not found\n");
			}
        } else {
	        unset($class[$funckey]);
        }
    }
    $classes[$classkey] = $class;
}

// parse input params
function replace_quotes($in) {
	return '(' . str_repeat('@', strlen($in[1])) . ')';
}
function startsWith($content, $find) {
	return substr($content, 0, strlen($find)) == $find;
}
foreach($classes as $classkey=>$class) {
    foreach($class as $funckey=>$function) {
		$param = $function['params'];
		$xparam = preg_replace_callback('/\((.+?)\)/s', 'replace_quotes', $param);
		$params = array();
		$pos = 0;
		$prev = 0;
		while(false !== ($pos = strpos($xparam, ',', $pos))) {
			$params[] = trim(substr($param, $prev, $pos - $prev));
			$pos++;
			$prev = $pos;
		}
		$params[] = trim(substr($param, $prev));
		foreach($params as $k=>$p) {
			if(startsWith($p, '@RequestBody'))
				$p = substr($p, strlen('@RequestBody'));
			if(startsWith($p, '@RequestHeader') || startsWith($p, 'ModelMap')) {
				unset($params[$k]);
			} elseif(startsWith($p, '@RequestParam')) {
				if(preg_match('/@RequestParam\((.*?(?:value="(.*?)")?.*?)\)\s*(.+?)\s+(.+?)\b/', $p, $m)) {
					$m[1] = trim($m[1]);
					$key = $m[2];
					if(!$key && $m[1]{0} == '"') {
						$key = substr($m[1], 1, -1);
					}
					if(!$key) {
						$key = $m[4];
					}
					$params[$k] = array('var'=>$m[4], 'name'=>$key, 'type'=>$m[3], 'isRequestParam'=>true);
				} else {
					echo "??? $p\n";
				}
			} else {
				$m = preg_split('/\s+/', trim($p));
				$isRP = !isset($beans[$m[0]]);
				$params[$k] = array('var'=>$m[1], 'name'=>$m[1], 'type'=>$m[0], 'isRequestParam'=>$isRP);
			}
		}
		$function['params'] = $params;
		$class[$funckey] = $function;
	}
    $classes[$classkey] = $class;
}

// Generate pacakge
// Copy beans
foreach($beans as $beanName=>$file) {
	$c = file_get_contents($file);
	$c = preg_replace('/package .*?;/', "package bean.$beanName;", $c);
	$c = preg_replace('/import cc\.ccme\..*?\.bean\..*?;/', '', $c);
	file_put_contents("bean/$beanName.java", $c);
}
// generate service
foreach($classes as $className=>$class) {
	$c = "package service;\n";
	$c .= "public final class $className {\n";
	foreach($class as $function) {
		$params = array();
		foreach($function['params'] as $p) {
			$params[] = "$p[type] $p[var]";
		}
		$params = implode(', ', $params);
		$cmfname = $function['name'];
		$cmfname = strtoupper($cmfname{0}) . substr($cmfname, 1);
		$c .= "\t\tpublic static interface On{$cmfname}SuccessListener() {\n";
		$arg = $function['return'] ? "$function[return] result" : '';
		$c .= "\t\t\ton{$cmfname}Success($arg);\n";
		$c .= "\t\t}\n\n";
		$c .= "\t\tpublic static RequestObject<On{$cmfname}SuccessListener> $function[name]($params) {\n";
		$c .= "\t\t\tRequestObject<On{$cmfname}SuccessListener> obj = new RequestObject<On{$cmfname}SuccessListener>();\n";
		$c .= "\t\t\tobj.setListenerInterfaceClass(On{$cmfname}SuccessListener.class);\n";
		$c .= "\t\t\tobj.setUrl(RequestObject.baseUrl + $function[path] + \".json\");\n";
		$s = false;
		foreach($function['params'] as $p) {
			if($p['isRequestParam'])
				$c .= "\t\t\tobj.addParam(\"$p[name]\", $p[var]);\n";
			else {
				$c .= "\t\t\tobj.setObj($p[var]);\n";
				if($s)
					echo 'duplicate !!!';
				$s = true;
			}
		}
		$c .= "\t\t\tobj.enque();\n";
		$c .= "\t\t\treturn obj;\n";
		$c .= "\t\t}\n";
	}
	$c .= "}\n";
	file_put_contents("service/$className.java", $c);
}
