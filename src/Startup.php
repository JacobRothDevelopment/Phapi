<?php

namespace Phapi;

class Startup
{
    private Routes $Routes;

    public function __construct(Routes $Routes = null)
    {
        if ($Routes === null) {
            $this->Routes = new Routes();
        } else {
            $this->Routes = $Routes;
        }
    }

    /** Kicks off the process of getting and calling the endpoint */
    public function Run(): void
    {
        try {
            $this->Call();
        } catch (ApiException $e) {
            http_response_code($e->getCode());
            if ($e->hasMessage()) {
                $this->PrintOut($e->getErrorMessage());
            }
        }
    }

    /** Responsible for 
     * 1) Matching request uri to controller and method
     * 2) Gather input data, in request data and url
     * 3) Executing given method using given data
     * 4) If no match is found, throw 404
     * 5) If data is ill-formatted or unexpected, throw 400
     */
    private function Call()
    {
        $inputData = $this->ReadIn();
        $inputMethod = $_SERVER['REQUEST_METHOD'];
        $inputPath = parse_url($_SERVER["REQUEST_URI"])['path'];

        $callingInfo = $this->Routes->Find($inputMethod, $inputPath);
        $controllerClass = $callingInfo->Controller . "Controller";

        // allows for http method as action
        // only if action is not specified in url
        $actionToUse = $callingInfo->Action === null ?  $_SERVER['REQUEST_METHOD'] : $callingInfo->Action;

        try {
            $reflectionMethod = new \ReflectionMethod($controllerClass, $actionToUse);
            $reflectionParams = $reflectionMethod->getParameters();

            $parameters = [];
            // loop through endpoint args
            foreach ($reflectionParams as $reflectionParam) {
                if (isset($callingInfo->Args[$reflectionParam->getName()])) {
                    $value = $callingInfo->Args[$reflectionParam->getName()];
                    array_push($parameters, $value);
                } else {
                    // put a try catch around this for referencing missing parameters
                    /** @var \ReflectionNamedType $reflectionType */
                    $reflectionType = $reflectionParam->getType();
                    $typeName = $reflectionType->getName();
                    $nativeTypes = ["int", "string", "bool", "mixed"];
                    if (in_array($typeName, $nativeTypes)) {
                        $paramName = $reflectionParam->getName();
                        $value = $inputData->$paramName;
                        if (!$reflectionParam->allowsNull() && $value === null) {
                            throw new ApiException(HttpCode::BadRequest, "Invalid Input Data");
                        }
                        array_push($parameters, $value);
                    } elseif ($typeName === "array") {
                        if (!$reflectionParam->allowsNull() && $inputData === null) {
                            throw new ApiException(HttpCode::BadRequest, "Invalid Input Data");
                        }
                        array_push($parameters, $inputData);
                    } else {
                        if (!$reflectionParam->allowsNull() && $inputData === null) {
                            throw new ApiException(HttpCode::BadRequest, "Invalid Input Data");
                        }
                        $object = $this->Cast($typeName, $inputData);
                        array_push($parameters, $object);
                    }
                }
            }

            $controllerObj = new $controllerClass;
            $output = $controllerObj->$actionToUse(...$parameters);

            $this->PrintOut($output);
        } catch (\ReflectionException $e) {
            error_log($e);
            throw new ApiException(HttpCode::NotFound);
        }
    }

    // TODO UPGRADE: allow for alternate (even custom) methods of output
    /** prints what the endpoint returns but formatted as json */
    private function PrintOut($o): void
    {
        header("Content-Type: application/json");
        print(json_encode($o));
    }

    // TODO UPGRADE: allow for multiple (even custom) methods of inputs
    /** reads int request data */
    private function ReadIn()
    {
        $inputData = json_decode(file_get_contents("php://input"), false);
        return $inputData;
    }

    /** @return mixed */
    /** Casts an input object to a class given by the endpoint's parameter;
     * Allows for "object" type as well
     */
    private function Cast(string $class, object $values)
    {
        if (strtolower($class) === "object") {
            return (object)$values;
        }

        $obj = new $class;
        foreach ($values as $key => $value) {
            if (property_exists($class, $key)) {
                try {
                    $obj->$key = $value;
                } catch (\TypeError $e) {
                    throw new ApiException(HttpCode::UnprocessableEntity, "Incorrect data type for key: " . $key);
                }
            }
        }

        return $obj;
    }
}
