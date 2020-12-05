<?php

namespace PhpApi;

class HttpMethod
{
    public string $Value;

    private function __construct(string $Method)
    {
        $this->Value = $Method;
    }

    public function Compare(string $InputMethod): bool
    {
        if (strtolower($this->Value) === strtolower($InputMethod)) {
            return true;
        } else {
            return false;
        }
    }

    const Get = new HttpMethod("GET");
    const Post = new HttpMethod("POST");
    const Put = new HttpMethod("PUT");
    const Patch = new HttpMethod("PATCH");
    const Delete = new HttpMethod("DELETE");
}
