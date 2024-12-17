<?php

namespace com\linways\api\v1\user\controller;

use Slim\Http\Request;
use Slim\Http\Response;
use com\linways\api\v1\controller\BaseController;
use Linways\Slim\Utils\ResponseUtils;
use com\linways\core\dto\User;
use com\linways\core\request\SearchUserRequest;
use com\linways\core\service\UserService;
use Exception;

class UserController extends BaseController
{
    // public $permissons_register = []; // why this variable

    protected function register(Request $request, Response $response)
    {


        $user = new User();

        $params = $request->getParsedBody();

        $user->uName = $params["uName"];
        $user->email = $params["email"];
        $user->password = $params["password"];
        //code to upload to s3 bucket or someother online storage
        $user->profilePicture = "s3BucketLink";
        $user->bio = $params["bio"];
        $user->role = "user";

        try {

            $user->id = UserService::getInstance()->createUser($user);

            $user->password = "";
            return $response->withJson($user);
        } catch (Exception $e) {

            //response utils
            return ResponseUtils::fault($response, $e);
        }
    }

    protected function login(Request $request, Response $response)
    {

        $userName = $request->getParsedBodyParam("uName");
        $password = $request->getParsedBodyParam("password");

        try {

            $id = UserService::getInstance()->authenticateUser($userName, $password);

            //create a new session using jwt
            //redirection
            return $response;
        } catch (Exception $e) {

            //response utils
            return ResponseUtils::fault($response, $e);
        }
    }

    protected function logout(Request $request, Response $response)
    {
        if ($request->isPost()) {
            //check for session 
            //code to delete a session -- ask how?????
        }
    }

    protected function edit(Request $request, Response $response)
    {

        //check for session

        $params = $request->getParsedBody();

        $user = new User();

        $user->id = $request->getAttribute('id');
        $user->uName = $params["uName"];
        $user->email = $params["email"];
        $user->password = $params["password"];

        try {
            UserService::getInstance()->editUserInfo($user);
        } catch (Exception $e) {

            //response utils
            return ResponseUtils::fault($response, $e);
        }
    }

    protected function delete(Request $request, Response $response)
    {
        if ($request->isDelete()) {

            //check for session

            $user = new User();

            $params = $request->getParsedBody();

            $user->id = $request->getAttribute("id");
            $user->uName = $params["uName"];
            $user->password = $params["password"];

            try {
                UserService::getInstance()->deleteUser($user);
            } catch (Exception $e) {

                //response utils
                return ResponseUtils::fault($response, $e);
            }
        }
    }

    protected function fetch(Request $request, Response $response)
    {

        //check for session
        $searchUser = new SearchUserRequest();

        $searchUser->id = $request->getAttribute("id");
        $searchUser->searchName = $request->getParam("searchName");

        try {

            $result = UserService::getInstance()->searchUsers($searchUser);

            return $response->withJson($result);
        } catch (Exception $e) {
            return ResponseUtils::fault($response, $e);
        }
    }
}
