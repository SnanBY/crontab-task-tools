#!/usr/bin/env php
<?php

defined('IN_PHAR') or define('IN_PHAR', boolval(\Phar::running(false)));
defined('SNANTASK_ROOT') or define('SNANTASK_ROOT', IN_PHAR ? \Phar::running() : realpath(getcwd()));
define('SNAN_BIN_PATH',__FILE__);
$file = SNANTASK_ROOT . '/vendor/autoload.php';
if (file_exists($file)) {
    require $file;
} else {
    die("include composer autoload.php fail\n");
}
//set_exception_handler('exception_handler');

if (is_file(SNANTASK_ROOT . '/.env')) {
    $env = parse_ini_file(SNANTASK_ROOT . '/.env', true);
    foreach ($env as $key => $val) {
        $name = strtolower($key);
        $newVal = [];
        if(is_array($val)){
            foreach($val as $k=>$v){
                $item = $name.'.'.strtolower($k);
                $_ENV[$item] = $v;
                $newVal[strtolower($k)] = $v;
            }
        }
        if(!empty($newVal)){
            $_ENV[$name]=$newVal;
        }else{
            $_ENV[$name]=$val;
        }
    }
}

$args = $argv;
array_shift($args);
$_ENV['param'] = isset($args[1])?$args[1]:false;
if(env_val('deamon',false)){
    error_reporting(0);
}
$ret = \SnanWord\TaskTool\Server::getInstance()->run($args);
if (!empty($ret)) {
    if(!file_exists(SNANTASK_ROOT.'/snantask.php')){
        $dir = opendir(SNANTASK_ROOT);
        copy(SNAN_BIN_PATH, SNANTASK_ROOT.'/snantask.php');
        closedir($dir);
        echo get_color_text(32,'Initialization successful').PHP_EOL;
    }
    echo $ret . "\n";
}