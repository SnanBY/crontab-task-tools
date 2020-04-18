<?php
/**
 * Singleton.php
 * @des
 * Created by PhpStorm.
 * User: Liwenhua
 * Date: 2020/4/18
 * Time: 17:34
 */

namespace SnanWord\TaskTool\basis;


trait Singleton
{
    private static $instance;

    static function getInstance(...$args)
    {
        if(!isset(self::$instance)){
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
}