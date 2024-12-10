<?php

namespace test\unit\service;

use com\linways\core\dto\User;
use com\linways\core\service\UserService;
use com\linways\core\util\UuidUtil;
use test\unit\APITestCase;
use Exception;


class UserServiceTest extends APITestCase{
    


    protected function setUp()
    {
        parent::setUp();
    }

    public function getUser($uName="sikePotatoe"){
        $user = new User();
        $user->id = UuidUtil::guidv4();
        $user->uName = $uName;
        $user->email = "example@gmail.com";
        $user->password = "12345678";
        $user->bio = "ooooooooo";
        $user->role = "user";

        return $user;   
    }


    public function testCheckUserExist(){
        
        $user = $this->getUser();
        try{
            
            UserService::getInstance()->createUser($user);
            $result = UserService::getInstance()->checkUserExist($user->uName);

        } catch(Exception $e) {
            echo $e->getMessage();
        }

        $this->assertIsBool($result);

        $this->clearDBTable("users");
    }

    public function testCreateUserService(){
        $user = $this->getUser();
        
        try{
            $result = UserService::getInstance()->createUser($user);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        
        $this->assertIsString($result);
        
        $this->clearDBTable("users");
        
    }
    
    public function testAuthenticateUserName(){
        
        $user = $this->getUser();
        $password = $user->password;
        try{
            
            UserService::getInstance()->createUser($user);
            
            $result = UserService::getInstance()->authenticateUser($user->uName, $password);
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
        
        $this->assertIsString($result);
        
        $this->clearDBTable("users");
    }
    
    public function testDeleteUser(){
        
        $user = $this->getUser();
        $password = $user->password;
        
        try{
            
            UserService::getInstance()->createUser($user);
            
            UserService::getInstance()->deleteUser($user->uName, $user->email, $password);
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
        
        $this->assertDatabaseHasNot("users", [
            "id" => $user->id
        ]);
        
        
    }
    
    public function testGetAllUsers(){
        
        try{
            
            UserService::getInstance()->createUser($this->getUser("googlymoogly"));
            UserService::getInstance()->createUser($this->getUser("sikePotatoe"));
            UserService::getInstance()->createUser($this->getUser("weuuweuuweuu"));
            
            $result1 = UserService::getInstance()->getAllUsers();
            $result2 = UserService::getInstance()->getAllUsers("o");
            
        } catch(Exception $e){
            echo $e->getMessage();
        }
        
        $this->assertIsArray($result1);
        $this->assertIsArray($result2);

        $this->clearDBTable("users");
        
        
        
    }
    
    public function testGetUserDetails(){
        
        $user = $this->getUser();
        
        try{
            
            UserService::getInstance()->createUser($user);
            
            $result = UserService::getInstance()->getUserDetails($user->uName);

        } catch(Exception $e) {
            echo $e->getMessage();
        }

        $this->assertIsObject($result);
        $this->clearDBTable("users");

    }

    protected function tearDown(){
        
    }
}
?>