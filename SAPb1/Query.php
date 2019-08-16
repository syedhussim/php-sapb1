<?php

namespace SAPb1;

class Query{
    
    private $config;
    private $session;
    private $serviceName;
    private $query = [];
    private $filters = [];

    public function __construct(Config $config, array $session, string $serviceName){
        $this->config = $config;
        $this->session = $session;
        $this->serviceName = $serviceName;
    }
    
    public function select(string $fields = '*') : Query{
        $this->query['select'] = $fields;
        return $this;
    }
    
    public function limit(int $top, int $skip = 0) : Query{
        $this->query['top'] = $top;
        $this->query['skip'] = $skip;
        return $this;
    }
    
    public function orderBy(string $field, string $direction = 'asc') : Query{
        $this->query['orderby'] = $field . ' ' . $direction;
        return $this;
    }
    
    public function inlineCount() : Query{
        $this->query['inlinecount'] = 'allpages';
        return $this;
    }
    
    public function where(Filters\Filter $filter) : Query{
        $filter->setOperator('and');
        $this->filters[] = $filter;
        return $this;
    }
    
    public function orWhere(Filters\Filter $filter) : Query{
        $filter->setOperator('or');
        $this->filters[] = $filter;
        return $this;
    }
    
    public function expand($name){
        $this->query['expand'] = $name;
        return $this;
    }

    /** 
     * Returns a count of entities.
     */
    public function count() : int{
        return $this->doRequest('/$count');
    }
    
    public function find($id) : object{
        
        if(is_string($id)){
            $id = "'" . str_replace("'", "''", $id) . "'";
        }
        
        return $this->doRequest('(' . $id . ')');
    }
    
    public function findAll(callable $callback = null) : object{
        return $this->doRequest('', $callback);
    }
    
    private function doRequest(string $action = '', callable $callback = null){

        $requestQuery = '?';
        
        foreach($this->query as $name => $value){
            $requestQuery .= '$' . $name . '=' . rawurlencode($value) . '&';
        }

        if(count($this->filters) > 0){
            $requestQuery .= '$filter=';
            foreach($this->filters as $idx => $filter){
                $op = ($idx > 0) ? ' ' . $filter->getOperator() . ' ' : '';
                $requestQuery .= rawurlencode($op . $filter->execute());
            }
        }

        $request = new Request($this->config->getServiceUrl($this->serviceName . $action) . $requestQuery, $this->config->getSSLOptions());
        $request->setMethod('GET');
        $request->setCookies($this->session);
        $response = $request->getResponse();

        if($response->getStatusCode() === 200){

            if($response->getHeaders('Content-Type') == 'text/plain'){
                return $response->getBody();
            }
            elseif ($response->getHeaders('Content-Type') == 'application/json'){
                
                
                $result = $response->getJson();
                
                if(null != $callback){
                    if(isset($result->value)){
                        foreach($result->value as $index => $item){
                            $callback($item, $index);
                        }
                    }
                }
                
                return $result;
            }
        }else{
            throw new SAPException($response);
        }
    }
}