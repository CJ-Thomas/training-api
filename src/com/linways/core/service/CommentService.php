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


        $comment->parentCommentId = empty($comment->parentCommentId) ? "null" : "'$comment->parentCommentId'";

        $query = "INSERT INTO comments (id, user_id, post_id, comment, parent_comment_id, created_by, updated_by)
        VALUES('$comment->id','$comment->userId','$comment->postId','$comment->content',$comment->parentCommentId,
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


        $query = "DELETE FROM comments WHERE id = '$id';";

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

        $query = "SELECT c1.id, c1.user_id, c1.post_id, c1.comment, u1.u_name, u1.profile_picture,
        c2.id as r_id, c2.user_id as r_user_id, c2.comment as r_comment, u2.id as r_user_id, u2.u_name as r_u_name,
        u2.profile_picture as r_profile_picture FROM comments c1 LEFT JOIN comments c2 ON c1.id = c2.parent_comment_id
        LEFT JOIN users u1 ON c1.user_id = u1.id LEFT JOIN users u2 ON c2.user_id = u2.id WHERE c1.parent_comment_id IS NULL";

        if (!empty($request->id))
            $query .= " AND c1.id = '$request->id'";

        if (!empty($request->postId))
            $query .= " AND c1.post_id = '$request->postId'";

        if (!empty($request->userId))
            $query .= " AND c1.user_id = '$request->userId'";

        if (!empty($request->searchContent))
            $query .= " AND c1.comment LIKE '%$request->searchContent%'";

        $query .= " LIMIT $request->startIndex, $request->endIndex;";

        try {
            $comments = $this->executeQueryForList($query, $this->mapper[CommentServiceMapper::SEARCH_COMMENT]);
        } catch (Exception $e) {
            throw $e;
        }

        return $comments;
    }
}
