<?php

namespace SAPb1;

/**
 * Class Service
 *
 * Service class contains methods to perform CRUD actions on a service.
 *
 */
class Service{
    
    private $config;
    private $session;
    private $serviceName;
    
    /**
     * Initializes a new instance of Service.
     *
     * @param Config $configOptions
     * @param array $session
     * @param string $serviceName
     */
    public function __construct(Config $configOptions, array $session, string $serviceName){
        $this->config = $configOptions;
        $this->session = $session;
        $this->serviceName = $serviceName;
    }
    
    /**
     * Creates an entity. Returns the newly created entity on success.
     * Throws SAPb1\SAPException if an error occurred.
     *
     * @param array $data
     * @return object
     */
    public function create(array $data) : object{
        
        $response = $this->doRequest('POST', $data);
        
        if($response->getStatusCode() === 201){
            return $response->getJson();
        }
        
        throw new SAPException($response);
    }
    
    /**
     * Updates an entity using $id. Returns true on success.
     * Throws SAPb1\SAPException if an error occurred.
     *
     * @param int|string $id
     * @param array $data
     * @return boolean
     */
    public function update($id, array $data) : bool{
        
        if(is_string($id)){
            $id = "'" . str_replace("'", "''", $id) . "'";
        }

        $response = $this->doRequest('PATCH', $data, '(' . $id . ')');

        if($response->getStatusCode() === 204){
            return true;
        }
        
        throw new SAPException($response);
    }
    
    /**
     * Deletes an entity using $id. Returns true on success.
     * Throws SAPb1\SAPException if an error occurred.
     *
     * @param int|string $id
     * @return boolean
     */
    public function delete($id) : bool{
        
        if(is_string($id)){
            $id = "'" . str_replace("'", "''", $id) . "'";
        }

        $response = $this->doRequest('DELETE');

        if($response->getStatusCode() === 204){
            return true;
        }
        
        throw new SAPException($response);
    }
    
    /**
     * Performs an action on an entity using $id. Returns true on success.
     * Throws SAPb1\SAPException if an error occurred.
     *
     * @param int|string $id
     * @param string $action
     * @return boolean
     */
    public function action($id, string $action) : bool{
        
        if(is_string($id)){
            $id = "'" . str_replace("'", "''", $id) . "'";
        }

        $response = $this->doRequest('POST', [], $action);

        if($response->getStatusCode() === 204){
            return true;
        }
        
        throw new SAPException($response);
    }
    
    /**
     * Returns a new instance of SAPb1\Query.
     *
     * @return Query
     */
    public function queryBuilder() : Query{
        return new Query($this->config, $this->session, $this->serviceName);
    }
    
    private function doRequest($method, $postData, $action = '') : Response{
        $request = new Request($this->config->getServiceUrl($this->serviceName) . $action, $this->config->getSSLOptions());
        $request->setMethod($method);
        $request->setCookies($this->session);
        $request->setPost($postData);
        
        return $request->getResponse();
    }
}