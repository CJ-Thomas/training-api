<?php

namespace com\linways\core\mapper;

use com\linways\base\mapper\Result;
use com\linways\base\mapper\IMapper;
use com\linways\base\mapper\ResultMap;
use com\linways\base\util\MakeSingletonTrait;

class PostServiceMapper implements IMapper{

    use MakeSingletonTrait;

    const SEARCH_COMMENT = 'SEARCH_COMMENT';

    private $mapper;
    
    private function getComment(){
        $mapper = null;
    
        $mapper = new ResultMap("getComment", "com\linways\core\dto\Comment", "id", "id");
        $mapper->results[] = new Result("id", "id");
        $mapper->results[] = new Result("userId", "user_id");
        $mapper->results[] = new Result("postId", "post_id");
        $mapper->results[] = new Result("comment", "comment");
        $mapper->results[] = new Result("parentCommentId", "parent_comment_id");
    
        return $mapper;
    }

    public function getMapper()
    {
        if (empty($this->mapper)) {
            $this->mapper[self::SEARCH_COMMENT] = $this->getMapper();
        }
        return $this->mapper;
    }

}