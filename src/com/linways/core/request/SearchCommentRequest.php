<?php
namespace com\linways\core\request;

use com\linways\base\request\BaseRequest;

class SearchCommentRequest{
    /**
     * @var String
     */
    public $id;
    
    /**
     * @var String
     */
    public $postId;

    /**
     * @var String
     */
    public $userId;

    /**
     * @var String
     */
    public $parentCommentId;

    /**
     * @var String
     */
    public $searchContent;

}