<?php

foreach (glob(__DIR__ . '/PhpApi/*.php') as $file) {
    require_once($file);
}
foreach (glob(__DIR__ . '/Controllers/*.php') as $file) {
    require_once($file);
}

$Routes = new PhpApi\Routes();
$Routes->Add(new PhpApi\Route(
    "Update By Id",
    "/PhpApi/api/v1/{controller}/{action}/{id}",
    PhpApi\HttpMethod::Put
));
$Routes->Add(new PhpApi\Route(
    "Get By Id",
    "/PhpApi/api/v2/{controller}/{id}",
    PhpApi\HttpMethod::Get
));
$Routes->Add(new PhpApi\Route(
    "Double Id Get",
    "/PhpApi/api/v1/{controller}/{action}/{id}/{idtwo?}"
    // ^ NOTE: id is a required input, idtwo is optional
));
$Routes->Add(new PhpApi\Route(
    "Default",
    "/PhpApi/api/v1/{controller}/{action?}"
    // NOTE: this is how you'll call a method by http method
));
$Routes->Add(new PhpApi\Route(
    "Calling Methods with Special URLs",
    "/PhpApi/{id}",
    null,
    "Special",
    "DontKnowWhyYoudUseThisButHereItIs"
));

$Startup = new PhpApi\Startup($Routes);
$Startup->Run();
