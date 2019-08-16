<?php

namespace SAPb1;

class Config{
    
    private $config = [];
    
    public function __construct(array $config){
        $this->config = $config;
    }
    
    public function getServiceUrl(string $serviceName) : string{
        $scheme = $this->get('https') === true ? 'https' : 'http';  
        return $scheme . '://' . $this->get('host') . ':' .  $this->get('port', 50000) . '/b1s/v' . $this->get('version', 2) . '/' . $serviceName;
    }
    
    public function getSSLOptions(){
        return $this->get('sslOptions', []);
    }
    
    public function toArray() : array{
        return $this->config;
    }

    private function get(string $name, $default = null){
        if(array_key_exists($name, $this->config)){
            return $this->config[$name];
        }
        return $default;
    }
}