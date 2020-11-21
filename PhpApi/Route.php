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
