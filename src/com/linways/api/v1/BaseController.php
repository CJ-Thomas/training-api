<?php
    namespace com\linways\api\v1\controller;

    use com\linways\core\constant\UserType;
    use Linways\Slim\Controller\BaseController as SlimBaseController;
    use Linways\Slim\Exception\CoreException;
    use com\linways\nucleus\core\service\PermissionService;
    
    class BaseController extends SlimBaseController
    {
        public $isPermissionsRequired = false;
    
        public function __construct() {
            global $container;
            parent::__construct($container);
            session_start();
            // Converting staffId, adminId or studentId into a common variable
            if ( isset ( $_SESSION['staffID'] ) || isset ( $_SESSION['adminID'] ) ) {
                $GLOBALS['userId'] = $_SESSION['staffID'] ? $_SESSION['staffID'] : $_SESSION['adminID'];
                $GLOBALS['userType'] = UserType::STAFF;
            } 
            else if ( isset ( $_SESSION['studentID'] ) || isset ( $_SESSION['applicantId'] ) ) {
                $GLOBALS['userId'] = $_SESSION['studentID'] ? $_SESSION['studentID'] : $_SESSION['applicantId'];
                $GLOBALS['userType'] = UserType::STUDENT;
            } 
            else {
                // exit("Permission Denied");
            }
        }
        
        public function hasPermission($permissions) {
            return true;
            $headers = apache_request_headers();
            $apiAccessToken = empty($headers['authorization']) ? $headers['Authorization'] : $headers['authorization'];
            $hasPermission = true;
            $apiAccessToken =  $_SERVER['HTTP_AUTHORIZATION'];
            foreach ($permissions as $perm) {
                try {
                    $hasSinglePerm = PermissionService::getInstance()->APIHasPermission($apiAccessToken, $perm);
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    $hasSinglePerm = false;
                }
                $hasPermission = $hasPermission && $hasSinglePerm;
            }
            return $hasPermission;
        }
    }
?>