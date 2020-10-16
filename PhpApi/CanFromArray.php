<?php

namespace PhpApi;

abstract class CanFromArray
{
    public function __set($name, $value)
    {
        throw new \Exception("invalid assignment of property [$name] for [" . get_class($this) . "]");
    }

    // input is array where key = property name; value = value
    public function FromArray(array $values): void
    {
        $propertyClassNames = array();
        $className = get_class($this);
        $properties = get_class_vars(get_class($this));
        foreach ($properties as $propertyName => $value) {
            $reflectionProp = new \ReflectionProperty($className, $propertyName);
            $propClass = $reflectionProp->getType()->getName(); // this is an error in intelephense.  but it works
            $propertyClassNames[$propertyName] = $propClass;
        }

        foreach ($propertyClassNames as $propertyName => $propertyClassName) {
            switch ($propertyClassName) {
                    // for native types
                case 'int':
                case 'string':
                case 'float':
                case 'bool':
                    $this->$propertyName = $values[$propertyName];
                    break;

                    // for special/object types
                    // TODO: Change: handle sql dates as strings 
                case 'DateTime':
                    $this->$propertyName = new $propertyClassName($values[$propertyName]);
                    break;

                    // for json objects being stored as strings
                    // limitation: it only known to handle json input
                case 'object':
                    if (is_array($values[$propertyName])) {
                        $this->$propertyName = (object)$values[$propertyName];
                    } else {
                        $this->$propertyName = (object)json_decode($values[$propertyName]);
                    }
                    break;

                    // if property type is not listed above
                default:
                    // do nothing
                    break;
            }
        }
    }
}
