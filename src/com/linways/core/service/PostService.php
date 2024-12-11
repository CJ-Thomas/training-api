<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Post;
use com\linways\core\exception\ParameterException;
use com\linways\core\mapper\PostServiceMapper;
use com\linways\core\request\SearchPostRequest;
use com\linways\core\util\UuidUtil;
use Exception;

class PostService extends BaseService
{
    use MakeSingletonTrait;

    private $mapper;

    private function __construct()
    {
        $this->mapper = PostServiceMapper::getInstance()->getMapper();
    }

    /**
     * Create a new post
     * @param Post $post
     * @return Post $post
     */
    public function createPost(Post $post)
    {
        $post = $this->realEscapeObject($post);
        $post->createdBy = $GLOBALS["userId"] ?? $post->createdBy;
        $post->updatedBy = $GLOBALS["userId"] ?? $post->updatedBy;

        $post->id = UuidUtil::guidv4();

        $query = "INSERT INTO posts (id, user_id, post, caption, created_by, updated_by)
            VALUES('$post->id','$post->userId','$post->post','$post->caption',
            '$post->created_by','$post->updated_by');";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }

        return $post;
    }

    /**
     * Edit an existing post within a certain period
     * @param Post $post
     */
    public function editPost(Post $post)
    {
        $post = $this->realEscapeObject($post);

        if (empty($post->post) && empty($post->caption))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS,"missing parameters");
        
        if (!empty($post))
            $columnArray[] = "post = '$post'";
        
        if (!empty($caption))
        $columnArray[] = "caption = '$caption'";
    
        $query = "UPDATE posts SET ".implode(",",$columnArray)." WHERE id LIKE '$post->id';";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * delete a post
     * @param string $id
     * @return bool
     */
    public function deletePost(string $id)
    {
        $id = $this->realEscapeString($id);

        if(empty($id))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS,"missing id parameter");

        $query = "DELETE FROM posts WHERE id = $id";

        try {
            $result = $this->executeQuery($query);
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }

    /**
     * gets all posts either in homepage or a user's account
     * @return Post[]
     */
    public function fetchPosts(SearchPostRequest $request)
    {
        $whereQuery=(!empty($request))?"WHERE user_id LIKE '$request->userId '":"";
        $limitQuery = "LIMIT $request->startIndex, $request->endIndex;";
        $query = "SELECT id, post, caption ".$whereQuery.$limitQuery;
        try {
            $posts = $this->executeQueryForList($query);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function fetchTotalLikes($postId){

        $postId = $this->realEscapeString($postId);

        if(empty($postId))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS,"missing postId parameter");

        $query = "SELECT COUNT(*) FROM likes WHERE post_id LIKE '$postId';";

        try {
            $result = $this->executeQueryForObject($query);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $result;
    }
}
