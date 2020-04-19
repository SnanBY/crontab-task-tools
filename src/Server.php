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
        'init'  => '',
        'help'  => '',
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
     * @des   Scheduling function
     * @param $argv
     * @return mixed
     * @author Snan
     */
    public function run($argv)
    {
        if (!isset($argv[0]) || !array_key_exists($argv[0], $this->active) || $argv[0] == 'help') {
            $this->help();
            die;
        }
        if ($argv[0] == 'init') {
            return $this->init();
        }
        $action = $this->active[$argv[0]];
        return $this->manage->$action();
    }

    public function help()
    {
        echo get_color_text(34, 'ERROR:Please select the following method to execute the code' . PHP_EOL);
        echo get_color_text(31, 'Commond:' . implode(' | ', $this->active) . '' . PHP_EOL);
        die();
    }

    public function init()
    {
        if (!file_exists(SNANTASK_ROOT . '/snantask.php')) {
            $dir = opendir(SNANTASK_ROOT);
            copy(SNAN_BIN_PATH, SNANTASK_ROOT . '/snantask.php');
            closedir($dir);
        }
        return get_color_text(32, 'You have initialized, please check in the root directory of the project') . PHP_EOL;
    }
}