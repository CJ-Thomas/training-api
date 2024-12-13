<?php

namespace test\unit\service;

use com\linways\core\dto\Comment;
use com\linways\core\exception\GeneralException;
use com\linways\core\request\SearchCommentRequest;
use com\linways\core\service\CommentService;
use test\unit\APITestCase;
use Exception;

class CommentServiceTest extends APITestCase{
    
    protected function setUp()
    {
        parent::setUp();
        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-users-setup.sql");
        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-posts-setup.sql");
        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-comments-setup.sql");
    }

    private function getComment(string $parentCommentId = null){
        $comment = new Comment();
        $comment->userId = "40569620-95d2-455f-b7d9-f5507f60ebd3";
        $comment->postId = "006c5976-9101-4fc5-bea4-6d45839b7392";
        $comment->content = "weeewwwwwwwwww";
        $comment->parentCommentId = $parentCommentId;

        return $comment;
    }

    public function testCreateComment(){
        $comment = $this->getComment();

        try {
            $result = CommentService::getInstance()->createComment($comment);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->assertDatabaseHas("comments",["id" => $result->id]);
    }

    public function testCreateCommentEmptyParam(){

        $comment = $this->getComment();
        $comment->content = "";

        try {
            CommentService::getInstance()->createComment($comment);
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }

    //editComment Test
    public function testEditComment(){
        $comment = new Comment();

        $comment->id = "b69e1ade-97d7-414c-b418-67b952bd9cec";
        $comment->content = "single-state installation";
        
        try {
            CommentService::getInstance()->editComment($comment);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->assertDatabaseHas("comments", ["id" => $comment->id, "comment" => $comment->content]);
        $this->assertDatabaseHasNot("comments", ["id" => $comment->id, "comment" => "Adaptive multi-state installation"]);

    }

    public function testEditCommentEmptyParam(){
        $comment = new Comment();

        $comment->id = "";
        $comment->content = "";

        try {
            CommentService::getInstance()->editComment($comment);
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }

    public function testDeleteComment(){
        $id = "96253936-a867-44e6-b4d0-54b3328d2c41";
        
        try {
            CommentService::getInstance()->deleteComment($id);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->assertDatabaseHasNot("comments", ["id" => $id]);
    }

    public function testDeleteCommentEmptyParams(){
        $id = "";
        
        try {
            CommentService::getInstance()->deleteComment($id);
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }

    public function testFetchCommentWithId(){
        $request = new SearchCommentRequest();
        $request->id = "935d327b-f1fb-44c8-8f3e-d20161ec111f";

        try{
            $result = CommentService::getInstance()->fetchComments($request);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>with id\n";
        echo var_dump($result)."\n";
        $this->assertIsArray($result);
    }

    public function testFetchCommentWithPostId(){
        $request = new SearchCommentRequest();
        $request->postId = "e2c7057f-35ff-4fdd-8e7f-8ec496881336";

        try{
            $result = CommentService::getInstance()->fetchComments($request);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>with post id\n";
        echo var_dump($result)."\n";
        $this->assertIsArray($result);
    }

    public function testFetchCommentWithUserId(){
        $request = new SearchCommentRequest();
        $request->userId = "c560839d-29c6-4134-9e14-c23fab1d5a5b";

        try{
            $result = CommentService::getInstance()->fetchComments($request);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>with user id\n";
        echo var_dump($result)."\n";
        $this->assertIsArray($result);
    }

    public function testFetchCommentWithParentCommentId(){
        $request = new SearchCommentRequest();
        $request->parentCommentId = "5970d16a-31f8-4a9d-a73d-b1582db8c7d0";

        try{
            $result = CommentService::getInstance()->fetchComments($request);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>with parent comment id\n";
        echo var_dump($result)."\n";
        $this->assertIsArray($result);
    }

    public function testFetchCommentWithComment(){
        $request = new SearchCommentRequest();
        $request->searchContent = "local";

        try{
            $result = CommentService::getInstance()->fetchComments($request);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>with comment\n";
        echo var_dump($result)."\n";
        $this->assertIsArray($result);
    }

    protected function tearDown()
    {
        $this->clearDBTable("users");
        $this->clearDBTable("posts");
        $this->clearDBTable("comments");
    }
}