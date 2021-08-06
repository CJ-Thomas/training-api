<?php
namespace com\linways\core\service;

use com\linways\base\connection\MySqlQuery;
use com\linways\base\connection\MySqlConnector;
use com\linways\core\exception\HelloWorldException;
                
 class BaseService extends MySqlQuery 
{

	/**
	 * 
	 * @var MySqlConnector
	 */
    public $mySqlConnector;
    
    /**
     * Establish a MYSQL Connection
     * If connection and db properties not set this method 
     * help to establish the connection.
     * This will be called from MySqlQuery or also you can call externally
     */
    public function establishConnection()
    {
        $this->mySqlConnector = MySqlConnector::getInstance();
        /**
         * This condition is mandatory to check multiple database connections.
         * If not added then connection will be reused from the first project ie projectA service
         * invoked first then mysqlconnection will established based on the projectA DB configuration
         * after that if we invoke projectB service mysqlConnection will be resused from ProjectA bz of
         * singleton instance of mysqlconnection
         * to avoid this situation we need to set 'instanceof' attribute value in setDBConnectionProperties() method
         */
        if($this->mySqlConnector->instanceOf  != null && !$this->mySqlConnector->instanceOf  instanceof BaseService)
        {
        	$this->mySqlConnector->closeConnection();
        }
        if(!$this->mySqlConnector->connection)
        {
        
            $this->setDBConnectionProperties();
        }
        $this->connection = $this->mySqlConnector->getConnection();
    }
    /**
     * Set DB Connection properties
     */
    protected function setDBConnectionProperties()
    {
    	$this->mySqlConnector->instanceOf = $this;
        // check DB configurations
        $dbconfigFilePath = getenv('DB_PROFESIONAL_CONFIG');
        
        //check file configured correctly 
        if(empty($dbconfigFilePath))
        {
            throw new HelloWorldException(HelloWorldException::DB_CONFIG_FILE_PATH_NOT_DEFINED_IN_SERVER,"Professional database configuration file 'db_prof_conf.php' path not configured in server conf!.");
        }
        //include db configuration file to load connection properties
        require $dbconfigFilePath;
//         global  $DB_HOST,$DB_USER,$DB_PASSWD,$DB_NAME;
        if (empty ( $DB_HOST ) || empty ( $DB_USER ) || empty ( $DB_PASSWD ) || empty ( $DB_NAME )) {
            throw new HelloWorldException ( HelloWorldException::DB_CONFIG_NOT_SET, "Professional  DB configuration not set properly" );
        }
        
        //Set DB Connection properties
        $this->mySqlConnector->DB_HOST = $DB_HOST;
        $this->mySqlConnector->DB_USER = $DB_USER;
        $this->mySqlConnector->DB_PASSWD = $DB_PASSWD;
        $this->mySqlConnector->DB_NAME = $DB_NAME;
    }
}