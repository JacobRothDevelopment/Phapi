<?php

namespace Phapi;

class RouteVariable
{
    /** Name of the URL parameter */
    public string $VariableName;
    /** Signifies if the URL parameter can be null */
    public bool $Nullable;

    /** Returns true if the element at the URL level is a variable; 
     * If so, `out` is set to the RouteVariable */
    public static function TryParse(string $element, ?RouteVariable &$out): bool
    {
        $length = strlen($element);
        if ((substr($element, 0, 1) === "{") && (substr($element, -1, 1) === "}")) {
            $variable = substr($element, 1, -1);
            if ($variable !== false && strlen($variable) > 0) {
                // if first char is ?, allow null inputs
                $canNull = substr($variable, 0, 1) === "?";
                $variableName = substr($variable, $canNull ? 1 : 0,  $length);
                $routeVariable = new RouteVariable();
                $routeVariable->VariableName = $variableName;
                $routeVariable->Nullable = $canNull;
                $out = $routeVariable;
                return true;
            }
        }

        return false;
    }
}
