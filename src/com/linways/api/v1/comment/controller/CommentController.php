<?php

namespace com\linways\api\v1\comment\controller;

use Slim\Http\Request;
use Slim\Http\Response;
use com\linways\api\v1\controller\BaseController;
use com\linways\core\dto\Comment;
use com\linways\core\request\SearchCommentRequest;
use com\linways\core\service\CommentService;
use Linways\Slim\Utils\ResponseUtils;
use Exception;


class CommentController extends BaseController{
    
    protected function create(Request $request, Response $response){
        $params = $request->getParsedBody();

        $comment = new Comment();

        $comment->userId = $params["useId"];
        $comment->postId = $params["postId"];
        $comment->content = $params["content"];
        $comment->parentCommentId = $params["parentCommentId"];

        try{

            $comment = CommentService::getInstance()->createComment($comment);

            return $response->withJson($comment);

        }  catch (Exception $e) {

            return ResponseUtils::fault($response, $e);
        }

    }
    
    protected function edit(Request $request, Response $response){

        $comment = new Comment();
        
        $comment->id = $request->getAttribute("id");
        $comment->content = $request->getParsedBodyParam("content");

        try {
            CommentService::getInstance()->editComment($comment);
        } catch (Exception $e) {
            return ResponseUtils::fault($response, $e);
        }

    }

    protected function delete(Request $request, Response $response){

        $id = $request->getAttribute("id");

        try {
            CommentService::getInstance()->deleteComment($id);
        } catch (Exception $e) {
            //response utils
            return ResponseUtils::fault($response, $e);
        }
    }

    protected function fetch(Request $request, Response $response){

        $searchComment = new SearchCommentRequest();

        $params = $request->getParams();

        $searchComment->id = $params["id"];
        $searchComment->postId = $params["postId"];
        $searchComment->userId = $params["userId"];
        $searchComment->parentCommentId = $params["parentCommentId"];
        $searchComment->searchComment = $params["searchComemnt"];

        try {
            $result = CommentService::getInstance()->fetchComment($searchComment);

            return $response->withJson($result);
        } catch (Exception $e) {
            //response utils
            return ResponseUtils::fault($response, $e);
        }

    }
}