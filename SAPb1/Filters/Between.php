<?php

namespace SAPb1\Filters;

class Between extends Filter{
    
    private $field;
    private $fromValue;
    private $toValue;

    public function __construct($field, $fromValue, $toValue){
        $this->field = $field;
        $this->fromValue = $fromValue;
        $this->toValue = $toValue;
    }

    public function execute(){
        return '(' . $this->field . ' ge ' . $this->escape($this->fromValue) . ' and ' . $this->field . ' le ' . $this->escape($this->toValue) . ')';
    }
}