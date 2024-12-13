<?php

namespace com\linways\core\service;

use com\linways\base\util\MakeSingletonTrait;
use com\linways\core\dto\User;
use com\linways\core\exception\GeneralException;
use com\linways\core\mapper\PostServiceMapper;
use com\linways\core\util\UuidUtil;
use com\linways\core\mapper\UserServiceMapper;
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
        $user->profilePicture = "newLinkAfterUploadingToS3Bucket/Alternatives";


        $query = "INSERT INTO users (id, u_name, email, password, profile_picture, bio, role, created_by,
            updated_by)VALUES('$user->id', '$user->uName', '$user->email', '$user->password', '$user->profilePicture',
            '$user->bio', '$user->role', '$user->createdBy', '$user->updatedBy');";

        try {
            // $result_1 = $this->executeQueryForObject($query, TRUE, $this->mapper[UserServiceMapper::SEARCH_USER]);
            // echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>".var_dump($result_1);
            $result = $this->executeQuery($query);
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

        $query = "SELECT id, password FROM users WHERE u_name = '$userName';";

        try {
            $result = $this->executeQueryForObject($query);
        } catch (Exception $e) {
            throw $e;
        }

        if (!password_verify($password, $result->password))
            throw new GeneralException(GeneralException::PASSWORD_MISSMATCH, "entered password does not match");

        return $result->id;
    }

    /**
     * Delete an existing user
     * @param string $userName
     * @param string $password
     */
    public function deleteUser(string $userName, string $password)
    {
        $userName = $this->realEscapeString($userName);
        $password = $this->realEscapeString($password);

        if (empty($userName) || empty($password))
            throw new GeneralException(GeneralException::EMPTY_PARAMETERS, "missing parameters");

        $passwordSelectQuery = "SELECT password FROM users WHERE u_name = '$userName';";
        $deleteQuery = "DELETE FROM users WHERE u_name = '$userName';";

        try {
            $passwordResult = $this->executeQueryForObject($passwordSelectQuery);

            if (!password_verify($password, $passwordResult->password))
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

        $updateQuery = "UPDATE users SET" . implode(",", $columnArray) . " WHERE id LIKE '$user->id';";

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
     * Fetching users
     * @param SearchUserRequest
     * @return UserResponse
     */
    public function searchUsers(SearchUserRequest $request)
    {
        $request = $this->realEscapeObject($request);

        if (!empty($request->id)) {
            $selectUserQuery = "SELECT id, u_name, email, profile_picture, bio FROM users
            WHERE id LIKE '$request->id';";

            $selectUserPostsQuery = "SELECT id, post, caption FROM posts 
            WHERE user_id = '$request->id';";
        } else {
            $whereQuery = (!empty($request->searchName)) ? "WHERE u_name LIKE '%$request->searchName%' " : "";
            $limitQuery = "LIMIT $request->startIndex, $request->endIndex;";

            $selectUserQuery = "SELECT id, u_name FROM users " . $whereQuery . $limitQuery;
        }

        $response = new UserResponse();

        try {
            $response->userArray = $this->executeQueryForList($selectUserQuery);

            if (!empty($request->id)) {
                $response->postsArray = $this->executeQueryForList($selectUserPostsQuery);
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
