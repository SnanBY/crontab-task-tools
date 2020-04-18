<?php
/**
 * RedisDriver.php
 * @des
 * Created by PhpStorm.
 * User: Liwenhua
 * Date: 2020/4/18
 * Time: 16:20
 */
namespace SnanWord\TaskTool\basis;

use Predis\Client;

class RedisClient
{
    use Singleton;

    private $client;

    private $config = [
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'password' => '',
        'database' => 1
    ];

    public function __construct(){
        $this->config = env_val('redis',$this->config);
        $this->connect();
    }

    public function get(){
        return $this->client;
    }

    private function connect(){
        $this->client = new Client($this->config);
        if(!$this->client->ping()){
            die('Redis is not connect');
        }
    }
}