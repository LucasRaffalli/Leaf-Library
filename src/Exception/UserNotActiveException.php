<?php

namespace App\Exception;

class UserNotActiveException extends \RuntimeException
{
    public function __construct(string $message = "Votre compte est désactivé", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
