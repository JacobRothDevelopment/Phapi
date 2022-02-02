<?php

require_once __DIR__ . "/loader.php";

use Phapi\Routes;
use Phapi\Route;
use Phapi\HttpMethod;
use Phapi\Startup;

$Routes = new Routes();
$Routes->Add(new Route(
    "Update By Id",
    "/Example/api/v1/{controller}/{action}/{?id}",
    [HttpMethod::Put, HttpMethod::Get],
    // NOTE: ^ this specifies that this route is only for PUT actions
    "Controllers"
));
$Routes->Add(new Route(
    "Get By Id",
    "/Example/api/v2/{controller}/{id}",
    [HttpMethod::Get],
    "Controllers"
));
$Routes->Add(new Route(
    "Double Id Get",
    "/Example/api/v1/{controller}/{action}/{id}/{?string}",
    // ^ NOTE: id is a required input, string is optional
    [HttpMethod::Get],
    "Controllers"
));
$Routes->Add(new Route(
    "Default",
    "/Example/api/v1/{controller}/{?action}",
    // NOTE: this is how you'll call a method by http method
    null,
    "Controllers"
));
$Routes->Add(new Route(
    "Calling Methods with Special URLs",
    "/Example/{id}",
    null,
    "V2",
    "Special",
    "DontKnowWhyYoudUseThisButHereItIs"
));

$Startup = new Startup($Routes);
$Startup->Run();