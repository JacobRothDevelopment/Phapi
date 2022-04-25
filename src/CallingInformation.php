<?php

namespace Phapi;

class CallingInformation
{
    /** The `Controller` class to be used when calling the endpoint
     * (does not include 'Controller') */
    public ?string $Controller;
    /** The `Controller` method to be used when calling the endpoint */
    public ?string $Action;
    /** The arguments given by the URL 
     * - dictionary: `key` = parameter name & `value` = value
     * @var mixed[] $Args */
    public ?array $Args;

    public function __construct(
        ?string $DefaultController = null,
        ?string $DefaultAction = null
    ) {
        $this->Controller = $DefaultController;
        $this->Action = $DefaultAction;
        $this->Args = null;
    }
}
