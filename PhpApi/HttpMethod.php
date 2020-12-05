<?php

namespace PhpApi;

class HttpMethod
{
    public string $Value;

    protected function __construct(string $Method)
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

    public static $Get = new HttpMethod("GET");
    public static $Post = new HttpMethod("POST");
    public static $Put = new HttpMethod("PUT");
    public static $Patch = new HttpMethod("PATCH");
    public static $Delete = new HttpMethod("DELETE");
}
