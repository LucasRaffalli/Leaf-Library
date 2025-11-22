<?php

namespace App\Exception;

class BorrowLimitExceededException extends \RuntimeException
{
    public function __construct(int $limit = 5, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        if (empty($message)) {
            $message = "Vous avez atteint le nombre maximum d'emprunts simultanés ($limit)";
        }
        parent::__construct($message, $code, $previous);
    }
}
