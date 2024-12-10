<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\base\util\SecurityUtils;
use com\linways\core\constant\StatusConstants;

use com\linways\core\dto\HelloWorld;
use com\linways\core\exception\HelloWorldException;
use com\linways\core\mapper\HelloWorldServiceMapper;
use com\linways\core\request\SearchHelloWorldRequest;

class HelloWorldService extends BaseService
{
    use MakeSingletonTrait;

    private function __construct() {
        $this->mapper = HelloWorldServiceMapper::getInstance()->getMapper();
    }

    /**
     * Save helloWorld
     * @param HelloWorld $helloWorld
     * @return String $id
     */
    public function saveHelloWorld (HelloWorld $helloWorld)
    {
        $helloWorld = $this->realEscapeObject($helloWorld);
        $helloWorld->createdBy = $GLOBALS['userId'] ?? $helloWorld->createdBy;
        $helloWorld->updatedBy = $GLOBALS['userId'] ?? $helloWorld->updatedBy;

        try{

                
            $this->validateSaveHelloWorldRequest($helloWorld);
            
            if(!empty($helloWorld->id))
            {
                $helloWorld->id = $this->updateHelloWorld($helloWorld);
            }
            else
            {
                $helloWorld->id = $this->insertHelloWorld($helloWorld);
            }


        }catch(\Exception $e) {
            if($e->getCode() !== HelloWorldException::INVALID_PARAMETERS && $e->getCode() !== HelloWorldException::EMPTY_PARAMETERS && $e->getCode() !== HelloWorldException::DUPLICATE_ENTRY) {
                throw new HelloWorldException(HelloWorldException::ERROR_SAVING_CURRICULUM,"Failed to save helloWorld! Please try again");
            } else if ($e->getCode() === HelloWorldException::DUPLICATE_ENTRY) {
                throw new HelloWorldException (HelloWorldException::DUPLICATE_ENTRY,"Cannot create helloWorld.".$helloWorld->name." already exists!");
            } else {
                throw new HelloWorldException ($e->getCode(),$e->getMessage());
            }
        }
        
        return $helloWorld->id;
    }
    
    /**
     * Validate HelloWorld Request Before Saving
     * @param HelloWorld $helloWorld
     * @return NULL
     */
    private function validateSaveHelloWorldRequest($helloWorld)
    {
        if(empty($helloWorld->name))
            throw new HelloWorldException(HelloWorldException::EMPTY_PARAMETERS,"HelloWorld name is empty! Please enter a name for helloWorld");
        if(empty($helloWorld->type))
            throw new HelloWorldException(HelloWorldException::EMPTY_PARAMETERS,"HelloWorld type is empty! Please choose a helloWorld type");
        
        //TODO: Validate type if updating
    }
    
    /**
     * Insert helloWorld
     * @param HelloWorld $helloWorld
     * @return String $id
     */
    private function insertHelloWorld(HelloWorld $helloWorld)
    {
        $properties = !empty($helloWorld->properties) ? "'".json_encode($helloWorld->properties)."'" : "NULL";
        $id = SecurityUtils::getRandomString();

        $query = "";
        
        try {
            $this->executeQuery($query);return $id;
        } catch (\Exception $e) {
            throw new HelloWorldException($e->getCode(),$e->getMessage());
        }
    }

    /**
     * Update HelloWorld
     * @param HelloWorld $helloWorld
     * @return NULL
     */
    private function updateHelloWorld(HelloWorld $helloWorld)
    {
        $properties = !empty($helloWorld->properties) ? "'".json_encode($helloWorld->properties)."'" : "NULL";
        
        $query = "";

        try {
            $this->executeQuery($query);return $helloWorld->id;
        } catch (\Exception $e) {
            throw new HelloWorldException($e->getCode(),$e->getMessage());
        }
    }

    /**
     * Delete HelloWorld (Soft Delete)
     * @param String $id
     * @return NULL
     */
    public function deleteHelloWorld($id)
    {
        $id = $this->realEscapeString($id);
        $updatedBy = $GLOBALS['userId'];

        if(empty($id)) {
            throw new HelloWorldException(HelloWorldException::EMPTY_PARAMETERS,"No helloWorld selected! Please select a helloWorld to delete");
        }

        //TODO: Do validation before deleting

        $query = "";

        try {
            $this->executeQuery($query);
        } catch (\Exception $e) {
            throw new HelloWorldException(HelloWorldException::ERROR_DELETING_CURRICULUM,"Error deleting helloWorld! Please try again");
        }
    }

    /**
     * Restore HelloWorld
     * @param String $id
     * @return NULL
     */
    public function restoreHelloWorld($id)
    {
        $id = $this->realEscapeString($id);
        $updatedBy = $GLOBALS['userId'];

        if(empty($id)) {
            throw new HelloWorldException(HelloWorldException::EMPTY_PARAMETERS,"No helloWorld selected! Please select a helloWorld to restore");
        }

        $query = "";

        try {
            $this->executeQuery($query);
        } catch (\Exception $e) {
            throw new HelloWorldException(HelloWorldException::ERROR_RESTORING_CURRICULUM,"Error restoring helloWorld! Please try again");
        }
    }
    
    /** 
     * Search HelloWorld Details
     * @param SearchHelloWorldRequest $request
     * @return HelloWorld
     */
    public function searchHelloWorld(SearchHelloWorldRequest $request)
    {
        $request = $this->realEscapeObject($request);

        $whereQuery = "";
        $limitQuery = "";

        if($request->trashed === StatusConstants::ACTIVE) {
            $whereQuery .= "";
        }

        if($request->trashed === StatusConstants::TRASHED) {
            $whereQuery .= "";
        }

        if($request->startIndex !== "" && $request->endIndex !== "")
        {
            $limitQuery .= " LIMIT $request->startIndex,$request->endIndex";
        }


        $query = "";

        try {
            $helloWorlds = $this->executeQueryForList($query.$whereQuery.$limitQuery,$this->mapper[HelloWorldServiceMapper::SEARCH_HELLO_WORLD]);
        } catch (\Exception $e) {
            throw new HelloWorldException(HelloWorldException::ERROR_FETCHING,"Cannot fetch helloWorld details! Please try again");
        }

        return $helloWorlds;
    }
}