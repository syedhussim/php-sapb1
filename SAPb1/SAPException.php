<?php

namespace SAPb1;

class SAPException extends \Exception{
    
    protected $statusCode;
    
    /**
     * Initializes a new instance of SAPException.
     */
    public function __construct(Response $response){
        $this->statusCode = $response->getStatusCode();
        $message = '';
        $erroCode = $this->code;

        if($response->getHeaders('Content-Type') == 'text/html'){
            $message = $response->getBody();
        }

        if($response->getHeaders('Content-Type') == 'application/json'){
            $message = $response->getJson()->error->message->value;
            $erroCode = $response->getJson()->error->code;
        }
        
        parent::__construct($message, $erroCode);
    }
    
    public function getStatusCode() : int{
        return $this->statusCode;
    }
}
