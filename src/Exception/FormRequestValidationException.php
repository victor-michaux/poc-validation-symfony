<?php

namespace App\Exception;

use Throwable;

class FormRequestValidationException extends \Exception
{
    private array $errors;

    public function __construct(array $errors, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}