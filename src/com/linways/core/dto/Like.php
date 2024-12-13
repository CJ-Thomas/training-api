<?php
namespace com\linways\core\dto;

use com\linways\base\dto\BaseDTO;

class Like extends BaseDTO{

    /**
     * @var string
     */
    public $id;
    
    /**
     * @var string
     */
	public $userId;
	
    /**
     * @var string
     */
    public $postId;
}
?>