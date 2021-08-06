<?php

namespace com\linways\core\mapper;

use com\linways\base\mapper\Result;
use com\linways\base\mapper\IMapper;
use com\linways\base\mapper\ResultMap;
use com\linways\base\util\MakeSingletonTrait;

class HelloWorldServiceMapper implements IMapper
{
    private $mapper = [];

    use MakeSingletonTrait;

    const SEARCH_HELLO_WORLD = "SEARCH_HELLO_WORLD";

    public function getMapper()
    {
        if (empty ($this->mapper)) {
            $this->mapper [self::SEARCH_HELLO_WORLD] = $this->getHelloWorld();
        }
        return $this->mapper;
    }

    private function getHelloWorld()
    {
        $mapper = null;
        
        $mapper = new ResultMap('getHelloWorld','com\linways\core\dto\HelloWorld',"id","id");
        $mapper->results [] = new Result('id','id');
        $mapper->results [] = new Result('name','name');

        return $mapper;
    }
}