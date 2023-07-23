<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Service;

use Predis\Client;

class RedisService {

    private $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    public function checkIfKeyExists($key)
    {
        return $this->redis->exists($key);
    }
    
    public function setValueInRedis($key, $value)
    {
        $this->redis->set($key, $value);
    }

    public function getValueFromRedis($key)
    {
        
        $value = $this->redis->get($key);

        return $value;
    }
    
    public function deleteAll(){
        $this->redis->flushdb();
    }
}
