<?php

declare (strict_types=1);
namespace EasyCI20220611\PhpParser\ErrorHandler;

use EasyCI20220611\PhpParser\Error;
use EasyCI20220611\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error)
    {
        throw $error;
    }
}
