<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\User;
use com\linways\core\exception\GeneralException;
use com\linways\core\util\UuidUtil;
use com\linways\core\mapper\UserServiceMapper;
use com\linways\core\request\ChangePasswordRequest;
use com\linways\core\request\SearchUserRequest;
use com\linways\core\response\UserResponse;
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
        try {
            $result = $this->executeQueryForObject($query);
        } catch (Exception $e) {
            throw $e;
        }

        if (!empty($result->id))
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

        if ($this->checkUserExist($user->uName))
            throw new GeneralException(GeneralException::USER_EXISTS, "user already exisits");

        $user->password = password_hash($user->password, PASSWORD_DEFAULT);

        $user->id = UuidUtil::guidv4();
        $user->role = "user";


        $query = "INSERT INTO users (id, u_name, email, password, profile_picture, bio, role, created_by,
            updated_by)VALUES('$user->id', '$user->uName', '$user->email', '$user->password', '$user->profilePicture',
            '$user->bio', '$user->role', '$user->createdBy', '$user->updatedBy');";

        try {
            $result = $this->executeQuery($query, True);
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
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing parameters");

        $query = "SELECT id, email, bio, password FROM users WHERE u_name = '$userName';";

        try {
            $result = $this->executeQueryForObject($query);
        } catch (Exception $e) {
            throw $e;
        }

        if (!password_verify($password, $result->password))
            throw new GeneralException(GeneralException::PASSWORD_MISSMATCH, "entered password does not match");

        unset($result->password);

        return $result;
    }

    /**
     * Delete an existing user
     * @param User $user
     */
    public function deleteUser(User $user)
    {
        $user = $this->realEscapeObject($user);

        if (empty($user->password))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing parameters");

        $passwordSelectQuery = "SELECT password FROM users WHERE id = '$user->id';";
        $deleteQuery = "DELETE FROM users WHERE id = '$user->id';";

        try {
            $passwordResult = $this->executeQueryForObject($passwordSelectQuery);

            if (!password_verify($user->password, $passwordResult->password))
                throw new GeneralException(GeneralException::PASSWORD_MISSMATCH, "entered password does not match");

            $this->executeQuery($deleteQuery);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit an existing user
     * @param User $userName
     */
    public function editUserInfo(User $user)
    {
        $user = $this->realEscapeObject($user);

        if ((empty($user->uName) && empty($user->email)) || empty($user->password))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing parameters");

        $passwordSelectQuery = "SELECT password FROM users WHERE id = '$user->id';";

        if (!empty($user->uName)) {
            Validator::alnum()->noWhitespace()->length(4, 30)->check($user->uName);
            $columnArray[] = " u_name = '$user->uName'";
        }

        if (!empty($user->email)) {
            Validator::email()->check($user->email);
            $columnArray[] = " email = '$user->email'";
        }

        if($user->bio === "") {
            $columnArray[] = " bio = null";
        } else {
            $columnArray[] = " bio = '$user->bio'";
        } 

        $updateQuery = "UPDATE users SET" . implode(",", $columnArray) . " WHERE id = '$user->id';";

        try {
            $passwordResult = $this->executeQueryForObject($passwordSelectQuery);

            if (!password_verify($user->password, $passwordResult->password))
                throw new GeneralException(GeneralException::PASSWORD_MISSMATCH, "enterd password does not match");


            $this->executeQuery($updateQuery);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * change users password
     * 
     * @param ChangePasswordRequest
     */
    public function changeUserPassword(ChangePasswordRequest $request){
        if(empty($request->currentPassword)||empty($request->newPassword)||empty($request->confirmNewPassword))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing parameters");

        if($request->newPassword !== $request->confirmNewPassword)
            throw new GeneralException(GeneralException::PASSWORD_MISSMATCH, "enterd password does not match");
            
        $passwordSelectQuery = "SELECT password FROM users WHERE id = '$request->id';";

        $newPasswordHash = password_hash($request->newPassword, PASSWORD_DEFAULT);

        $updateQuery = "UPDATE users SET password = '$newPasswordHash' WHERE id = '$request->id'";

        try {
            $passwordResult = $this->executeQueryForObject($passwordSelectQuery);

            if (!password_verify($request->currentPassword, $passwordResult->password))
                throw new GeneralException(GeneralException::PASSWORD_MISSMATCH, "enterd password does not match");


            $this->executeQuery($updateQuery);
        } catch (Exception $e) {
            throw $e;
        }


    }

    /**
     * Fetching users
     * @param SearchUserRequest
     * @return UserResponse
     */
    public function searchUsers(SearchUserRequest $request)
    {
        $request = $this->realEscapeObject($request);

        if (!empty($request->id)) {

            $selectUserQuery = "SELECT u.id, u.u_name, u.email, u.profile_picture, u.bio, p.id as p_id, p.post, p.caption, l.id AS l_id, l.user_id AS l_users
            FROM users u LEFT JOIN posts p ON u.id = p.user_id LEFT JOIN likes l ON p.id = l.post_id 
            WHERE u.id = '$request->id'";

        } else {
            $whereQuery = (!empty($request->searchName)) ? "WHERE u_name LIKE '%$request->searchName%' " : "";
            $limitQuery = "LIMIT $request->startIndex, $request->endIndex;";

            $selectUserQuery = "SELECT id, u_name FROM users " . $whereQuery . $limitQuery;
        }

        $response = new UserResponse();
        try {
            if (!empty($request->id)) {
                $response->userProfile = $this->executeQueryForList($selectUserQuery, $this->mapper[UserServiceMapper::SEARCH_USER]);
            } else {
                $response->usersArray = $this->executeQueryForList($selectUserQuery);
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }

    private function validateUser(User $user)
    {

        if (empty($user->uName) || empty($user->password) || empty($user->email))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "miss paramters");

        $userValidator = Validator::attribute("uName", validator::stringType()->length(4, 30))
            ->attribute("email", validator::email())
            ->attribute("password", validator::alnum()->noWhitespace());

        $userValidator->check($user);
    }
}
