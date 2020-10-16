<?php
require_once(__DIR__ . "/PhpApi/Startup.php");

use PhpApi\Routes;
use PhpApi\Route;
use PhpApi\Startup;
use PhpApi\Options;

$Routes = new Routes();
$Routes->Add(new Route(
    "Update By Id",
    "/api/v1/{controller}/{action}/{id}",
    "PUT"
));
$Routes->Add(new Route(
    "Default",
    "/api/v1/{controller}/{action}"
));

$Options = new Options("application/json", true);

$Startup = new Startup($Options, $Routes);
$Startup->Call();
