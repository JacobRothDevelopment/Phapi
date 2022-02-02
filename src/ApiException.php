<?php

namespace Phapi;

class ApiException extends \Exception
{
    protected bool $noMessage;
    protected string $messageClass;
    protected $errorMessage;

    public function __construct(int $httpCode, $message = null)
    {
        $this->noMessage = $message === null;
        $this->messageClass = gettype($message);
        $this->errorMessage = $message;
        parent::__construct(json_encode($message), $httpCode);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: $this->message\n";
    }

    public function hasMessage(): bool
    {
        return !$this->noMessage;
    }

    public function getMessageClass(): string
    {
        return $this->messageClass;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
