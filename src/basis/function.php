<?php
/**
 * function.php
 * @des
 * Created by PhpStorm.
 * User: Liwenhua
 * Date: 2020/4/18
 * Time: 18:01
 */

function env_val($name, $default = '')
{
    if (isset($_ENV[$name])) return $_ENV[$name];
    return $default;
}

function write_log($str, $type)
{
    return error_log($str . PHP_EOL, SNANTASK_ROOT . '/log/' . $type . '/' . date('Y-m-d') . '/eroor.log');
}

function exception_handler($e)
{
    $str = 'An error occurred at ' . date('Y-m-d H:i:s') . ' o\'clock' . PHP_EOL;
    $str .= 'error_message:' . $e->getMessage() . PHP_EOL;
    $str .= 'error_file:' . $e->getFile() . PHP_EOL;
    $str .= 'error_line:' . $e->getLine() . PHP_EOL;
    write_log($str, 'error');
    if (env_val('deamon', true)) {
        var_dump($str);
    }
    return true;
}


function get_color_text($colorCode, $string)
{
    $colorCode = (string)$colorCode;
    $cmd       = "echo -ne \"\033[" . $colorCode . "m" . $string . " \033[0m\n\"";
    return exec($cmd);
}

/**
 * 14 　　* 驼峰命名转下划线命名
 * 15 　　* 思路:
 * 16 　　* 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 * 17 　　*/
function uncamelize($camelCaps, $separator = '_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}