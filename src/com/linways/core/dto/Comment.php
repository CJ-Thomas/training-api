<?php
namespace com\linways\core\dto;

use com\linways\base\dto\BaseDTO;

class Comment extends BaseDTO {

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

    /**
     * @var string
     */
    public $parentCommentId;

    /**
     * @var string
     */
	public $content;

}
?>