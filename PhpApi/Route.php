<?php

namespace PhpApi;

class Route
{
    public string $Name;
    public string $Path;
    public ?HttpMethod $HttpMethod;
    public ?string $DefaultController;
    public ?string $DefaultAction;

    public function __construct(
        string $Name,
        string $Path,
        ?HttpMethod $HttpMethod = null,
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
