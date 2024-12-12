<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Like;
use com\linways\core\exception\GeneralException;
use com\linways\core\mapper\LikeServiceMapper;
use com\linways\core\util\UuidUtil;
use Exception;

class LikeService extends BaseService
{

    use MakeSingletonTrait;

    private $mapper;
    
    private function __construct()
    {
        $this->mapper = LikeServiceMapper::getInstance()->getMapper();
    }

    /**
     * @param Like $like
     */
    public function createLike(Like $like)
    {
        $like = $this->realEscapeObject($like);
        $like->createdBy = $GLOBALS["userId"] ?? $like->createdBy;
        $like->updatedBy = $GLOBALS["userId"] ?? $like->updatedBy;

        $like->id = UuidUtil::guidv4();

        $query = "INSERT INTO likes (id, user_id, post_id, created_by, updated_by)
        VALUES('$like->id', '$like->userId', '$like->postId', '$like->createdBy',
        '$like->updatedBy');";
        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Like $like
     */
    public function removeLike(string $id)
    {
        $like = $this->realEscapeString($id);

        if(empty($id))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS,"missing id parameter");

        $query = "DELETE FROM likes WHERE id LIKE '$id';";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }

}
