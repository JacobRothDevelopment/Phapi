<?php

namespace PhpApi;

class CallingInformation
{
    public ?string $Controller;
    public ?string $Action;
    /** @var mixed[string] $Args */
    // key = variable name
    // value = value
    public ?array $Args;

    public function __construct()
    {
        $this->Controller = null;
        $this->Action = null;
        $this->Args = null;
    }
}
