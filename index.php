<?php

foreach (glob(__DIR__ . '/PhpApi/*.php') as $file) {
    require_once($file);
}
foreach (glob(__DIR__ . '/Controllers/*.php') as $file) {
    require_once($file);
}

use PhpApi\Routes;
use PhpApi\Route;
use PhpApi\HttpMethod;
use PhpApi\Startup;

$Routes = new Routes();
$Routes->Add(new Route(
    "Update By Id",
    "/PhpApi/api/v1/{controller}/{action}/{id}",
    HttpMethod::Put
));
$Routes->Add(new Route(
    "Get By Id",
    "/PhpApi/api/v2/{controller}/{id}",
    HttpMethod::Get
));
$Routes->Add(new Route(
    "Double Id Get",
    "/PhpApi/api/v1/{controller}/{action}/{id}/{idtwo?}",
    HttpMethod::Get
    // ^ NOTE: id is a required input, idtwo is optional
));
$Routes->Add(new Route(
    "Default",
    "/PhpApi/api/v1/{controller}/{action?}"
    // NOTE: this is how you'll call a method by http method
));
$Routes->Add(new Route(
    "Calling Methods with Special URLs",
    "/PhpApi/{id}",
    null,
    "Special",
    "DontKnowWhyYoudUseThisButHereItIs"
));

$Startup = new Startup($Routes);
$Startup->Run();
