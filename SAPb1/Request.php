<?php

namespace SAPb1;

class Request{
    
    protected $url;
    protected $sslOptions = [];
    protected $method = 'GET';
    protected $postParams = [];
    protected $cookies = [];
    
    public function __construct(string $url, array $sslOptions = []){
        $this->url = $url;
        $this->sslOptions = $sslOptions;
    }
    
    public function setMethod(string $method) : Request{
        $this->method = $method;
        return $this;
    }
    
    public function setPost(array $postParams) : Request{
        $this->postParams = $postParams;
        return $this;
    }
    
    public function setCookies(array $cookies) : Request{
        $this->cookies = $cookies;
        return $this;
    }

    public function getResponse() : Response{

        $postdata = json_encode($this->postParams);

        $header = "Content-Type: application/json\r\n";
        $header.= "Content-Length: " . strlen($postdata) . "\r\n";
        
        if(count($this->cookies) > 0){
            $header.= "Cookie: ";
            foreach($this->cookies as $name => $value){
                $header.= $name .'='. $value . ';';
            }
            $header.= "\r\n";
        }

        $options = array( 
            'http' => array(
                'ignore_errors' => true,
                'method'  => $this->method,
                'content' => $postdata,
                'header'  => $header,
            ),
            "ssl" => $this->sslOptions
        );
        
        set_error_handler(
            function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, $severity, $severity, $file, $line);
            }
        );
        
        try{
            $body = file_get_contents($this->url, false, stream_context_create($options));
            $response = $this->createResponse($body, $http_response_header);
            restore_error_handler();
            
        }catch(\ErrorException $e){
            restore_error_handler();
            throw $e;
        }
        
        return $response;
    }
    
    private function createResponse($body, $responseHeaders) : Response{
        
        $statusCode = 0;
        $headers = [];
        $cookies = [];

        foreach($responseHeaders as $idx => $header){

            if($idx == 0){
                $array = explode(' ', $header);
                $statusCode = $array[1];
                continue;
            }

            $array = explode(':', $header, 2);

            if(count($array) == 2){

                $cookie = [];
                $key = $array[0];
                $value = $array[1];

                if(array_key_exists($key, $headers)){
                    $prevValue = $headers[$key];

                    if(is_string($prevValue)){
                        $headers[$key] = [$prevValue, $value];
                    }
                    if(is_array($prevValue)){
                        $headers[$key][] = $value;
                    }

                }else{

                    if($key == 'Content-Type'){ 
                        $contentParts = explode(';', $value);
                        $headers['Content-Type'] = trim($contentParts[0]);
                    }else{
                        $headers[$key] = $value;
                    }
                }

                if($key == 'Set-Cookie'){
                    parse_str(strtr($value, array('&' => '%26', '+' => '%2B', ';' => '&')), $cookie); 
                    $cookies[key($cookie)] = reset($cookie);
                }
            }
        }

        return new Response($statusCode, $headers, $cookies, $body);
    }
}