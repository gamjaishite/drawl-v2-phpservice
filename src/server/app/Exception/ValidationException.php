<?php

class ValidationException extends \Exception
{
    public function __construct(
        $message,
        int|null $code = 0,
        Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->code = 400;
    }
}