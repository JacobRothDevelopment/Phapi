<?php

namespace Phapi;

class Route
{
    public string $Name;
    public string $Path;
    public ?string $HttpMethod;
    public ?string $DefaultController;
    public ?string $DefaultAction;

    public function __construct(
        string $Name,
        string $Path,
        ?string $HttpMethod = null,
        ?string $DefaultController = null,
        ?string $DefaultAction = null
    ) {
        $this->Name = $Name;
        $this->Path = $Path;
        $this->HttpMethod = $HttpMethod;
        $this->DefaultController = $DefaultController;
        $this->DefaultAction = $DefaultAction;
    }
}
