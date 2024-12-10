<?php
namespace com\linways\core\service;
use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Like;
use com\linways\core\util\UuidUtil;
use Exception;

class LikeService extends BaseService{

    use MakeSingletonTrait;
    /**
     * @param Like $like
     */
    public function createLike(Like $like){
        $like = $this-> realEscapeObject($like);
        $like->createdBy = $GLOBALS["userId"] ?? $like->createdBy;
        $like->updatedBy = $GLOBALS["userId"] ?? $like->updatedBy;

        $like->id = UuidUtil::guidv4();

        $query = "INSERT INTO likes (id, user_id, post_id, created_by, updated_by)
        VALUES('$like->id', '$like->userId', '$like->postId', '$like->createdBy',
        '$like->updatedBy');";
        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw new Exception("UNABLE TO INSERT INTO DB");
        }
    }
    
    /**
     * @param Like $like
     */
    public function removeLike(Like $like){
        $like = $this-> realEscapeObject($like);

        $query = "DELETE FROM likes WHERE id LIKE '$like->id';";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * @param String $postId
     * @return Likes[]
     */
    public function getTotaPostlLikes(String $postId){
        $postId = $this-> realEscapeString($postId);

        $query = "SELECT COUNT(*) FROM likes WHERE post_id LIKE '$postId';";

        try {
            $result = ($this->executeQuery($query))->sqlResult;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>