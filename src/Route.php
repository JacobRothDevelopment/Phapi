<?php

namespace Phapi;

class Route
{
    /** A name given to a path; 
     * Makes it easy to delineate between Routes while programming them; 
     * Holds no functionality */
    public string $Name;
    /** The URL path, including parameters */
    public string $Path;
    /** Defines the allowable HTTP method usable by the Route
     * @var string[]|null $HttpMethods */
    public ?array $HttpMethods;
    /** Allows for the separation of similar controllers through namespaces */
    public string $Namespace;
    /** If a controller is not otherwise defined, this will be used instead */
    public ?string $DefaultController;
    /** If an action is not otherwise defined, this will be used instead */
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
