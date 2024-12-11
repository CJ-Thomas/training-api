<?php

namespace com\linways\core\exception;

use com\linways\base\exception\CoreException;

class UserException extends CoreException{

    const PASSWORD_MISSMATCH = "PASSWORD_MISSMATCH";

    const USER_EXISTS = "USER_EXISTS";

}