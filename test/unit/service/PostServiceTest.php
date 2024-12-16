<?php

namespace test\unit\service;

use com\linways\core\dto\Like;
use com\linways\core\dto\Post;
use com\linways\core\exception\GeneralException;
use com\linways\core\request\SearchPostRequest;
use com\linways\core\service\PostService;
use test\unit\APITestCase;
use Exception;

class PostServiceTest extends APITestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-users-setup.sql");
        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-posts-setup.sql");
    }

    private function getPost()
    {
        $post = new Post();
        $post->content = "http://dummyimage.com/558x561.png/ff4444/ffffff";
        $post->userId = "c560839d-29c6-4134-9e14-c23fab1d5a5b";
        $post->caption = "Vision-oriented incremental open system";

        return $post;
    }

    private function getLike()
    {
        $like = new Like();
        $like->id = "";
        $like->userId = "3cbf5ad8-d662-4aaa-9ea8-c59564773ae1";
        $like->postId = "a97257d5-6956-4011-866a-ca9dcce4f181";

        return $like;
    }

    //createPost Tests
    public function testCreatePost()
    {
        $post = $this->getPost();

        try {
            $result = PostService::getInstance()->createPost($post);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->assertDatabaseHas("posts", ["id" => $post->id]);
        $this->assertIsString($result->id);
    }

    public function testCreatePostEmptyContent()
    {
        $post = $this->getPost();
        $post->content = "";

        try {
            PostService::getInstance()->createPost($post);
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }
    //('2f8429d0-1fa5-42a7-bbcd-fc53f7adbb2d', '4dd48b7c-dd0f-4b33-a8e1-7e45b98f9c51', 'http://dummyimage.com/593x515.png/ff4444/ffffff'
    public function testEditPost()
    {
        $post = $this->getPost();
        $post->id = "2f8429d0-1fa5-42a7-bbcd-fc53f7adbb2d";
        $post->content = "http://dummyimage.com/593x515.png/444444/fafafa";
        $post->caption = "mechanical real-time parallelism";

        try {
            PostService::getInstance()->editPost($post);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->assertDatabaseHas("posts", ["post" => "http://dummyimage.com/593x515.png/444444/fafafa"]);
        $this->assertDatabaseHasNot("posts", ["post" => "http://dummyimage.com/593x515.png/ff4444/ffffff"]);
        $this->assertDatabaseHas("posts", ["caption" => "mechanical real-time parallelism"]);
        $this->assertDatabaseHasNot("posts", ["caption" => "Organic real-time parallelism"]);
    }

    public function testEditPostEmptyParam()
    {
        $post = $this->getPost();
        $post->id = "2f8429d0-1fa5-42a7-bbcd-fc53f7adbb2d";
        $post->content = "";
        $post->caption = "";

        try {
            PostService::getInstance()->editPost($post);
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }

    //deletePost Tests
    public function testDeletePost()
    {
        try {
            PostService::getInstance()->deletePost("2f8429d0-1fa5-42a7-bbcd-fc53f7adbb2d");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->assertDatabaseHasNot("posts", ["id" => "2f8429d0-1fa5-42a7-bbcd-fc53f7adbb2d"]);
    }

    public function testDeletePostEmptyParam()
    {
        try {
            PostService::getInstance()->deletePost("");
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }

    // fetchPost Tests
    public function testFetchPostWithoutId()
    {

        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-likes-setup.sql");

        $request = new SearchPostRequest();
        $request->id = "";
        try {
            $result = PostService::getInstance()->fetchPosts($request);
        } catch (Exception $e) {
        }

        $this->assertIsArray($result);

        $this->clearDBTable("likes");
    }

    public function testFetchPostUsingId()
    {

        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-likes-setup.sql");

        $request = new SearchPostRequest();
        $request->id = "2f8429d0-1fa5-42a7-bbcd-fc53f7adbb2d";
        try {
            $result = PostService::getInstance()->fetchPosts($request);
        } catch (Exception $e) {
        }

        echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>\n".var_dump($result)."\n";
        $this->assertIsArray($result);


        $this->clearDBTable("likes");
    }


    public function testFetchPostUsingDateRange()
    {

        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-likes-setup.sql");

        $request = new SearchPostRequest();
        $request->fromDate = "2024-01-19 23:28:46";
        $request->toDate = "2024-12-12 12:35:58";
        try {
            $result = PostService::getInstance()->fetchPosts($request);
        } catch (Exception $e) {
        }

        $this->assertIsArray($result);

        $this->clearDBTable("likes");
    }

    public function testFetchPostInvalidDate()
    {
        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-likes-setup.sql");

        $request = new SearchPostRequest();
        $request->fromDate = "2024-12-12 12:35:58";
        $request->toDate = "2024-01-19 23:28:46";

        try {
            PostService::getInstance()->fetchPosts($request);
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::INVALID_DATE_RANGE);
        }

        $this->clearDBTable("likes");
    }

    //LikePost tests
    public function testLikePost()
    {
        $like = $this->getLike();

        try {
            $result = PostService::getInstance()->likePost($like);
        } catch (Exception $e) {
        }

        $this->assertDatabaseHas("likes", ["id" => $result->id]);
    }

    public function testLikePostEmptyParam()
    {

        $like = $this->getLike();

        $like->userId = "";

        try {
            $like = PostService::getInstance()->likePost($like);
        } catch (Exception $e) {

            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }

    //removeLike tests
    public function testRemoveLike()
    {

        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-likes-setup.sql");

        $id = "3a41a9ea-d78d-410f-8ec4-b6037f9b748c";

        try {
            PostService::getInstance()->removeLike($id);
        } catch (Exception $e) {
        }

        $this->assertDatabaseHasNot("likes", ["id" => $id]);

        $this->clearDBTable("likes");
    }

    public function testRemoveLikeEmptyParam()
    {

        $id = "";

        try {
            PostService::getInstance()->removeLike($id);
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }

    protected function tearDown()
    {
        $this->clearDBTable("users");
        $this->clearDBTable("posts");
    }
}
