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

    private function getLikedUsers(){
        $mapper = null;

        $mapper = new ResultMap("getLikedUsers", "com\linways\core\dto\Like", "id", "l_id");
        $mapper->results[] = new Result("id", "l_id");
        $mapper->results[] = new Result("userId", "l_users");
        
        return $mapper;
    }
    
    private function getPost(){
        $mapper = null;
    
        $mapper = new ResultMap("getPost", "com\linways\core\dto\Post", "id", "p_id");
        $mapper->results[] = new Result("id", "p_id");
        $mapper->results[] = new Result("userId", "user_id");
        $mapper->results[] = new Result("content", "post");
        $mapper->results[] = new Result("caption", "caption");
        $mapper->results[] = new Result("likedUsers", "likedUsers", Result::OBJECT_ARRAY, $this->getLikedUsers());
    
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