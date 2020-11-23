<?php

namespace PhpApi;

use ReflectionMethod;

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
            $this->PrintOut($e->getMessage());
        }
    }

    private function Call()
    {
        // TODO? configure for xml data as input?
        $inputData = json_decode(file_get_contents("php://input"), TRUE);

        $DoesNotExistMessage = "Endpoint Does Not Exist";
        $callingInfo = $this->Routes->Find();

        Err($callingInfo);

        $controllerClass = $callingInfo->Controller . "Controller";

        // allows for http method as action
        // only if action is not a class method
        $actionToUse = $callingInfo->Action;
        if (!method_exists($controllerClass, $callingInfo->Action)) {
            $actionToUse = $_SERVER['REQUEST_METHOD'];
        }
        try {
            $reflectionMethod = new \ReflectionMethod($controllerClass, $callingInfo->Action);
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
                    if (!in_array($typeName, $nativeTypes)) {
                        $value = $inputData[$reflectionParam->getName()];
                        array_push($parameters, $value);
                    } else {
                        $object = $this->Cast($typeName, $inputData);
                        array_push($parameters, $object);
                    }
                }
            }

            $controllerObj = new $controllerClass;
            $output = $controllerObj->$actionToUse(...$parameters);

            $this->PrintOut($output);
        } catch (\ReflectionException $e) {
            throw new ApiException(404, $DoesNotExistMessage . " :: " . $e->getMessage());
        }
    }

    /** @param mixed $o */
    private function PrintOut($o): void
    {
        header("Content-Type: application/json");
        print(json_encode($o));
    }

    /** @return mixed */
    private function Cast(string $class, array $values) 
    {
        $obj = new $class;
        foreach ($values as $key => $value) {
            if (property_exists($class, $key)) {
                $obj->$key = $value;
            }
        }
        return $obj;
    }
}

class Options // not currently being used
{
    public string $ReturnType;
    public bool $AllowRest;

    public function __construct(string $ReturnType = "application/json", bool $AllowRest = true)
    {
        $this->ReturnType = $ReturnType;
        $this->AllowRest = $AllowRest;
    }
}
