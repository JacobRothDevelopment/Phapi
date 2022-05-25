<?php

namespace Phapi;

class Startup
{
    private Routes $Routes;
    public $ReadCallback;
    public $ReturnCallback;

    /** @param ?Routes $Routes Lists available Routes for API
     *  @param ?callable $ReadCallback Override reading in data from 
     * HTTP request
     *  @param ?callable $ReturnCallback Override returning data for 
     * HTTP response
     */
    public function __construct(
        ?Routes $Routes = null,
        ?callable $ReadCallback = null,
        ?callable $ReturnCallback = null
    ) {
        if ($Routes === null) {
            $this->Routes = new Routes();
        } else {
            $this->Routes = $Routes;
        }

        $this->ReadCallback = $ReadCallback;
        $this->ReturnCallback = $ReturnCallback;
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
        $actionToUse =
            $callingInfo->Action === null
            ?  $_SERVER['REQUEST_METHOD']
            : $callingInfo->Action;

        try {
            $reflectionMethod = new \ReflectionMethod(
                $controllerClass,
                $actionToUse
            );
            $reflectionParams = $reflectionMethod->getParameters();

            $parameters = [];
            // loop through endpoint args
            foreach ($reflectionParams as $reflectionParam) {
                // if argument is in URL, get value from Calling Info
                if (isset($callingInfo->Args[$reflectionParam->getName()])) {
                    $value = $callingInfo->Args[$reflectionParam->getName()];
                    array_push($parameters, $value);
                } else {
                    // If argument is not in URL, assume it is in the body data
                    /** @var \ReflectionNamedType $reflectionType */
                    $reflectionType = $reflectionParam->getType();
                    $typeName = $reflectionType->getName();
                    $nativeTypes = ["int", "string", "bool", "mixed"];
                    if (in_array($typeName, $nativeTypes)) {
                        $paramName = $reflectionParam->getName();
                        $value = $inputData->$paramName;
                        if (!$reflectionParam->allowsNull() && $value === null) {
                            throw new ApiException(
                                HttpCode::BadRequest,
                                "Invalid Input Data"
                            );
                        }
                        array_push($parameters, $value);
                    } elseif ($typeName === "array") {
                        if (
                            !$reflectionParam->allowsNull()
                            && $inputData === null
                        ) {
                            throw new ApiException(
                                HttpCode::BadRequest,
                                "Invalid Input Data"
                            );
                        }
                        array_push($parameters, $inputData);
                    } else {
                        if (
                            !$reflectionParam->allowsNull()
                            && $inputData === null
                        ) {
                            throw new ApiException(
                                HttpCode::BadRequest,
                                "Invalid Input Data"
                            );
                        }

                        if (
                            $reflectionParam->allowsNull()
                            && $inputData === null
                        ) {
                            array_push($parameters, null);
                        } else {
                            $object = \PhpCast\Cast::cast(
                                $typeName,
                                $inputData,
                                true
                            );
                            array_push($parameters, $object);
                        }
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

    /** Returns what the endpoint returns; Is overridden if ReturnCallback is not null 
     * @param mixed $o The value returned from the endpoint
     * @return void
     */
    private function PrintOut($o): void
    {
        if ($this->ReturnCallback === null) {
            $this->DefaultPrintOut($o);
        } else {
            ($this->ReturnCallback)($o);
        }
    }

    /** Reads in request data. Is overridden if ReadCallback is not null 
     * @return mixed Standard representation of input. Not parsed or typed 
     * until used as method argument
     */
    private function ReadIn()
    {
        if ($this->ReadCallback === null) {
            return $this->DefaultReadIn();
        } else {
            return ($this->ReadCallback)();
        }
    }

    /** By default, treat the input as JSON
     * @return string string stringified JSON
     */
    private function DefaultReadIn()
    {
        return json_decode(file_get_contents("php://input"), false);
    }

    /** By default, treat the output as JSON
     * @param mixed $o whatever you want returned from endpoint
     */
    private function DefaultPrintOut($o)
    {
        header("Content-Type: application/json");
        print(json_encode($o));
    }
}
