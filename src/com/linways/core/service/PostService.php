<?php
    namespace com\linways\core\service;
    use com\linways\base\util\MakeSingletonTrait;
    use com\linways\core\dto\Post;

    class PostService extends BaseService{
        use MakeSingletonTrait;

        /**
         * Create a new post
         * @param Post $post
         * @return Post $post
         */
        public function createPost(Post $post){
            $post = $this-> realEscapeObject($post);
            //validate and create new post

            return $post;
        }

        /**
         * Edit an existing post within a certain period
         * @param Post $post
         */
        public function editPost(Post $post){
            $post = $this-> realEscapeObject($post);
        }

        /**
         * @param Post $post
         */
        public function deletePost(Post $post){
            $post = $this-> realEscapeObject($post);
        }

        /**
         * @return Posts[]
         */
        public function getAllPosts(){

        }
        
        /**
         * @param Post $post
         * @return Post
         */
        public function getPostDetails(String $postId){
            $postId = $this-> realEscapeString($postId);
        }
    }
?>