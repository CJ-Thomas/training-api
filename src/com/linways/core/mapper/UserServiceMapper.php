<?php

namespace com\linways\core\mapper;

use com\linways\base\mapper\Result;
use com\linways\base\mapper\IMapper;
use com\linways\base\mapper\ResultMap;
use com\linways\base\util\MakeSingletonTrait;

class UserServiceMapper implements IMapper
{

    use MakeSingletonTrait;

    const SEARCH_USER = 'SEARCH_USER';

    private function getPost(){
        
        $mapper = null;
    
        $mapper = new ResultMap("getPost", "com\linways\core\dto\Post", "id", "id");
        $mapper->results[] = new Result("id", "p_id");
        $mapper->results[] = new Result("userId", "user_id");
        $mapper->results[] = new Result("content", "post");
        $mapper->results[] = new Result("caption", "caption");
    
        return $mapper;
    }


    private function getUser()
    {
        $mapper = null;

        $mapper = new ResultMap("getUser", "com\linways\core\dto\User", "id", "id");
        $mapper->results[] = new Result("id", "id");
        $mapper->results[] = new Result("uName", "u_name");
        $mapper->results[] = new Result("email", "email");
        $mapper->results[] = new Result("password", "password");
        $mapper->results[] = new Result("bio", "bio");
        $mapper->results[] = new Result("profilePicture", "profile_picture");
        $mapper->results[] = new Result("role", "role");
        $mapper->results[] = new Result("posts", "posts", Result::OBJECT_ARRAY, $this->getPost());

        return $mapper;
    }

    public function getMapper()
    {
        if (empty($this->mapper)) {
            $this->mapper[self::SEARCH_USER] = $this->getUser();
        }
        return $this->mapper;
    }
}
