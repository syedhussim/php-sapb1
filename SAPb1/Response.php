<?php

namespace SAPb1;

/**
 * Encapsulates an SAP B1 HTTP response.
 */
class Response{
    
    protected $statusCode;
    protected $headers;
    protected $cookies;
    protected $body;

    /**
     * Initializes a new instance of Response.
     */
    public function __construct(int $statusCode, array $headers = [], array $cookies = [], string $body = ''){
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->body = $body;
    }
    
    /**
     * Gets the response status code.
     */
    public function getStatusCode() : int{
        return $this->statusCode;
    }
    
    /**
     * Gets an array of response headers. If $header is specified and $header
     * exists then returns the value of the $header key.
     */
    public function getHeaders(string $header = ''){
        if($header){
            if(array_key_exists($header, $this->headers)){
                return $this->headers[$header];
            }
        }
        return $this->headers;
    }
    
    /**
     * Gets an array of response of cookies.
     */
    public function getCookies() : array{
        return $this->cookies;
    }
    
    /**
     * Gets the response body.
     */
    public function getBody() : string{
        return $this->body;
    }

    /**
     * Returns the response body as an object.
     */
    public function getJson() : object{
        if($this->body){
            return json_decode($this->body);
        }
        return new \std();
    }
}