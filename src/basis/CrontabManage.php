<?php
/**
 * Crontab.php
 * @des
 * Created by PhpStorm.
 * User: Liwenhua
 * Date: 2020/4/18
 * Time: 23:23
 */

namespace SnanWord\TaskTool\basis;

use Crontab\Crontab;
use Crontab\Job;


class CrontabManage
{
    use Singleton;
    private $crontab;
    private $job;

    public function __construct()
    {
        $this->crontab = new Crontab();
        $this->job = new Job();

    }

    /**
     * setJob
     * @des   设置crontab
     * @param $config
     * @return Crontab
     * @author Snan
     */
    public function setJob($config)
    {
        $this->job
            ->setMinute($config[0])
            ->setHour($config[1])
            ->setDayOfMonth($config[2])
            ->setMonth($config[3])
            ->setDayOfWeek($config[4])
            ->setCommand($config[5])
        ;
        $this->crontab->addJob($this->job);
        return $this->crontab->write();
    }

    public function delete($config){
        $this->job
            ->setMinute($config[0])
            ->setHour($config[1])
            ->setDayOfMonth($config[2])
            ->setMonth($config[3])
            ->setDayOfWeek($config[4])
            ->setCommand($config[5])
        ;
        $this->crontab->addJob($this->job);
        $this->crontab->removeJob($this->job);
        return $this->crontab->write();
    }

    public function render(){
        return $this->crontab->render();
    }
}