<?php

namespace SAPb1;

/**
 * Class SAPClient
 *
 * SAPClient manages access to SAP B1 Service Layer and provides methods to 
 * perform CRUD operations.
 *
 */
class SAPClient{
    
    private $config = [];
    private $session = [];

    /**
     * Initializes SAPClient with configuration and session data.
     *
     * @param array $configOptions
     * @param array $session
     */
    public function __construct(array $configOptions, array $session){
        $this->config = new Config($configOptions);
        $this->session = $session;
    }
    
    /**
     * Returns a new instance of SAPb1\Service.
     *
     * @param string $serviceName
     * @return Service
     */
    public function getService(string $serviceName) : Service{
        return new Service($this->config, $this->session, $serviceName);
    }
    
    /**
     * Returns the current SAP B1 session data.
     *
     * @return array
     */
    public function getSession() : array{
        return $this->session;
    }
    
    /**
     * Returns an XML string of the oData meta data.
     *
     * @return string
     */
    public function getMetaData() : string{
        $request = new Request($this->config->getServiceUrl('$metadata'), $this->config->getSSLOptions());
        $request->setMethod('GET');
        $request->setCookies($this->session);
        $response = $request->getResponse(); 

        return $response->getBody();
    }
    
    /**
     * Returns a new instance of SAPb1\Query, which allows for cross joins.
     *
     * @param string $join
     * @return Query
     */
    public function query($join) : Query{
        return new Query($this->config, $this->session, '$crossjoin('. str_replace(' ', '', $join) . ')');
    }

    /**
     * Creates a new SAP B1 session and returns a new instance of SAPb1\Client.
     *
     * @param array $configOptions
     * @param string $username
     * @param string $password
     * @param string $company
     * @return SAPClient
     */
    public static function createSession(array $configOptions, string $username, string $password, string $company) : SAPClient{
        
        $config = new Config($configOptions);

        $request = new Request($config->getServiceUrl('Login'), $config->getSSLOptions());
        $request->setMethod('POST');
        $request->setPost(['UserName' => $username, 'Password' => $password, 'CompanyDB' => $company]);
        $response = $request->getResponse();
        
        if($response->getStatusCode() === 200){
            return new SAPClient($config->toArray(), $response->getCookies());
        }
        
        throw new SAPException($response);
    }
}
