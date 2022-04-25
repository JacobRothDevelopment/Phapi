<?php

namespace Phapi;

class ApiException extends \Exception
{
    /** Is `true` if `message` is not null */
    protected bool $noMessage;
    /** The string name of the `message` class */
    protected string $messageClass;
    /**  */
    protected $errorMessage;

    /** This Exception will be caught by Phapi and will result in an 
     * appropriate output */
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

    /** Returns true if `message` is not null */
    public function hasMessage(): bool
    {
        return !$this->noMessage;
    }

    /** Returns the string name of the `message` class */
    public function getMessageClass(): string
    {
        return $this->messageClass;
    }

    /** Returns the `message` */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
