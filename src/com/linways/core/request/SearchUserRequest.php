<?php
namespace com\linways\core\request;

use com\linways\base\request\BaseRequest;

class SearchUserRequest extends BaseRequest{
    /**
     * @var String
     */
    public $id;

    /**
     * @var String
     */
    public $name;
}