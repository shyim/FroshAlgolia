<?php

namespace SwAlgolia\Structs;

/**
 * Class Struct
 * @package SwAlgolia\Structs
 */
abstract class Struct
{
    /**
     * Converts the given struct to a multidimensional array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [];
        $methods = get_class_methods(get_class($this));

        foreach ($methods as $method) {

            if (0 === strpos($method,'get')) {
                $propertyName = lcfirst(substr($method, 3));
                $data[$propertyName] = $this->$method();
            }
        }

        return $data;
    }
}
