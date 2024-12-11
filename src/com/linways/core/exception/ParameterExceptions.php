<?php

namespace com\linways\core\exception;

use com\linways\base\exception\CoreException;

class ParameterExceptions extends CoreException{
    
    const EMPTY_PARAMETERS = "EMPTY_PARAMETERS";

    const INVALID_PARAMETERS = "INVALID_PARAMETERS";
    
}