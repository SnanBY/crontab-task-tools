<?php
/**
 * Manage.php
 * @des
 * Created by PhpStorm.
 * User: Liwenhua
 * Date: 2020/4/18
 * Time: 11:28
 */

namespace SnanWord\TaskTool;


use Predis\Client;
use SnanWord\TaskTool\basis\CrontabManage;
use SnanWord\TaskTool\basis\Singleton;

class Manage
{
    use Singleton;

    private $db;
    private $param;
    private $task_list;
    private $redis_task_key = 'snan_word_task_list';
    private $run_task;

    public function __construct(Client $db)
    {
        $this->param     = env_val('param');
        $this->task_list = env_val('task_list', []);
        $this->db        = $db;
        $runTask         = $this->db->get($this->redis_task_key);
        $this->run_task  = $runTask ? json_decode($runTask, true) : [];
    }

    public function start()
    {
        return $this->addTask();
    }

    public function stopTask(){
        return $this->killTask();
    }

    /**
     * addTask
     * @des    插入task
     * @return string
     * @author Snan
     */
    public function addTask()
    {
        if ($this->param) {
            $this->task_list = [$this->param => 1];
        }
        $insTask = [];
        foreach ( $this->task_list as $k => $v ) {
            $taskStatus = $this->taskStatus($k);
            if ($taskStatus !== true) {
                echo $taskStatus;
                continue;
            }
            $taskInfo = $this->keyToClass($k);
            if (!$taskInfo) {
                echo get_color_text(31,'task '.$this->param.' does not exist');
                continue;
            }
            //写入crontab
            $crontabRule = $this->getCrontabRule($taskInfo);
            if (!is_array($crontabRule)) {
                //记录日志
                echo get_color_text(31, 'warning：Task' . $taskInfo['class'] . 'setCrontab returns abnormal format!' . PHP_EOL);
                continue;
            }
            $crontabRule[5] = 'php ' . SNAN_BIN_PATH . ' run ' . $taskInfo['class'];
            CrontabManage::getInstance()->delete($crontabRule);
            CrontabManage::getInstance()->setJob($crontabRule);
            $insTask[$k] = $taskInfo['path'];
        }
        $re = $this->db->set($this->redis_task_key, json_encode(array_merge($this->run_task, $insTask)));
        if(!$re) return get_color_text(31,'Redis failed to write task data');
        return $this->taskList(true);
    }

    public function taskList($ref = false)
    {
        if($ref) $runTask = $this->db->get($this->redis_task_key);$this->run_task  = $runTask ? json_decode($runTask, true) : [];
        $notStopList = array_diff(array_keys($this->task_list), array_keys($this->run_task));
        $colorRun    = get_color_text(32, 'run');
        $colorStop   = get_color_text(31, 'stop');
        $colorHg     = get_color_text(32, '|');
        $colorHr     = get_color_text(31, '|');
        print get_color_text(34,'Task list status:').PHP_EOL;
        foreach ( $this->run_task as $k => $v ) {
            print $colorHg . 'task_name:' . $k . $colorHg . 'task_class:' . $v . $colorHg . 'status:' . $colorRun . PHP_EOL;
        }
        foreach ( $notStopList as $v ) {
            print $colorHr . 'task_name:' . $v . $colorHr . 'status:' . $colorStop . PHP_EOL;
        }
        return get_color_text(32,'success');
    }

    public function killTask()
    {
        $taskStatus = $this->taskStatus($this->param,false);
        if($taskStatus!==true){
            if($this->param!=='all') return $taskStatus;
            $killList = $this->run_task;
        }else{
            $killList = [$this->param=>$this->keyToClass($this->param)['path']];
        }
        $newTask = $this->run_task;
        foreach($killList as $k=>$v){
            $crontabRule = $this->getCrontabRule($this->keyToClass($k));
            CrontabManage::getInstance()->delete($crontabRule);
            unset($newTask[$k]);
        }
        $re = $this->db->set($this->redis_task_key, json_encode($newTask));
        if(!$re) return get_color_text(31,'Redis failed to write task data');
        return $this->taskList(true);
    }

    public function runTask()
    {
        $taskStatus = $this->taskStatus($this->param);
        if ($taskStatus !== true) {
            echo $taskStatus;
            die;
        }
        if(!$this->keyToClass($this->param)){
            echo get_color_text(31,'task '.$this->param.' does not exist');
            die;
        }
        call_user_func(array($this->keyToClass($this->param)['path'], 'run'));
    }

    public function taskStatus($param = '',$checkRun = true)
    {
        if (!$param) $param = $this->param;
        if (!$param) return get_color_text(31, 'Please pass the parameter: task key') . PHP_EOL;

        if (!isset($this->task_list[$param])) {
            return get_color_text(31, 'Please add a task to the configuration file the task name is' . $param) . PHP_EOL;;
        }

        if (!$this->task_list[$param]) {
            return get_color_text(31, 'task ' . $param . ' is not started') . PHP_EOL;
        }

        if (isset($this->run_task[$param]) && $checkRun) {
            return get_color_text(31, 'task ' . $param . ' has been started') . PHP_EOL;
        }

        return true;
    }

    public function keyToClass($key){
        $taskName = implode('', array_map(function ($v) {
            return ucfirst($v);
        }, explode('_', $key)));
        $taskFile = '\\SnanWord\\TaskTool\\task\\' . $taskName;
        if (!class_exists($taskFile)) {
            return false;
        }
        return [
            'class'=>$taskName,
            'path'=>$taskFile,
        ];
    }

    /**
     * getCrontabRule
     * @des   获取crontab配置
     * @param $taskInfo
     * @return mixed|string
     * @author Snan
     */
    public function getCrontabRule($taskInfo){
        $crontabRule = call_user_func(array($taskInfo['path'], 'setCrontab'));
        if (!$crontabRule || count($crontabRule) != 5) {
            //记录日志
            return get_color_text(31, 'warning：Task' . $taskInfo['class'] . 'setCrontab returns abnormal format!' . PHP_EOL);
        }
        $crontabRule[5] = 'php ' . SNAN_BIN_PATH . ' run ' . $taskInfo['class'];
        return $crontabRule;
    }
}