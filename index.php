<?php
foreach (glob("PhpApi" . '/*.php') as $file) { require_once($file); }

$Routes = new PhpApi\Routes();
$Routes->Add(new PhpApi\Route(
    "Update By Id",
    "/api/v1/{controller}/{action}/{id}",
    "PUT"
));
$Routes->Add(new PhpApi\Route(
    "Double Id Get",
    "/api/v1/{controller}/{action}/{id}/{idtwo?}" 
    // ^ NOTE: id is a required input, idtwo is optional
));
$Routes->Add(new PhpApi\Route(
    "Default",
    "/api/v1/{controller}/{action}"
));

$Options = new PhpApi\Options("application/json",true);

$Startup = new PhpApi\Startup($Routes);
$Startup->Run();
