<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Post;
use com\linways\core\mapper\PostServiceMapper;
use com\linways\core\util\UuidUtil;
use Exception;

class PostService extends BaseService
{
    use MakeSingletonTrait;


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
            throw new Exception("UNABLE TO INSERT INTO DB");
        }

        return $post;
    }

    /**
     * Edit an existing post within a certain period
     * @param string $id
     * @param string $post url of new post
     * @param string $caption
     */
    public function editPost(string $id, string $post, string $caption)
    {
        $post = $this->realEscapeString($post);
        $caption = $this->realEscapeString($caption);

        if (empty($post) && empty($caption))
            throw new Exception("UNDEFINED FIELDS");

        $query = "UPDATE posts SET";

        if (!empty($post))
            $query = $query . " post = '$post',";

        if (!empty($caption))
            $query = $query . " caption = '$caption',";

        $query = substr($query, 0, -1) . " WHERE id LIKE '$id';";

        try {

            $result1 = ($this->executeQuery($query))->sqlResult;
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

        $query = "DELETE FROM posts WHERE id = $id";

        try {

            $result = ($this->executeQuery($query))->sqlResult;
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }

    /**
     * gets all posts either in homepage or a user's account
     * @param string $userId default null
     * @param int $limit
     * @param int $offset
     * @return object[ id=> string, post=> string ]
     */
    public function getAllPosts(string $userId = "", int $limit = 10, int $offSet = 0)
    {

        $query = "SELECT id FROM posts LIMIT $limit OFFSET $offSet;";

        if (!empty($userId))
            $query = "SELECT id, post FROM posts WHERE user_id LIKE '$userId' LIMIT $limit OFFSET $offSet;";

        try {

            $result = ($this->executeQuery($query))->sqlResult;

            while ($object = $result->fetch_object())
                $posts[] = $object;
        } catch (Exception $e) {
            throw $e;
        }

        return $posts;
    }
}
