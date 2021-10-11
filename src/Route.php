<?php

namespace Phapi;

class Route
{
    public string $Name;
    public string $Path;
    /** @var string[]|null $HttpMethods */
    public ?array $HttpMethods;
    public string $Namespace;
    public ?string $DefaultController;
    public ?string $DefaultAction;

    /** @param string[]|null $HttpMethods */
    public function __construct(
        string $Name,
        string $Path,
        ?array $HttpMethods = null,
        string $Namespace = "",
        ?string $DefaultController = null,
        ?string $DefaultAction = null
    ) {
        $this->Name = $Name;
        $this->Path = $Path;
        $this->HttpMethods = $HttpMethods;
        if ($this->HttpMethods === null) {
            $this->HttpMethods = HttpMethod::All;
        }
        $this->Namespace = $Namespace;
        $this->DefaultController = $DefaultController;
        $this->DefaultAction = $DefaultAction;
    }
}
