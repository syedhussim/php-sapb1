<?php

namespace SAPb1\Filters;

class NotInArray extends Filter{
    
    private $field;
    private $collection;
    
    public function __construct($field, array $collection){
        $this->field = $field;
        $this->collection = $collection;
    }

    public function execute(){
        $group = '';

        foreach($this->collection as $idx => $value){
            $op = ($idx < count($this->collection)-1) ? ' and ' : '';
            $group .= $this->field . " ne " . $this->escape($value) . $op;
        }
        return '(' . $group . ')';
    }
}