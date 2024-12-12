<?php
namespace com\linways\core\request;

use com\linways\base\request\BaseRequest;

class SearchPostRequest extends BaseRequest{

    /**
     * @var String
     */
    public $id;

    /**
     * @var String
     */
    public $fromDate;

    /**
     * @var String
     */
    public $toDate;

}