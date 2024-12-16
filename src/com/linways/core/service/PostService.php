<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Post;
use com\linways\core\exception\GeneralException;
use com\linways\core\mapper\PostServiceMapper;
use com\linways\core\request\SearchPostRequest;
use com\linways\core\util\UuidUtil;
use com\linways\core\dto\Like;
use com\linways\core\response\PostResponse;
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
     */
    public function createPost(Post $post)
    {
        $post = $this->realEscapeObject($post);
        $post->createdBy = $GLOBALS["userId"] ?? $post->createdBy;
        $post->updatedBy = $GLOBALS["userId"] ?? $post->updatedBy;

        $post->id = UuidUtil::guidv4();

        if (empty($post->content))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS);

        $query = "INSERT INTO posts(id, user_id, post, caption, created_by, updated_by)
        VALUES('$post->id', '$post->userId', '$post->content', '$post->caption', '$post->createdBy', '$post->updatedBy');";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }

        return $post;
    }

    /**
     * Edit an existing post
     * @param Post $post
     */
    public function editPost(Post $post)
    {
        $post = $this->realEscapeObject($post);

        if (empty($post->content) && empty($post->caption))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing parameters");

        if (!empty($post->content))
            $columnArray[] = "post = '$post->content'";

        if (!empty($post->caption))
            $columnArray[] = "caption = '$post->caption'";

        $query = "UPDATE posts SET " . implode(",", $columnArray) . " WHERE id = '$post->id';";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * delete a post
     * @param string $id
     */
    public function deletePost(string $id)
    {
        $id = $this->realEscapeString($id);

        if (empty($id))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing id parameter");

        $query = "DELETE FROM posts WHERE id = '$id'";

        try {
            $this->executeQuery($query);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * gets all posts/ a single post
     * @return object[]
     */
    public function fetchPosts(SearchPostRequest $request)
    {

        $query = "SELECT  p.id, p.user_id, p.post, p.caption, p.time_stamp, COUNT(l.post_id) AS total_likes
        FROM posts p LEFT JOIN likes l ON p.id = l.post_id WHERE 1=1";

        if (!empty($request->id)) {
            $query .= " AND p.id = '$request->id'";
        }

        if (!empty($request->fromDate) && !empty($request->toDate)) {

            $query .= " AND p.time_stamp BETWEEN '$request->fromDate' AND '$request->toDate'";
        }

        if (date($request->fromDate) > date($request->toDate)){
            throw new GeneralException(GeneralException::INVALID_DATE_RANGE);
        }

        $query .= " GROUP BY p.id LIMIT $request->startIndex, $request->endIndex;";

        try {
            $posts = $this->executeQueryForList($query);
        } catch (Exception $e) {
            throw $e;
        }
        return $posts;
    }

    /**
     * @param Like $like
     * @return Like
     */
    public function likePost(Like $like)
    {
        $like = $this->realEscapeObject($like);
        $like->createdBy = $GLOBALS["userId"] ?? $like->createdBy;
        $like->updatedBy = $GLOBALS["userId"] ?? $like->updatedBy;

        if (empty($like->userId) || empty($like->postId))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS);

        $like->id = UuidUtil::guidv4();

        $query = "INSERT INTO likes (id, user_id, post_id, created_by, updated_by)
        VALUES('$like->id', '$like->userId', '$like->postId', '$like->createdBy',
        '$like->updatedBy');";
        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
        return $like;
    }

    /**
     * @param Like $like
     */
    public function removeLike(string $id)
    {
        $id = $this->realEscapeString($id);

        if (empty($id))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing id parameter");

        $query = "DELETE FROM likes WHERE id = '$id';";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
