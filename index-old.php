<?php
// From PhpApi

require_once(__DIR__ . "/DbClasses/DbClasses.php");
require_once(__DIR__ . "/PhpApi/ApiException.php");
require_once(__DIR__ . "/env.php");

// for debugging only
function DebugPrint($o): void
{
    print("<pre>" . print_r($o, true) . "</pre>");
}

// print return data onto screen as JSON object
function JsonPrint(object $o): void
{
    print(json_encode($o));
}

// parse http request to get controller, action, and data
function GetApiRequest(): array
{
    $inputData = null;
    $uri = parse_url($_SERVER["REQUEST_URI"]);

    $method = $_SERVER["REQUEST_METHOD"];
    if ($method === "GET") {
        $output = array();
        if (isset($uri['query'])) {
            parse_str($uri['query'], $output);
        }
        $inputData = $output;
    } else {
        $inputData = json_decode(file_get_contents("php://input"), TRUE);
    }

    $levels = explode("/", $uri['path']);
    $apiDirName = basename(__DIR__);
    $uriOffset = array_search($apiDirName, $levels, true);

    $controller = $levels[1 + $uriOffset];
    $action = $levels[2 + $uriOffset];

    $apiVars = array(
        'controller' => $controller,
        'action' => $action,
        'data' => $inputData
    );
    return $apiVars;
}

// this one is a doozy so buckle up
// Calls the given Method for the given Class by name using the given Argument
function Call(?string $className, ?string $methodName, ?array $args)
{
    $noEndpointEsception = new PhpApi\ApiException(404, "Specified endpoint does not exist");
    if ($className == null || $methodName == null) {
        throw $noEndpointEsception;
    }

    $ControllerClass = $className . "Controller";
    $ControllerFilePath = __DIR__ . "/Controllers/" . $ControllerClass . ".php";
    if (!file_exists($ControllerFilePath)) {
        throw $noEndpointEsception;
    }
    require_once($ControllerFilePath);

    $ControllerObject = new $ControllerClass; // instantiate Controller class

    // get paramter object for the 
    $reflectionClass = new ReflectionClass($ControllerClass);
    try {
        $params = $reflectionClass->getMethod($methodName)->getParameters();
    } catch (ReflectionException $e) { // if method does not exist in controller
        throw $noEndpointEsception;
    }

    if (count($params) > 0) { // if the controller method takes a param, create that object
        if ($args === null) {
            throw new PhpApi\ApiException(400, "Invalid arguments");
        }
        $param = $params[0]; // get 1st parameter
        $argClass = $param->getClass()->getName(); // get parameter class name
        $argObject = new $argClass; // create new instance of argued class
        $argObject->FromArray($args);

        return $ControllerObject->{$methodName}($argObject);
    } else { // if the method does not take any paramters, call it
        return $ControllerObject->{$methodName}();
    }
}

/// This is where it all beings

// get api request variables
$apiReq = GetApiRequest();
$UrlController = $apiReq["controller"];
$UrlAction = $apiReq["action"];

// define variable so that we don't get a NOTICE in error log
$return = "If you're seeing this, something went wrong";

try {
    // call the Method in the Controller class
    $return = Call($UrlController, $UrlAction, $apiReq["data"]);
} catch (PhpApi\ApiException $e) {
    // if an api exception is ever thrown
    //      return error message and set the proper http error code
    http_response_code($e->getCode());
    $return = (object)[
        "message" => $e->getMessage(),
    ];
}

header("Content-Type: application/json");
JsonPrint((object)($return));
