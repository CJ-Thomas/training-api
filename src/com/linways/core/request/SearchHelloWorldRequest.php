<?php
namespace com\linways\core\request;

use com\linways\base\request\BaseRequest;
use com\linways\core\constant\StatusConstants;

class SearchHelloWorldRequest extends BaseRequest
{
    /**
     * @var String
     */
    public $id;

    /**
     * @var String
     */
    public $name;

    /**
     * @var String
     * 'ALL' for all data
     * 'ACTIVE' for active data 
     * 'TRASHED' for deleted data
     */
    public $trashed = StatusConstants::ACTIVE;
}