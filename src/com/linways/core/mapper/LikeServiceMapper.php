<?php

namespace com\linways\core\mapper;

use com\linways\base\mapper\Result;
use com\linways\base\mapper\IMapper;
use com\linways\base\mapper\ResultMap;
use com\linways\base\util\MakeSingletonTrait;

class LikeServiceMapper implements IMapper{

    use MakeSingletonTrait;

    const SEARCH_LIKE = 'SEARCH_LIKE';

    private $mapper;

    private function getlike(){

        $mapper = null;
    
        $mapper = new ResultMap("getlike", "com\linways\core\dto\Like", "id", "id");
        $mapper->results[] = new Result("id", "id");
        $mapper->results[] = new Result("userId", "user_id");
        $mapper->results[] = new Result("postId", "post_id");
    
        return $mapper;
    }

    public function getMapper()
    {
        if (empty($this->mapper)) {
            $this->mapper[self::SEARCH_LIKE] = $this->getMapper();
        }
        return $this->mapper;
    }

}