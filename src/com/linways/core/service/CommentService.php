<?php
    namespace com\linways\core\service;
    use com\linways\base\util\MakeSingletonTrait;
    use com\linways\core\dto\Comment;

    class CommentService extends BaseService{
        use MakeSingletonTrait;

        /**
         * @param Comment $comment
         * @return Comment $comment
         */
        public function createComment(Comment $comment){
            $comment = $this-> realEscapeObject($comment);


            $query = "INSERT INTO comments (id, user_id, post_id, comment, parent_comment_id, created_by, updated_by)
            VALUES('$comment->id','$comment->userId','$comment->postId','$comment->comment','','','');";
            return $comment;
        }
        

        /**
         * @param Comment $comment
         * @return Comment $comment
         */
        public function editComment(Comment $comment){
            $comment = $this-> realEscapeObject($comment); 
            
            return $comment;
        }

        /**
         * @param Comment $comment
         */
        public function deleteComment(Comment $comment){
            $comment = $this-> realEscapeObject($comment); 
        }

        /**
         * @param string $postId
         * @return Comments[]
         */
        public function getAllPostComments(string $postId){
            $postId = $this-> realEscapeString($postId);

        }

        /**
         * @param string $commentId
         * @return Comments[]
         */
        public function getAllCommentReplies(string $commentId){
            $commentId = $this-> realEscapeString($commentId);
        }

    }
?>