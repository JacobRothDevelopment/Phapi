<?php

namespace PhpApi;

class Route
{
    public string $Name;
    public string $Path;
    public ?string $HttpMethod;

    public function __construct(string $Name, string $Path, ?string $HttpMethod = null)
    {
        $this->Name = $Name;
        $this->Path = $Path;
        $this->HttpMethod = $HttpMethod;
    }
}

class Routes
{
    private arrayss $Routes; // array of type Route

    public function __construct()
    {
        $this->Routes = array();
    }

    public function Add(Route $Route): void
    {
        array_push($this->Routes, $Route);
    }

    public function Find(string $Path): Route
    {
        foreach ($this->Routes as $route) {
            /* @var Route $route */
            $route;
        }
    }
}
