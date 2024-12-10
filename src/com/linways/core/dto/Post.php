<?php
namespace com\linways\core\dto;

use com\linways\base\dto\BaseDTO;

class Post extends BaseDTO{

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
	public $post;

    /**
     * @var string
     */
	public $caption;
}

?>