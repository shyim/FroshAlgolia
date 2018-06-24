<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Struct;

/**
 * Class Struct.
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
        $data = get_object_vars($this);

        return $data;
    }
}
