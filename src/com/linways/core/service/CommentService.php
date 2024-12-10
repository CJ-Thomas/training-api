<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Comment;
use com\linways\core\mapper\CommentServiceMapper;
use com\linways\core\util\UuidUtil;
use Exception;

class CommentService extends BaseService
{
    use MakeSingletonTrait;


    private function __construct()
    {
        $this->mapper = CommentServiceMapper::getInstance()->getMapper();
    }

    
    /**
     * create a new comment
     * @param Comment $comment
     * @return Comment $comment
     */
    public function createComment(Comment $comment)
    {
        $comment = $this->realEscapeObject($comment);
        $comment->createdBy = $GLOBALS["userId"] ?? $comment->createdBy;
        $comment->updatedBy = $GLOBALS["userId"] ?? $comment->updatedBy;

        $comment->id = UuidUtil::guidv4();

        $query = "INSERT INTO comments (id, user_id, post_id, comment, parent_comment_id, created_by, updated_by)
            VALUES('$comment->id','$comment->userId','$comment->postId','$comment->comment','$comment->parentCommentId',
            '$comment->createdBy','$comment->updatedBy');";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw new Exception("UNABLE TO INSERT INTO DB");
        }


        return $comment;
    }


    /**
     * edit an existing comment
     * @param string $id
     * @param string $comment
     */
    public function editComment(string $id, string $comment)
    {
        $id = $this->realEscapeString($id);
        $comment = $this->realEscapeString($comment);

        if (empty($comment))
            return "";

        $query = "UPDATE comments SET commet = '$comment' WHERE id = '$id';";

        try {

            $result = ($this->executeQuery($query))->sqlResult;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * delete a comment
     * @param string $id
     * @return bool
     */
    public function deleteComment(string $id)
    {
        $id = $this->realEscapeString($id);

        $query = "DELETE FROM comments WHERE id LIKE '$id';";

        try {

            $result = ($this->executeQuery($query))->sqlResult;
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    /**
     * Get all comments related to a post
     * @param string $postId
     * @return Comments[]
     */
    public function getAllPostComments(string $postId)
    {
        $postId = $this->realEscapeString($postId);

        $query = "SELECT id, user_id, comment, parent_comment_id FROM comments WHERE post_id = '$postId';";

        try {

            $result = ($this->executeQuery($query))->sqlResult;
            while ($object = $result->fetch_object())
                $comments[] = $object;
        } catch (\Exception $e) {
            throw $e;
        }

        return $comments;
    }
}
