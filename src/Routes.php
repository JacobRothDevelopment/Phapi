<?php

namespace Phapi;

class Routes
{
    /** @var Route[] $Routes */
    private array $Routes;

    /** @param Route[] $routes */
    public function __construct(array $routes = [])
    {
        $this->Routes = $routes;
        $this->CallingInfo = null;
    }

    /** Add a Route to the list */
    public function Add(Route $Route): void
    {
        array_push($this->Routes, $Route);
    }

    /** Match Request's URI and HTTP Method to the existing Routes  */
    public function Find(string $inputMethod, string $inputPath): CallingInformation
    {
        $inputElements = explode("/", $inputPath);

        if ($inputElements === false) {
            throw new ApiException(HttpCode::BadRequest, "Invalid Url");
        }

        // if input is ever empty, assume the parameter value is null
        // start at index 1 because index 0 is expected to be ""
        for ($i = 1; $i < count($inputElements); $i++) {
            if ($inputElements[$i] === "") {
                $inputElements[$i] = null;
            }
        }

        $index = 0;
        $callingInfo = null;
        while ($index < count($this->Routes)) {
            $route = $this->Routes[$index];
            $methodMatch = $route->HttpMethods !== null
                ? in_array($inputMethod, $route->HttpMethods)
                : true;
            $routeFits = $this->TryFit($route, $inputElements, $callingInfo);
            if ($methodMatch && $routeFits) {
                // end search
                break;
            }
            $index++;
        }

        if ($callingInfo === null) {
            throw new ApiException(HttpCode::NotFound);
        }

        return $callingInfo;
    }

    /** @param string[] $inputElements */
    /** Tests a route to see if it fits the request information */
    private function TryFit(
        Route $route,
        array $inputElements,
        ?CallingInformation &$callingInfoOut
    ): bool {
        $callingInfoOut = null;
        $callingInfo = new CallingInformation(
            $route->Namespace . "\\" . $route->DefaultController,
            $route->DefaultAction
        );
        $genericPath = $route->Path;
        $genericElements = explode("/", $genericPath);
        if (count($genericElements) < count($inputElements)) {
            // if there are more elements in the url request than the route url 
            return false;
        }

        foreach ($genericElements as $key => $genericElement) {
            $inputElement = isset($inputElements[$key])
                ? $inputElements[$key]
                : null;
            if (RouteVariable::TryParse($genericElement, $variable)) {
                if (!$variable->Nullable && ($inputElement === null)) {
                    // if element cannot be null. yet no value is given
                    return false;
                }
                switch ($variable->VariableName) {
                    case "controller":
                        $callingInfo->Controller =
                            $route->Namespace . "\\" . $inputElement;
                        break;
                    case "action":
                        $callingInfo->Action = $inputElement;
                        break;
                    default:
                        $callingInfo->Args[$variable->VariableName]
                            = $inputElement;
                        break;
                }
            } else {
                // if generic element is not a variable, 
                //      assume it is a part of the url path
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
