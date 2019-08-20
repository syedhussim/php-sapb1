<?php

namespace SAPb1;

class Config{
    
    private $config = [];
    
    /**
     * Initializes a new instance of Config.
     */
    public function __construct(array $config){
        $this->config = $config;
    }
    
    /**
     * Gets the full URL to the service.
     */
    public function getServiceUrl(string $serviceName) : string{
        $scheme = $this->get('https') === true ? 'https' : 'http';  
        return $scheme . '://' . $this->get('host') . ':' .  $this->get('port', 50000) . '/b1s/v' . $this->get('version', 2) . '/' . $serviceName;
    }
    
    /**
     * Gets an array of SSL options.
     */
    public function getSSLOptions() : array{
        return $this->get('sslOptions', []);
    }
    
    /**
     * Gets the Config as an array.
     */
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