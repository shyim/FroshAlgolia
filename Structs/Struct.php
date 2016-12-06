<?php

namespace SwAlgolia\Structs;

abstract class Struct
{

    /**
     * Converts the given struct to a multidimensional array
     * @return array
     */
    public function toArray()
    {
        $data = array();
        $methods = get_class_methods(get_class($this));

        foreach ($methods as $method):

            if ('get' == substr($method, 0, 3)):

                //$propertyName = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', substr($method,3))), '_');
                $propertyName = lcfirst(substr($method, 3));
        $data[$propertyName] = $this->$method();

        endif;

        endforeach;

        return $data;
    }
}
