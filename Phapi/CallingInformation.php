<?php

namespace Phapi;

class CallingInformation
{
    public ?string $Controller;
    public ?string $Action;
    /** @var mixed[string] $Args */
    // key = variable name
    // value = value
    public ?array $Args;

    public function __construct(?string $DefaultController = null, ?string $DefaultAction = null)
    {
        $this->Controller = $DefaultController;
        $this->Action = $DefaultAction;
        $this->Args = null;
    }
}
