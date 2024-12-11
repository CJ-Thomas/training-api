<?php

namespace com\linways\core\service;

use com\linways\base\exception\CoreException;
use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\User;
use com\linways\core\exception\ParameterException;
use com\linways\core\exception\UserException;
use com\linways\core\util\UuidUtil;
use com\linways\core\mapper\UserServiceMapper;
use com\linways\core\request\SearchUserRequest;
use Respect\Validation\Validator;
use Exception;


class UserService extends BaseService
{
    use MakeSingletonTrait;

    private $mapper;

    private function __construct()
    {
        $this->mapper = UserServiceMapper::getInstance()->getMapper();
    }


    /**
     * function to check user name already exists
     * return true if username exists
     * @param string $uName
     * @return Bool
     */
    private function checkUserExist(string $uName)
    {

        $uName = $this->realEscapeString($uName);

        $query = "SELECT id FROM users WHERE u_name = '$uName';";

        $result = $this->executeQuery($query,TRUE);

        if (!empty($result))
            return true;

        return false;
    }

    /**
     * Create a new user
     * @param  User $user
     * @return string
     */
    public function createUser(User $user)
    {
        $user = $this->realEscapeObject($user);
        $user->createdBy = $GLOBALS["userId"] ?? $user->createdBy;
        $user->updatedBy = $GLOBALS["userId"] ?? $user->updatedBy;

        $this->validateUser($user);

        if($this->checkUserExist($user->uName))
            throw new UserException(UserException::USER_EXISTS,"user already exisits");

        $user->password = password_hash($user->password, PASSWORD_DEFAULT);

        $user->id = UuidUtil::guidv4();
        $user->role = "user";
        $user->profilePicture = "newLinkAfterUploadingToS3Bucket/Alternatives";


        $query = "INSERT INTO users (id, u_name, email, password, profile_picture, bio, role, created_by,
            updated_by)VALUES('$user->id', '$user->uName', '$user->email', '$user->password', '$user->profilePicture',
            '$user->bio', '$user->role', '$user->createdBy', '$user->updatedBy');";

        try {
            $this->executeQuery($query);
        } catch (Exception $e) {
            throw $e;
        }

        return $user->id;
    }

    /**
     * Login/ Authenticate user
     * @param string $userName
     * @param string $password
     * @return string id of the user
     */
    public function authenticateUser(string $userName, string $password)
    {

        $userName = $this->realEscapeString($userName);
        $password = $this->realEscapeString($password);


        if (empty($userName) || empty($password))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS, "missing parameters");

        $query = "SELECT id, password FROM users WHERE u_name = '$userName';";

        try {
            $result = $this->executeQueryForObject($query);
        } catch (Exception $e) {
            throw $e;
        }

        
        if (!password_verify($password, $result->password))
            throw new UserException(UserException::PASSWORD_MISSMATCH,"entered password does not match");

        return $result->id;
    }

    /**
     * Delete an existing user
     * @param string $userName
     * @param string $password
     * @return bool
     */
    public function deleteUser(string $userName, string $password)
    {
        $userName = $this->realEscapeString($userName);
        $password = $this->realEscapeString($password);

        if (empty($userName) || empty($password))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS, "missing parameters");

        $passwordSelectQuery = "SELECT password FROM users WHERE u_name = '$userName';";
        $deleteQuery = "DELETE FROM users WHERE u_name = '$userName';";

        try {
            $passwordResult = $this->executeQueryForObject($passwordSelectQuery);

            if (!password_verify($password, $passwordResult->password))
                throw new UserException(UserException::PASSWORD_MISSMATCH,"entered password does not match");

            $deleteResult = $this->executeQueryForObject($deleteQuery);
        } catch (Exception $e) {
            throw $e;
        }

        return $deleteResult;
    }

    /**
     * Edit an existing user
     * @param User $userName
     */
    public function editUserInfo(User $user)
    {
        $user = $this->realEscapeObject($user);

        if ((empty($user->uName) && empty($user->email)) || empty($user->password))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS, "missing parameters");

        $passwordSelectQuery = "SELECT password FROM users WHERE id = '$user->id';";

        if (!empty($userName)) {
            Validator::alnum()->noWhitespace()->length(4, 30)->check($userName);
            $columnArray[] =" u_name = '$userName'";
        }

        if (!empty($email)) {
            Validator::email()->check($email);
            $columnArray[] =" email = '$email'";
        }

        $updateQuery = "UPDATE users SET".implode(",",$columnArray)." WHERE id LIKE '$user->id';";

        try {
            $passwordResult = $this->executeQueryForObject($passwordSelectQuery);

            if (!password_verify($user->password, $passwordResult->password))
                throw new UserException(UserException::PASSWORD_MISSMATCH,"enterd password does not match");


            $this->executeQuery($updateQuery);
        } catch (Exception $e) {
            throw $e;
        }
    }



    /**
     * Fetching users
     * @param SearchUserRequest
     * @return Object[("id"=>string, "u_name"=>string)] $userArray
     */
    public function searchUsers(SearchUserRequest $request)
    {
        $request = $this->realEscapeObject($request);

        $whereQuery = (!empty($request->name))? "WHERE u_name LIKE '%$request->name%' ":"";
        $limitQuery = "LIMIT $request->startIndex, $request->endIndex;";

        $query = "SELECT id, u_name FROM users ".$whereQuery.$limitQuery;

        try {
            $userArray = $this->executeQueryForList($query);
        } catch (Exception $e) {
            throw $e;
        }

        return $userArray;
    }


    private function validateUser(User $user)
    {

        if (empty($user->uName) || empty($user->password) || empty($user->email))
            throw new ParameterException(ParameterException::EMPTY_PARAMETERS,"miss paramters");

        $userValidator = Validator::attribute("uName", validator::stringType()->length(4, 30))
            ->attribute("email", validator::email())
            ->attribute("password", validator::alnum()->noWhitespace());

        $userValidator->check($user);
    }
}
