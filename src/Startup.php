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
            foreach ($reflectionParams as $reflectionParam) {
                if (isset($callingInfo->Args[$reflectionParam->getName()])) {
                    $value = $callingInfo->Args[$reflectionParam->getName()];
                    array_push($parameters, $value);
                } else {
                    // put a try catch around this for referencing missing paramters
                    /** @var \ReflectionNamedType $reflectionType */
                    $reflectionType = $reflectionParam->getType();
                    $typeName = $reflectionType->getName();
                    $nativeTypes = ["int", "string", "bool", "mixed"];
                    if (in_array($typeName, $nativeTypes)) {
                        $paramName = $reflectionParam->getName();
                        $value = $inputData->$paramName;
                        array_push($parameters, $value);
                    } else {
                        if ($inputData === null) {
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

    /** @param mixed $o */
    private function PrintOut($o): void
    {
        // TODO UPGADE: allow for alternate (even custom) methods of output
        header("Content-Type: application/json");
        print(json_encode($o));
    }

    private function ReadIn()
    {
        // TODO UPGRADE: allow for multiple (even custom) methods of inputs
        $inputData = json_decode(file_get_contents("php://input"), false);
        return $inputData;
    }

    /** @return mixed */
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
