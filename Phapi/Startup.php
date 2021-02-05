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
                $this->PrintOut($e->getMessage());
            }
        }
    }

    private function Call()
    {
        // TODO? configure for xml data as input?
        $inputData = json_decode(file_get_contents("php://input"), false);

        $callingInfo = $this->Routes->Find();

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
                    /** @var \ReflectionNamedType $reflectionType */
                    $reflectionType = $reflectionParam->getType();
                    $typeName = $reflectionType->getName();
                    $nativeTypes = ["int", "string", "bool"];
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
            throw new ApiException(HttpCode::NotFound);
        }
    }

    /** @param mixed $o */
    private function PrintOut($o): void
    {
        header("Content-Type: application/json");
        print(json_encode($o));
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
