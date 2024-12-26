<?php

namespace com\linways\core\mapper;

use com\linways\base\mapper\Result;
use com\linways\base\mapper\IMapper;
use com\linways\base\mapper\ResultMap;
use com\linways\base\util\MakeSingletonTrait;

class CommentServiceMapper implements IMapper{

    use MakeSingletonTrait;

    const SEARCH_COMMENT = 'SEARCH_COMMENT';

    private $mapper;

    private function getReplies(){
        
        $mapper = null;

        $mapper = new ResultMap("getReplies", "com\linways\core\dto\Comment", "id", "r_id");
        $mapper->results[] = new Result("id", "r_id");
        $mapper->results[] = new Result("userId", "r_user_id");
        $mapper->results[] = new Result("content", "r_comment");
        $mapper->results[] = new Result("userName", "r_u_name");
        $mapper->results[] = new Result("profilePicture", "r_profile_picture");

        return $mapper;
    }

    private function getCommentWithReplies(){
        $mapper = null;
    
        $mapper = new ResultMap("getCommentWithReplies", "com\linways\core\dto\Comment", "id", "id");
        $mapper->results[] = new Result("id", "id");
        $mapper->results[] = new Result("userId", "user_id");
        $mapper->results[] = new Result("postId", "post_id");
        $mapper->results[] = new Result("content", "comment");
        $mapper->results[] = new Result("userName", "u_name");
        $mapper->results[] = new Result("profilePicture", "profile_picture");
        $mapper->results[] = new Result("replies", "replies", Result::OBJECT_ARRAY, $this->getReplies());
    
        return $mapper;
    }

    public function getMapper()
    {
        if (empty($this->mapper)) {
            $this->mapper[self::SEARCH_COMMENT] = $this->getCommentWithReplies();
        }
        return $this->mapper;
    }

}