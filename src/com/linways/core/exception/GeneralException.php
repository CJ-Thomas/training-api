<?php

namespace com\linways\core\exception;

use com\linways\base\exception\CoreException;

class GeneralException extends CoreException{
    
    const EMPTY_PARAMETERS = "EMPTY_PARAMETERS";

    const INVALID_PARAMETERS = "INVALID_PARAMETERS";

    const PASSWORD_MISSMATCH = "PASSWORD_MISSMATCH";

    const USER_EXISTS = "USER_EXISTS";
    
    const INVALID_DATE_RANGE = "INVALID_DATE_RANGE";
}