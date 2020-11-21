<?php

namespace PhpApi;

class CallingInformation
{
    public string $Controller;
    public ?string $Action;
    /** @var mixed[string] $Args */
    // key = variable name
    // value = value
    public ?array $Args;
}
