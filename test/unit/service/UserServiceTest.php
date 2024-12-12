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


        $result = UserService::getInstance()->createUser($user);

        $this->assertIsString($result);
        $this->assertDatabaseHas("users", ["id" => $result]);
    }

    public function testCreateUserServiceExistingUserName()
    {
        $user = $this->getUser();
        $user->uName = "bpleasance1";


        $result = UserService::getInstance()->createUser($user);
        $this->expectExceptionCode(GeneralException::USER_EXISTS);

        $this->assertDatabaseHasNot("users", ["email" => $user->email]);
    }

    public function testCreateUserServiceEmptyParam()
    {
        $user = $this->getUser();
        $user->uName = "";


        $result = UserService::getInstance()->createUser($user);
        $this->expectExceptionCode(GeneralException::EMPTY_PARAMETERS);

        $this->assertDatabaseHasNot("users", ["email" => $user->email]);
    }

    //authenticateUserService tests
    public function testAuthenticateUserService()
    {


        $result = UserService::getInstance()->authenticateUser("bpleasance1", "systematic");

        $this->assertIsString($result);
        $this->assertDatabaseHas("users", ["id" => $result]);
    }

    public function testAuthenticateUserServiceWrongPassword()
    {

        $result = UserService::getInstance()->authenticateUser("bpleasance1", "ayyoooo");
        $this->expectExceptionCode(GeneralException::PASSWORD_MISSMATCH);

        $this->assertTrue($result == null);
    }

    public function testAuthenticateUserServiceEmptyParam()
    {

        $result = UserService::getInstance()->authenticateUser("", "ayyoooo");
        $this->expectExceptionCode(GeneralException::EMPTY_PARAMETERS);
    }


    //deleteUser Tests
    public function testDeleteUser()
    {

        UserService::getInstance()->deleteUser("smaundrell8", "protocolsmania");

        $this->assertDatabaseHasNot("users", ["u_name" => "smaundrell8"]);
    }

    public function testDeleteUserEmptyParam()
    {

        UserService::getInstance()->deleteUser("smaundrell8", "");
        $this->expectExceptionCode(GeneralException::EMPTY_PARAMETERS);

        $this->assertDatabaseHas("users", ["u_name" => "smaundrell8"]);
    }

    public function testDeleteUserWrongPassword()
    {

        UserService::getInstance()->deleteUser("smaundrell8", "");
        // echo $e->getMessage();
        $this->expectExceptionCode(GeneralException::PASSWORD_MISSMATCH);
        $this->assertDatabaseHas("users", ["u_name" => "smaundrell8"]);
    }

    //editUserInfo Tests
    public function testEditUserInfo()
    {
        $userCath = $this->getUser();
        $userCath->id = "e0c326ec-a3b3-4e94-bcd0-df6fcbc210b9";
        $userCath->password = "opensystem";


        UserService::getInstance()->editUserInfo($userCath);

        $this->assertDatabaseHas("users", ["u_name" => $userCath->uName]);
        $this->assertDatabaseHasNot("users", ["u_name" => "ccrathern2"]);
        $this->assertDatabaseHas("users", ["email" => $userCath->email]);
        $this->assertDatabaseHasNot("users", ["email" => "deberlein2@webmd.com"]);
    }

    public function testEditUserInfoUsername()
    {
        $userCath = $this->getUser();
        $userCath->id = "e0c326ec-a3b3-4e94-bcd0-df6fcbc210b9";
        $userCath->email = "";
        $userCath->password = "opensystem";


        UserService::getInstance()->editUserInfo($userCath);

        $this->assertDatabaseHas("users", ["u_name" => $userCath->uName]);
        $this->assertDatabaseHasNot("users", ["u_name" => "ccrathern2"]);
    }

    public function testEditUserInfoEmail()
    {
        $userCath = $this->getUser();
        $userCath->id = "e0c326ec-a3b3-4e94-bcd0-df6fcbc210b9";
        $userCath->uName = "";
        $userCath->password = "opensystem";


        UserService::getInstance()->editUserInfo($userCath);

        $this->assertDatabaseHas("users", ["email" => $userCath->email]);
        $this->assertDatabaseHasNot("users", ["email" => "deberlein2@webmd.com"]);
    }

    public function testEditUserInfoEmptyParams()
    {
        $user = $this->getUser();
        $user->uName = "";
        $user->email = "";


        UserService::getInstance()->editUserInfo($user);
        $this->expectExceptionCode(GeneralException::EMPTY_PARAMETERS);
    }

    public function testEditUserInfoWrongPassword()
    {

        $userCath = $this->getUser();
        $userCath->id = "e0c326ec-a3b3-4e94-bcd0-df6fcbc210b9";
        $userCath->password = "closedSystem";


        UserService::getInstance()->editUserInfo($userCath);

        $this->expectExceptionCode(GeneralException::PASSWORD_MISSMATCH);
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


        $result = UserService::getInstance()->searchUsers($request);

        $expected = [
            (object) ['id' => 'c560839d-29c6-4134-9e14-c23fab1d5a5b', 'u_name' => 'bpleasance1'],
            (object) ['id' => '5c371307-f658-47e3-b7d7-c83ad23f6417', 'u_name' => 'dlegallo0'],
            (object) ['id' => '3cbf5ad8-d662-4aaa-9ea8-c59564773ae1', 'u_name' => 'sdelatremoille7']
        ];

        echo var_dump($result);
        $this->assertIsArray($result->userArray);
        $this->assertTrue($result->userArray == $expected);
        $this->assertTrue($result->postsArray == null);
    }

    public function testSearchUsersSingle()
    {
        $request = new SearchUserRequest();

        $request->id = "5c371307-f658-47e3-b7d7-c83ad23f6417";


        $result = UserService::getInstance()->searchUsers($request);

        $expected = [
            (object)[
                "id" => "5c371307-f658-47e3-b7d7-c83ad23f6417",
                "u_name" => "dlegallo0",
                "email" => "rcarley0@theatlantic.com",
                "profile_picture" => "https://robohash.org/corporisautanimi.png?size=50x50&set=set1",
                "bio" => "Quality-focused homogeneous architecture"
            ]
        ];

        echo var_dump($result);
        $this->assertIsArray($result);
        $this->assertTrue($result->userArray == $expected);
    }

    protected function tearDown()
    {
        $this->clearDBTable("users");
    }
}
