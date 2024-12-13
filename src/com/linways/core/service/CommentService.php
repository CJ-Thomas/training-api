<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\Comment;
use com\linways\core\exception\GeneralException;
use com\linways\core\mapper\CommentServiceMapper;
use com\linways\core\request\SearchCommentRequest;
use com\linways\core\response\CommentResponse;
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

        if (empty($comment->content) || empty($comment->userId) || empty($comment->postId))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing comment");


        $query = "INSERT INTO comments (id, user_id, post_id, comment, created_by, updated_by)
            VALUES('$comment->id','$comment->userId','$comment->postId','$comment->content',
            '$comment->createdBy','$comment->updatedBy');";

        if (!empty($comment->parentCommentId))
            $query = "INSERT INTO comments (id, user_id, post_id, comment, parent_comment_id, created_by, updated_by)
            VALUES('$comment->id','$comment->userId','$comment->postId','$comment->content','$comment->parentCommentId',
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
     * @param Comment $comment
     */
    public function editComment(Comment $comment)
    {
        $comment = $this->realEscapeObject($comment);

        if (empty($comment->content) || empty($comment->id))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing comment parameter");

        $query = "UPDATE comments SET comment = '$comment->content' WHERE id = '$comment->id';";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * delete a commentParameterException(GeneralException::EMPTY_PARAMETERS,"missing parametes");
     * @param string $id
     */
    public function deleteComment(string $id)
    {
        $id = $this->realEscapeString($id);

        if (empty($id))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing id parameter");


        $query = "DELETE FROM comments WHERE id LIKE '$id';";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all comments related to a post
     * @param SearchCommentRequest $request
     * @return CommentResponse
     */
    public function fetchComments(SearchCommentRequest $request)
    {
        $request = $this->realEscapeObject($request);

        $query = "SELECT id, user_id, post_id, comment, parent_comment_id FROM comments WHERE 1=1";

        if (!empty($request->id))
            $query .= " AND id LIKE '$request->id'";

        if (!empty($request->postId))
            $query .= " AND post_id LIKE '$request->postId'";

        if (!empty($request->userId))
            $query .= " AND user_id LIKE '$request->userId'";

        if (!empty($request->parentCommentId))
            $query .= " AND parent_comment_id LIKE '$request->parentCommentId'";

        if (!empty($request->searchContent))
            $query .= " AND comment LIKE '%$request->searchContent%'";

        $response = new CommentResponse();

        try {
            $response->comments = $this->executeQueryForList($query);
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }
}
