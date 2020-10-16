<?php
require_once(__DIR__ . "/Route.php");
require_once(__DIR__ . "/.php");

namespace PhpApi;

class Startup
{
    private Options $Options;
    private Routes $Routes;

    public function __construct(?Options $Options = new Options(), Routes $Routes = array())
    {
        $this->Options = $Options;
        $this->Routes = $Routes;
    }

    public function Call()
    {
    }
}

class Options
{
    public string $ReturnType;
    public bool $AllowRest;

    public function __construct(string $ReturnType = "application/json", bool $AllowRest = true)
    {
        $this->ReturnType = $ReturnType;
        $this->AllowRest = $AllowRest;
    }
}
