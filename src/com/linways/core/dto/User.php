<?php

namespace com\linways\core\dto;

use com\linways\base\dto\BaseDTO;

class User extends BaseDTO{
    
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $uName;
    
    /**
     * @var string
     */
	public $email;

    /**
     * @var string
     */

	public $password;
    
    /**
     * @var string
     */
	public $profilePicture;
    
    /**
     * @var string
     */
	public $bio;

    /**
     * @var string
     */
    public $role; 

    /**
     * @var object[]
     */
    public $posts;
}
?>