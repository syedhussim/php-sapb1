<?php

namespace SAPb1\Filters;

class StartsWith extends Filter{
    
    private $field;
    private $value;
    
    public function __construct($field, $value){
        $this->field = $field;
        $this->value = $value;
    }

    public function execute(){
        return 'startswith(' . $this->field . "," . $this->escape($this->value) . ")";
    }
}