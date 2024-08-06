<?php

namespace AlirezaMoh\LaravelFileExplorer\Exceptions;

use Exception;

class NullUserException extends Exception
{
    protected $message = 'Could not validate user permission. User is null';
}
