<?php

namespace SwAlgolia\Bootstrap;

use Doctrine\ORM\Tools\SchemaTool;
use SwAlgolia\Models\Backlog;

/**
 * Class Schemas
 * @package SwAlgolia\Bootstrap
 *
 * This class is responsible for creating schemas based on plugin models during plugin installation.
 */
class Schemas
{

    /**
     * Creates the schemas for the additional models
     */
    public static function createSchemas()
    {
        $tool = new SchemaTool(Shopware()->Container()->get('models'));
        $classes = [
            Shopware()->Container()->get('models')->getClassMetadata(Backlog::class)
        ];
        $tool->createSchema($classes);
    }

    /**
     * Removes the schemas for the additional models
     */
    public static function removeSchemas()
    {
        $tool = new SchemaTool(Shopware()->Container()->get('models'));
        $classes = [
            Shopware()->Container()->get('models')->getClassMetadata(Backlog::class)
        ];
        $tool->dropSchema($classes);
    }

}