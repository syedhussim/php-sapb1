<?php

namespace SAPb1\Filters;

class EndsWith extends Filter{
    
    private $field;
    private $value;
    
    public function __construct($field, $value){
        $this->field = $field;
        $this->value = $value;
    }

    public function execute(){
        return 'endswith(' . $this->field . "," . $this->escape($this->value) . ")";
    }
}