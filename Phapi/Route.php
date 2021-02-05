<?php

namespace Phapi;

class Route
{
    public string $Name;
    public string $Path;
    /** @var string[]|null $HttpMethods */
    public ?array $HttpMethods;
    public ?string $DefaultController;
    public ?string $DefaultAction;

    /** @param string[]|null $HttpMethods */
    public function __construct(
        string $Name,
        string $Path,
        ?array $HttpMethods = null,
        ?string $DefaultController = null,
        ?string $DefaultAction = null
    ) {
        $this->Name = $Name;
        $this->Path = $Path;
        $this->HttpMethods = $HttpMethods;
        $this->DefaultController = $DefaultController;
        $this->DefaultAction = $DefaultAction;
    }
}
