<?php
namespace com\linways\core\service;
use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Like;

class LikeService extends BaseService{

    use MakeSingletonTrait;
    /**
     * @param Like $like
     */
    public function createLike(Like $like){
        $like = $this-> realEscapeObject($like);
        
    }
    
    /**
     * @param Like $like
     */
    public function removeLike(Like $like){
        $like = $this-> realEscapeObject($like);
    }


    /**
     * @param String $postId
     * @return Likes[]
     */
    public function getTotalLikes(String $postId){
        $postId = $this-> realEscapeString($postId);
    }
}
?>