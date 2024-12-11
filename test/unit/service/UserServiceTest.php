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
        // $this->setInitialDataUsingSQLFile(__DIR__."/initial-sql-setup.sql");
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
        
        $user = $this->getUser("sookpotatoe");
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
        
        $user = $this->getUser("wasawasawasa");
        $password = $user->password;
        
        try{
            
            UserService::getInstance()->createUser($user);
            
            UserService::getInstance()->deleteUser($user->uName, $password);
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
        
        $this->assertDatabaseHasNot("users", [
            "id" => $user->id
        ]);
        $this->clearDBTable("users");        
    }
    
    // public function testsearchUsers(){
        
    //     try{
            
            
            
    //     } catch(Exception $e){
    //         echo $e->getMessage();
    //     }
        
    //     $this->assertIsArray($result1);
    //     $this->assertIsArray($result2);
        
    //     $this->clearDBTable("users");
    // }
    

    protected function tearDown(){
        $this->clearDBTable("users");
    }
}
?>