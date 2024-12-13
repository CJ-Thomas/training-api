<?php

namespace test\unit\service;

use com\linways\core\dto\User;
use com\linways\core\exception\GeneralException;
use com\linways\core\request\SearchUserRequest;
use com\linways\core\service\UserService;
use com\linways\core\util\UuidUtil;
use test\unit\APITestCase;
use Exception;

class UserServiceTest extends APITestCase
{



    protected function setUp()
    {
        parent::setUp();
        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-users-setup.sql");
    }

    private function getUser()
    {
        $user = new User();
        $user->id = UuidUtil::guidv4();
        $user->uName = "sikePotatoe";
        $user->email = "example@gmail.com";
        $user->password = "12345678";
        $user->bio = "ooooooooo";
        $user->role = "user";

        return $user;
    }

    //createUserService Tests
    public function testCreateUserService()
    {
        $user = $this->getUser();

        try {

            $result = UserService::getInstance()->createUser($user);
        } catch (Exception $e) {
            echo $e->getCode();
        }

        $this->assertIsString($result);
        $this->assertDatabaseHas("users", ["id" => $result]);
    }

    public function testCreateUserServiceInvalidEmail(){
        $user = $this->getUser();
        $user->email = "bpleasance1";

        try {

            $result = UserService::getInstance()->createUser($user);
        } catch (Exception $e) {
            echo $e->getCode();
            $this->assertEquals($e->getMessage(), "email must be valid email");
        }

        $this->assertDatabaseHasNot("users", ["email" => $user->email]);
    }

    public function testCreateUserServiceExistingUserName()
    {
        $user = $this->getUser();
        $user->uName = "bpleasance1";

        try {

            $result = UserService::getInstance()->createUser($user);
        } catch (Exception $e) {
            echo $e->getCode();
            $this->assertEquals($e->getCode(), GeneralException::USER_EXISTS);
        }

        $this->assertDatabaseHasNot("users", ["email" => $user->email]);
    }

    public function testCreateUserServiceEmptyParam()
    {
        $user = $this->getUser();
        $user->uName = "";

        try {

            $result = UserService::getInstance()->createUser($user);
        } catch (Exception $e) {
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }

        $this->assertDatabaseHasNot("users", ["email" => $user->email]);
    }

    //authenticateUserService tests
    public function testAuthenticateUserService()
    {
        try {

            $result = UserService::getInstance()->authenticateUser("bpleasance1", "systematic");
        } catch (Exception $e) {
            echo $e->getCode();
        }

        $this->assertIsString($result);
        $this->assertDatabaseHas("users", ["id" => $result]);
    }

    public function testAuthenticateUserServiceWrongPassword()
    {
        try {

            $result = UserService::getInstance()->authenticateUser("bpleasance1", "ayyooo");
        } catch (Exception $e) {
            echo $e->getCode();
            $this->assertEquals($e->getCode(), GeneralException::PASSWORD_MISSMATCH);
        }
    }

