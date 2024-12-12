<?php

// namespace test\unit\service;

// use com\linways\core\dto\Post;
// use com\linways\core\service\PostService;
// use test\unit\APITestCase;
// use Exception;

// class PostServiceTest extends APITestCase{
//     protected function setUp()
//     {
//         parent::setUp();
//         $this->setInitialDataUsingSQLFile(__DIR__."/initial-users-setup.sql");
//     }

//     private function getPost(){
//         $post = new Post();
//         $post->content = "http://dummyimage.com/558x561.png/ff4444/ffffff";
//         $post->userId = "3cbf5ad8-d662-4aaa-9ea8-c59564773ae1";
//         $post->caption = "Vision-oriented incremental open system";
//         $post->timeStamp = "2023-12-15 11:00:27";

//         return $post;
//     }

//     public function testCreatePost(){
//         $post = $this->getPost();

//         try{
//             $result = PostService::getInstance()->createPost($post);
//         } catch( Exception $e ) { 
//             echo $e->getMessage();
//         }

//         // echo var_dump($result);  
//     }


//     protected function tearDown(){
//         $this->clearDBTable("users");
//     }
// }