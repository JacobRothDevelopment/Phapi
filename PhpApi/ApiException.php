<?php

namespace PhpApi;

class ApiException extends \Exception
{
    public function __construct(int $httpCode, string $message)
    {
        parent::__construct($message, $httpCode);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
