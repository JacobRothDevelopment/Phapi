<?php

namespace Phapi;

class ApiException extends \Exception
{
    protected bool $noMessage;

    public function __construct(int $httpCode, ?string $message = null)
    {
        $this->noMessage = $message === null;
        parent::__construct($message, $httpCode);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function hasMessage(): ?string
    {
        return !$this->noMessage;
    }
}