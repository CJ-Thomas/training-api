<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Comment;
use com\linways\core\exception\ParameterException;
use com\linways\core\mapper\CommentServiceMapper;
use com\linways\core\util\UuidUtil;
use Exception;

class CommentService extends BaseService
{
    use MakeSingletonTrait;

    private $mapper;

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

        if(empty($comment->comment))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS,"missing comment");


        $query = "INSERT INTO comments (id, user_id, post_id, comment, parent_comment_id, created_by, updated_by)
            VALUES('$comment->id','$comment->userId','$comment->postId','$comment->comment','$comment->parentCommentId',
            '$comment->createdBy','$comment->updatedBy');";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
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
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS,"missing comment parameter");
            
        $query = "UPDATE comments SET commet = '$comment' WHERE id = '$id';";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * delete a commentParameterException(ParameterException::EMPTY_PARAMETERS,"missing parametes");
     * @param string $id
     * @return bool
     */
    public function deleteComment(string $id)
    {
        $id = $this->realEscapeString($id);

        if(empty($id))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS,"missing id parameter");


        $query = "DELETE FROM comments WHERE id LIKE '$id';";

        try {
            $result = $this->executeQueryForObject($query);
        } catch (Exception $e) {
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

        if(empty($postId))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS,"missing postId parameters");


        $query = "SELECT id, user_id, comment, parent_comment_id FROM comments WHERE post_id = '$postId';";

        try {
            $comments = $this->executeQueryForList($query,false,$this->mapper);
        } catch (Exception $e) {
            throw $e;
        }

        return $comments;
    }
}
