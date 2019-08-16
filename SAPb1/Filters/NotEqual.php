<?php

namespace SAPb1\Filters;

class NotEqual extends Filter{
    
    private $field;
    private $value;
    
    public function __construct($field, $value){
        $this->field = $field;
        $this->value = $value;
    }

    public function execute(){
        return $this->field . " ne " . $this->escape($this->value);
    }
}