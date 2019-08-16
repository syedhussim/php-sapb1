<?php

namespace SAPb1\Filters;

class InArray extends Filter{
    
    private $field;
    private $collection;
    
    public function __construct($field, array $collection){
        $this->field = $field;
        $this->collection = $collection;
    }

    public function execute(){
        $group = '';

        foreach($this->collection as $idx => $value){
            $op = ($idx < count($this->collection)-1) ? ' or ' : '';
            $group .= $this->field . " eq " . $this->escape($value) . $op;
        }
        return '(' . $group . ')';
    }
}