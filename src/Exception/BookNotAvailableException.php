<?php

namespace App\Exception;


class BookNotAvailableException extends \RuntimeException
{
    public function __construct(string $message = "Ce livre n'est pas disponible pour l'emprunt", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
