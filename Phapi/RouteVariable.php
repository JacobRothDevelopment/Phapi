<?php

namespace Phapi;

class RouteVariable
{
    public string $VariableName;
    public bool $Nullable;

    public static function TryParse(string $element, ?RouteVariable &$out): bool
    {
        $length = strlen($element);
        if ((substr($element, 0, 1) === "{") && (substr($element, -1, 1) === "}")) {
            $variable = substr($element, 1, -1);
            if ($variable !== false && strlen($variable) > 0) {
                $canNull = substr($variable, 0, 1) === "?"; // if first char is ?, allow null inputs
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
