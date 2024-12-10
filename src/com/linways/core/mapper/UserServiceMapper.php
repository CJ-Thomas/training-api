<?php
namespace com\linways\core\mapper;

use com\linways\base\mapper\Result;
use com\linways\base\mapper\IMapper;
use com\linways\base\mapper\ResultMap;
use com\linways\base\util\MakeSingletonTrait;

class UserServiceMapper implements IMapper{
    
    use MakeSingletonTrait;
    
    private $mapper = [];

    const SEARCH_USER = 'SEARCH_USER';


    private function getUser(){
        $mapper = null;

        $mapper = new ResultMap("getUser", "com\linways\dto\HelloWorld","id","id");
        $mapper->results[] = new Result("id","id");
        $mapper->results[] = new Result("uName","u_name");
        $mapper->results[] = new Result("email","email");
    }

    public function getMapper(){
        if (empty ($this->mapper)) {
            $this->mapper [self::SEARCH_USER] = $this->getUser();
        }
        return $this->mapper;
    }

}
?>