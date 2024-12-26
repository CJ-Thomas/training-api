<?php

namespace com\linways\api\v1\post\controller;


use Slim\Http\Request;
use Slim\Http\Response;
use com\linways\api\v1\controller\BaseController;
use com\linways\core\dto\Like;
use com\linways\core\dto\Post;
use com\linways\core\request\SearchPostRequest;
use com\linways\core\service\PostService;
use Exception;
use Linways\Slim\Utils\ResponseUtils;

class PostController extends BaseController
{

    private function uploadToS3(){
        $color1 = dechex(rand(0x000000, 0xFFFFFF));
        $color2 = dechex(rand(0x000000, 0xFFFFFF));
        $str = "https://dummyimage.com/350x350/$color1/$color2";

        return $str;
    }
    
    protected function createPost(Request $request, Response $response)
    {

        $post = new Post();

        $param = $request->getParsedBody();

        $activeSessionUser = $param["userId"];

        //check for active session 
        //get userId from active session

        $post->userId = $activeSessionUser;

        //upload content to some online storage($param["content"])
        $content = $this->uploadToS3();

        $post->content = $content;
        $post->caption = $param["caption"];
        $post->timeStamp = date('Y-m-d H:i:s');

        try {
            $post = PostService::getInstance()->createPost($post);

            return $response->withJson($post);
        } catch (Exception $e) {
            return ResponseUtils::fault($response, $e);
        }
    }

    protected function editPost(Request $request, Response $response)
    {
        
        $post = new Post();

        $param = $request->getParsedBody();

        $post->id = $request->getAttribute("id");
        $post->content = $param["content"];
        $post->caption = $param["caption"];

        try {
            PostService::getInstance()->editPost($post);
        } catch (Exception $e) {
            return ResponseUtils::fault($response, $e);
        }
    }

    protected function deletePost(Request $request, Response $response)
    {

        $id = $request->getAttribute("id");

        try {
            PostService::getInstance()->deletePost($id);
        } catch (Exception $e) {
            return ResponseUtils::fault($response, $e);
        }
    }

    protected function fetchPost(Request $request, Response $response)
    {
        $searchPostRequest = new SearchPostRequest();

        $params = $request->getParams();

        $searchPostRequest->id = $request->getAttribute("id");
        $searchPostRequest->fromDate = $params["fromDate"];
        $searchPostRequest->toDate = $params["toDate"];

        try {
            $result = PostService::getInstance()->fetchPosts($searchPostRequest);
        } catch (Exception $e) {
            return ResponseUtils::fault($response, $e);
        }

        return $response->withJson($result);
    }

    protected function interactWithPost(Request $request, Response $response)
    {

        if ($request->isPost()) {
            $params = $request->getParsedBody();
            $like = new Like();
            $like->userId = $params["userId"];
            $like->postId = $params["postId"];


            try {

                $result = PostService::getInstance()->likePost($like);

                return $response->withJson($result);

            } catch (Exception $e) {

                return ResponseUtils::fault($response, $e);
            }
        } elseif ($request->isDelete()) {

            $id = $request->getAttribute("id");

            try {

                PostService::getInstance()->removeLike($id);

            } catch (Exception $e) {

                return ResponseUtils::fault($response, $e);
            }
        }
    }
}
