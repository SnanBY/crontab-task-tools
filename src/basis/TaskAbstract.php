<?php
/**
 * TaskInterface.php
 * @des
 * Created by PhpStorm.
 * User: Liwenhua
 * Date: 2020/4/18
 * Time: 18:42
 */

namespace SnanWord\TaskTool\basis;


abstract class TaskAbstract
{
    private $task_name    = __CLASS__;

    /**
     * run
     * @des    脚本运行代码
     * @return mixed
     * @author Snan
     */
    abstract public function run();

    /**
     * setCrontab
     * @des    设置crontab时间 //[,分,时,日,月,周]
     * @return array
     * @author Snan
     */
    public function setCrontab(){
        return ['*','0','*','*','*'];
    }
}