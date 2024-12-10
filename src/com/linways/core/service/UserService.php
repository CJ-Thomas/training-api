<?php
    namespace com\linways\core\service;

    use com\linways\base\util\MakeSingletonTrait;
    use com\linways\core\dto\User;
    use com\linways\core\util\UuidUtil;
    use com\linways\core\mapper\UserServiceMapper;
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
        public function checkUserExist(string $uName){
            
            $uName = $this->realEscapeString($uName);

            $query = "SELECT id FROM users WHERE u_name = '$uName';";

            $result = ($this->executeQuery($query))->sqlResult;

            if($result->num_rows == 1)
                return true;

            return false;
        }

        /**
         * Create a new user
         * @param  User $user
         * @return string
         */
        public function createUser(User $user) {
            $user = $this->realEscapeObject($user); 
            $user->createdBy = $GLOBALS["userId"] ?? $user->createdBy;
            $user->updatedBy = $GLOBALS["userId"] ?? $user->updatedBy;

            $this->validateUser($user);

            $user->password = password_hash($user->password,PASSWORD_DEFAULT);

            $user->id = UuidUtil::guidv4();
            $user->role = "user";
            $user->profilePicture = "newLinkAfterUploadingToS3Bucket/Alternatives";


            $query = "INSERT INTO users (id, u_name, email, password, profile_picture, bio, role, created_by,
            updated_by)VALUES('$user->id', '$user->uName', '$user->email', '$user->password', '$user->profilePicture',
            '$user->bio', '$user->role', '$user->createdBy', '$user->updatedBy');";

            try{
                $this->executeQuery($query);
            }catch(Exception $e){
                throw new Exception("UNABLE TO INSERT INTO DB");
            }

            return $user->id;
        }

        /**
         * Login/ Authenticate user
         * @param string $userName
         * @param string $password
         * @return string
         */
        public function authenticateUser(string $userName, string $password){
            
            $userName = $this-> realEscapeString($userName);
            $password = $this-> realEscapeString($password);
            
            
            if(empty($userName)||empty($password))
                return "";

            $query = "SELECT id, password FROM users WHERE u_name = '$userName';";

            try{
                $result = ($this->executeQuery($query))->sqlResult->fetch_object();
            }catch(\Exception $e){
                throw $e;
            }

            
            if(!password_verify($password, $result->password))
                return "";

            return $result->id;
            
        }

        /**
         * Delete an existing user
         * @param string $userName
         * @param string $password
         * @param string $email
         * @return Bool
         */
        public function deleteUser(string $userName, string $email, string $password) {
            $userName = $this-> realEscapeString($userName);
            $email = $this-> realEscapeString($email);
            $password = $this-> realEscapeString($password);
            
            if(empty($email)||empty($password))
                return "";
            
            $query1 = "SELECT password FROM users WHERE u_name = '$userName';";
            $query2 = "DELETE FROM users WHERE u_name = '$userName';";

            try{

                $result1 = ($this->executeQuery($query1))->sqlResult->fetch_object();

                if(!password_verify($password, $result1->password))
                    return "";

                $result2 = ($this->executeQuery($query2))->sqlResult;

            }catch(\Exception $e){
                throw $e;
            }

            return $result2;

        }
        
        /**
         * Edit an existing user
         * @param User $userName
         */
        public function editUserInfo(string $userName, string $email, string $bio, string $password){
            $userName = $this->realEscapeString($userName);

            $query = "UPDATE users SET";


                $emailValidator = Validator::attribute("email", validator::email());
                $emailValidator->check($email);
    
        }



        /**
         * Fetching all users
         * @return string[] $users
         */
        public function getAllUsers(string $subString = "", int $limit = 10, int $offSet = 0){
            //code for fetching users

            $query = "SELECT u_name FROM users LIMIT $limit OFFSET $offSet;";

            if($subString)
                $query = "SELECT u_name FROM users WHERE u_name LIKE '%$subString%'
                LIMIT $limit OFFSET $offSet;";

            try{
                
                $result = ($this->executeQuery($query))->sqlResult;
                


                while($row = $result->fetch_row())
                    $uNames[] = $row[0];


            } catch(Exception $e){
                throw $e;
            }

            return $uNames;
        }

        /**
         * fetch user details from user name
         * @param string $uName
         * @return User only uName, email, profilePicture, bio other fields will be empty
         */
        public function getUserDetails(string $uName){
            
            $query = "SELECT u_name, email, profile_picture, bio FROM users
            WHERE u_name LIKE '$uName'";

            try{
                
                $result = ($this->executeQuery($query))->sqlResult->fetch_object();
                
            } catch(Exception $e){
                
                throw $e;
                
            }

            $user = new User();
            
            

            
            return $result;
        }

        private function validateUser(User $user){
        
            if(empty($user->uName) or empty($user->password) or empty($user->email))
                throw new Exception("EMPTY USER FIELD");

            $userValidator = Validator::attribute("uName", validator::stringType()->length(4))
                ->attribute("email", validator::email())
                ->attribute("password", validator::alnum()->noWhitespace());

            $userValidator->check($user);
        
        }
        
    }
?>