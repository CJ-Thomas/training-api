<?php

namespace com\linways\core\mapper;

use com\linways\base\mapper\Result;
use com\linways\base\mapper\IMapper;
use com\linways\base\mapper\ResultMap;
use com\linways\base\util\MakeSingletonTrait;

class PostServiceMapper implements IMapper{

    use MakeSingletonTrait;

    const SEARCH_POST = 'SEARCH_POST';

    private $mapper;
    
    private function getPost(){
        $mapper = null;
    
        $mapper = new ResultMap("getPost", "com\linways\core\dto\Post", "id", "id");
        $mapper->results[] = new Result("id", "id");
        $mapper->results[] = new Result("userId", "user_id");
        $mapper->results[] = new Result("content", "post");
        $mapper->results[] = new Result("caption", "caption");
    
        return $mapper;
    }

    public function getMapper()
    {
        if (empty($this->mapper)) {
            $this->mapper[self::SEARCH_POST] = $this->getPost();
        }
        return $this->mapper;
    }

}