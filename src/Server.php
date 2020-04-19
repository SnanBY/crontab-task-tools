<?php
/**
 * Server.php
 * @des
 * Created by PhpStorm.
 * User: Snan
 * Date: 2020/4/18
 * Time: 11:26
 */

namespace SnanWord\TaskTool;

use SnanWord\TaskTool\basis\CrontabManage;
use SnanWord\TaskTool\basis\RedisClient;
use SnanWord\TaskTool\basis\Singleton;
use SnanWord\TaskTool\Manage;

class Server
{
    use Singleton;
    private $active = [
        'start' => 'start',
        'add'   => 'addTask',
        'list'  => 'taskList',
        'kill'  => 'killTask',
        'stop'  => 'stopTask',
        'run'   => 'runTask',
        'help',
    ];
    private $db;
    private $manage;

    /**
     * init server
     * Server constructor.
     */
    public function __construct()
    {
//        var_dump(CrontabManage::getInstance()->setJob(['*','*','*','1','*','netstat -natp']));
//        var_dump(CrontabManage::getInstance([])->render());
//        var_dump(CrontabManage::getInstance()->delete(['*','*','*','1','*','netstat -natp']));
        $this->db     = RedisClient::getInstance()->get();
        $this->manage = Manage::getInstance($this->db);
    }

    /**
     * run
     * @des   run server
     * @param $argv
     * @return mixed
     * @author Snan
     */
    public function run($argv)
    {
        if (!isset($argv[0]) || !array_key_exists($argv[0], $this->active) || $argv[0]=='help') {
            $this->help();
            die('You need to enter the start method:'.implode(' | ',$this->active));
        }
        $action        = $this->active[$argv[0]];
        return $this->manage->$action();
    }

    public function help()
    {
        echo 'ERROR:Please select the following method to execute the code'.PHP_EOL;
        echo 'Commond:'.implode(' | ',$this->active).''.PHP_EOL;
        die();
    }
}