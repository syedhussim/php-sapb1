<?php

namespace SAPb1;

class Query{
    
    private $config;
    private $session;
    private $serviceName;
    private $query = [];
    private $filters = [];
    private $headers = [];

    /**
     * Initializes a new instance of Query.
     */
    public function __construct(Config $config, array $session, string $serviceName, array $headers){
        $this->config = $config;
        $this->session = $session;
        $this->serviceName = $serviceName;
        $this->headers = $headers;
    }
    
    /**
     * Specifies the fields to return. Returns the current Query instance.
     */
    public function select(string $fields = '*') : Query{
        $this->query['select'] = $fields;
        return $this;
    }
    
    /**
     * Specifies how many results to return and how many results to skip. 
     * Returns the current Query instance.
     */
    public function limit(int $top, int $skip = 0) : Query{
        $this->query['top'] = $top;
        $this->query['skip'] = $skip;
        return $this;
    }
    
    /**
     * Specifies how many results to skip. 
     * Returns the current Query instance.
     */
    public function skip(int $skip) : Query{
        $this->query['skip'] = $skip;
        return $this;
    }
    
    /**
     * Specifies the field to order the results by and the order direction.
     */
    public function orderBy(string $field, string $direction = 'asc') : Query{
        $this->query['orderby'] = $field . ' ' . $direction;
        return $this;
    }
    
    /**
     * Includes the result count in the result data.
     */
    public function inlineCount() : Query{
        $this->query['inlinecount'] = 'allpages';
        return $this;
    }
    
    /**
     * Adds a SAPb1\Filter to filter the results. This method performs an
     * AND operation.
     */
    public function where(Filters\Filter $filter) : Query{
        $filter->setOperator('and');
        $this->filters[] = $filter;
        return $this;
    }
    
    /**
     * Adds a SAPb1\Filter to filter the results. This method performs an
     * OR operation.
     */
    public function orWhere(Filters\Filter $filter) : Query{
        $filter->setOperator('or');
        $this->filters[] = $filter;
        return $this;
    }
    
    /**
     * Specifies the navigation properties to expand.
     */
    public function expand($name) : Query{
        $this->query['expand'] = $name;
        return $this;
    }

    /** 
     * Returns a count of the result.
     */
    public function count() : int{
        return $this->doRequest('/$count');
    }
    
    /** 
     * Returns a single result using the specified $id.
     */
    public function find($id) : object{
        
        if(is_string($id)){
            $id = "'" . str_replace("'", "''", $id) . "'";
        }
        
        return $this->doRequest('(' . $id . ')');
    }
    
    /** 
     * Returns a collection of results. A $callback function can be applied 
     * to each result.
     */
    public function findAll(callable $callback = null) : object{
        return $this->doRequest('', $callback);
    }
    
    private function doRequest(string $action = '', callable $callback = null){

        // Build the query string.
        $requestQuery = '?';
        
        foreach($this->query as $name => $value){
            $requestQuery .= '$' . $name . '=' . rawurlencode($value) . '&';
        }

        // Append the filters to the query string.
        if(count($this->filters) > 0){
            $requestQuery .= '$filter=';
            
            // Iterate over the filters collection.
            foreach($this->filters as $idx => $filter){
                // Append the filter operator (AND,OR) after the first filter has
                // been appened to the $requestQuery.
                $op = ($idx > 0) ? ' ' . $filter->getOperator() . ' ' : '';
                
                // Call the execute method on each filter.
                $requestQuery .= rawurlencode($op . $filter->execute());
            }
        }

        // Execute the service API with the query string.
        $request = new Request($this->config->getServiceUrl($this->serviceName . $action) . $requestQuery, $this->config->getSSLOptions());
        $request->setMethod('GET');
        $request->setHeaders($this->headers);
        
        // Set the SAP B1 session data.
        $request->setCookies($this->session);
        $response = $request->getResponse();

        // Check if the response code is successful.
        if($response->getStatusCode() === 200){

            // If the Content Type is plain text, then return the plain response body.
            if($response->getHeaders('Content-Type') == 'text/plain'){
                return $response->getBody();
            }
            
            // If the Content Type is JSON, then get the body as an object.
            elseif ($response->getHeaders('Content-Type') == 'application/json'){

                $result = $response->getJson();
                
                // If a callback is specified, then call it for each result in
                // the collection.
                if(null != $callback){
                    if(isset($result->value)){
                        foreach($result->value as $index => $item){
                            $callback($item, $index);
                        }
                    }
                }
                
                // Return the result.
                return $result;
            }
        }
        
        // Throw an exception when the response code is not 200.
        throw new SAPException($response);
    }
}