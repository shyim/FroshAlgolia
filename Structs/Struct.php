<?php declare(strict_types=1);

namespace FroshAlgolia\Structs;

/**
 * Class Struct.
 */
abstract class Struct implements StructInterface
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
            if (strpos($method, 'get') === 0) {
                $propertyName = lcfirst(substr($method, 3));
                $data[$propertyName] = $this->$method();
            }
        }

        return $data;
    }
}
