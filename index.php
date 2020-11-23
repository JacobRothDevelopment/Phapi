<?php
foreach (glob(__DIR__ . '/PhpApi/*.php') as $file) { require_once($file); }
foreach (glob(__DIR__ . '/Controllers/*.php') as $file) { require_once($file); }

function ERR($o) { // TODO: REMOVE WHEN DONE TESTING
    error_log(print_r($o, true));
}


$Routes = new PhpApi\Routes();
$Routes->Add(new PhpApi\Route(
    "Update By Id",
    "/PhpApi/api/v1/{controller}/{action}/{id}",
    "PUT"
));
$Routes->Add(new PhpApi\Route(
    "Double Id Get",
    "/PhpApi/api/v1/{controller}/{action}/{id}/{idtwo?}"
    // ^ NOTE: id is a required input, idtwo is optional
));
$Routes->Add(new PhpApi\Route(
    "Default",
    "/PhpApi/api/v1/{controller}/{action?}"
));

$Options = new PhpApi\Options("application/json",true);

$Startup = new PhpApi\Startup($Routes);
$Startup->Run();
