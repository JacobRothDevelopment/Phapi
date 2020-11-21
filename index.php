<?php
foreach (glob("PhpApi" . '/*.php') as $file) { require_once($file); }

namespace PhpApi;

$Routes = new Routes();
$Routes->Add(new Route(
    "Update By Id",
    "/api/v1/{controller}/{action}/{id}",
    "PUT"
));
$Routes->Add(new Route(
    "Double Id Get",
    "/api/v1/{controller}/{action}/{id}/{idtwo?}" 
    // ^ NOTE: id is a required input, idtwo is optional
));
$Routes->Add(new Route(
    "Default",
    "/api/v1/{controller}/{action}"
));

$Options = new Options("application/json",true);

$Startup = new Startup($Routes);
$Startup->Run();