    public function testAuthenticateUserServiceEmptyParam()
    {
        try {

            $result = UserService::getInstance()->authenticateUser("", "ayyoooo");
        } catch (Exception $e) {
            echo $e->getCode();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }


    //deleteUser Tests
    public function testDeleteUser()
    {
        try {

            UserService::getInstance()->deleteUser("smaundrell8", "protocolsmania");
        } catch (Exception $e) {
            echo $e->getCode();
        }

        $this->assertDatabaseHasNot("users", ["u_name" => "smaundrell8"]);
    }

    public function testDeleteUserEmptyParam()
    {
        try {

            UserService::getInstance()->deleteUser("smaundrell8", "");
        } catch (Exception $e) {
            echo $e->getCode();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }

        $this->assertDatabaseHas("users", ["u_name" => "smaundrell8"]);
    }

    public function testDeleteUserWrongPassword()
    {

        try {

            UserService::getInstance()->deleteUser("smaundrell8", "ayyooo");
        } catch (Exception $e) {
            echo $e->getCode();
            $this->assertEquals($e->getCode(), GeneralException::PASSWORD_MISSMATCH);
        }
        // echo $e->getMessage();
        $this->assertDatabaseHas("users", ["u_name" => "smaundrell8"]);
    }

    //editUserInfo Tests
    public function testEditUserInfo()
    {
        $userCath = $this->getUser();
        $userCath->id = "e0c326ec-a3b3-4e94-bcd0-df6fcbc210b9";
        $userCath->password = "opensystem";

        try {

            UserService::getInstance()->editUserInfo($userCath);
        } catch (Exception $e) {
            echo $e->getCode();
        }

        $this->assertDatabaseHas("users", ["u_name" => $userCath->uName]);
        $this->assertDatabaseHasNot("users", ["u_name" => "ccrathern2"]);
        $this->assertDatabaseHas("users", ["email" => $userCath->email]);
        $this->assertDatabaseHasNot("users", ["email" => "deberlein2@webmd.com"]);
    }


    public function testEditUserInfoEmptyParams()
    {
        $user = $this->getUser();
        $user->uName = "";
        $user->email = "";

        try {

            UserService::getInstance()->editUserInfo($user);
        } catch (Exception $e) {
            echo $e->getCode();
            $this->assertEquals($e->getCode(), GeneralException::EMPTY_PARAMETERS);
        }
    }

    public function testEditUserInfoWrongPassword()
    {

        $userCath = $this->getUser();
        $userCath->id = "e0c326ec-a3b3-4e94-bcd0-df6fcbc210b9";
        $userCath->password = "closedSystem";

        try {

            UserService::getInstance()->editUserInfo($userCath);
        } catch (Exception $e) {
            echo $e->getCode();
            $this->assertEquals($e->getCode(), GeneralException::PASSWORD_MISSMATCH);
        }

        $this->assertDatabaseHas("users", ["u_name" => "ccrathern2"]);
        $this->assertDatabaseHas("users", ["email" => "deberlein2@webmd.com"]);
        $this->assertDatabaseHasNot("users", ["u_name" => $userCath->uName]);
        $this->assertDatabaseHasNot("users", ["email" => $userCath->email]);
    }

    //searchUser Tests
    public function testSearchUsersMultiple()
    {
        $request = new SearchUserRequest();

        $request->searchName = "le";

        try {

            $result = UserService::getInstance()->searchUsers($request);
        } catch (Exception $e) {
            echo $e->getCode();
        }

        $expected = [
            (object) ['id' => 'c560839d-29c6-4134-9e14-c23fab1d5a5b', 'u_name' => 'bpleasance1'],
            (object) ['id' => '5c371307-f658-47e3-b7d7-c83ad23f6417', 'u_name' => 'dlegallo0'],
            (object) ['id' => '3cbf5ad8-d662-4aaa-9ea8-c59564773ae1', 'u_name' => 'sdelatremoille7']
        ];

        $this->assertIsArray($result->userArray);
        $this->assertTrue($result->userArray == $expected);
        $this->assertTrue($result->postsArray == null);
    }

    public function testSearchUsersSingle()
    {
        $this->setInitialDataUsingSQLFile(__DIR__ . "/initial-posts-setup.sql");

        $request = new SearchUserRequest();
        $request->id = "4dd48b7c-dd0f-4b33-a8e1-7e45b98f9c51";

        try {

            $result = UserService::getInstance()->searchUsers($request);
        } catch (Exception $e) {
            echo $e->getCode();
        }


        // better way to compare values?
        $expectedUser = [
            (object)[
                "id" => "4dd48b7c-dd0f-4b33-a8e1-7e45b98f9c51",
                "u_name" => "hlongwood5",
                "email" => "cramelot5@mozilla.com",
                "profile_picture" => "https://robohash.org/ipsumvelexercitationem.png?size=50x50&set=set1",
                "bio" => "Focused encompassing synergy"
            ]
        ];

        $expectedPosts = [
            (object) [
                "id" => "2f8429d0-1fa5-42a7-bbcd-fc53f7adbb2d",
                "post" => "http://dummyimage.com/593x515.png/ff4444/ffffff",
                "caption" => "Organic real-time parallelism"
            ],
            (object) [
                "id" => "c0634088-c082-4bbb-bd2d-49a5062c082e",
                "post" => "http://dummyimage.com/558x561.png/ff4444/ffffff",
                "caption" => "Reduced eco-centric utilisation"
            ],
            (object) [
                "id" => "f82e9c1e-574c-4fac-aee5-dd4ace6a701c",
                "post" => "http://dummyimage.com/558x511.png/cc0000/ffffff",
                "caption" => "Inverse dedicated help-desk"
            ]
        ];

        $this->assertIsArray($result->userArray);
        $this->assertIsArray($result->postsArray);
        $this->assertEquals($expectedUser, $result->userArray);
        $this->assertEquals($expectedPosts, $result->postsArray);

        $this->clearDBTable("posts");
    }

    protected function tearDown()
    {
        $this->clearDBTable("users");
    }
}
