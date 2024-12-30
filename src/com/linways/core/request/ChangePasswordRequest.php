<?php
namespace com\linways\core\request;

use com\linways\base\request\BaseRequest;

class ChangePasswordRequest extends BaseRequest{
    
    /**
     * @var String
     */
    public $id;

    /**
     * @var String
     */
    public $currentPassword;

    /**
     * @var String
     */
    public $newPassword;

    /**
     * @var String
     */
    public $confirmNewPassword;


}