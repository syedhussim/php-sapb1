<?php

namespace SAPb1;

class Response{
    
    protected $statusCode;
    protected $headers;
    protected $cookies;
    protected $body;

    public function __construct(int $statusCode, array $headers = [], array $cookies = [], string $body = ''){
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->body = $body;
    }
    
    public function getStatusCode() : int{
        return $this->statusCode;
    }
    
    public function getHeaders(string $header = ''){
        if($header){
            if(array_key_exists($header, $this->headers)){
                return $this->headers[$header];
            }
        }
        return $this->headers;
    }
    
    public function getCookies() : array{
        return $this->cookies;
    }
    
    public function getBody() : string{
        return $this->body;
    }

    public function getJson() : object{
        return json_decode($this->body);
    }
}