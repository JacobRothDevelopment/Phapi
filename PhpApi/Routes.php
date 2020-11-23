<?php

namespace PhpApi;

use Error;

class Routes
{
    /** @var Route[] $Routes */
    private array $Routes;

    public function __construct()
    {
        $this->Routes = [];
        $this->CallingInfo = null;
    }

    public function Add(Route $Route): void
    {
        array_push($this->Routes, $Route);
    }

    public function Find(): CallingInformation
    {
        $inputMethod = $_SERVER['REQUEST_METHOD'];
        $inputPath = parse_url($_SERVER["REQUEST_URI"])['path'];
        $inputElements = explode("/", $inputPath);
        if ($inputElements === false) {
            throw new ApiException(400, "Invalid Url");
        }

        $index = 0;
        $callingInfo = null;
        while ($index < count($this->Routes)) {
            $route = $this->Routes[$index];
            // error_log(print_r([
            //     "index" => $index,
            //     "route" => $route
            // ], true));
            $methodMatch = $route->HttpMethod !== null ? $route->HttpMethod === $inputMethod : true;
            $routeFits = $this->TryFit($route, $inputElements, $callingInfo);
            if ($methodMatch && $routeFits) {
                // end search
                break;
            }
            $index++;
        }

        if ($callingInfo === null) {
            throw new ApiException(400, "Invalid Request");
        }

        return $callingInfo;
    }

    private function TryFit(Route $route, array $inputElements, ?CallingInformation &$callingInfoOut): bool
    {
        $callingInfoOut = null;
        $callingInfo = new CallingInformation();
        $genericPath = $route->Path;
        $genericElements = explode("/",$genericPath);
        if (count($genericElements) < count($inputElements)) {
            // if there are more elements in the url request than the 
            return false;
        }
        // Err([
        //     "generic els" => $genericElements,
        //     "input els" => $inputElements,
        //     "generic path" => $route->Path
        // ]);

        foreach ($genericElements as $key => $genericElement) {
            $inputElement = isset($inputElements[$key]) ? $inputElements[$key] : null;

            // Err(["generic el" => $genericElement]);
            if (RouteVariable::TryParse($genericElement, $variable)) {
                // Err("--------------------TRY PARSE SUCCESSFUL--------------------");
                if (!$variable->Nullable && empty($inputElement)) {
                    // if element cannot be null. yet no value is given
                    return false;
                }
                // Err($variable);
                switch ($variable->VariableName) {
                    case "controller":
                        $callingInfo->Controller = $inputElement;
                        // Err(["controller" => $inputElement]);
                    break;
                    case "action":
                        $callingInfo->Action = $inputElement;
                        // Err(["action" => $inputElement]);
                    break;
                    default:
                        $callingInfo->Args[$variable->VariableName] = $inputElement;
                        // Err(["strict" => $inputElement]);
                    break;
                }
            } else {
                // if generic element is not a variable, assume it is a part of the url path
                // e.g. generic path = /api/v1/{controller}/{action}
                // "api" and "v1" must match exactly
                if ($genericElement !== $inputElement) {
                    return false;
                }
            }

            // if end of both generic path and input path

            if (count($genericElements) === ($key + 1)) {
                $callingInfoOut = $callingInfo;
                return true;
            }
        }

        return false;
    }
}
