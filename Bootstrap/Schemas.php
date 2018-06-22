<?php declare(strict_types=1);

namespace FroshAlgolia\Bootstrap;

use Doctrine\ORM\Tools\SchemaTool;
use FroshAlgolia\Models\Backlog;
use FroshAlgolia\Models\Config;

/**
 * This class is responsible for creating schemas based on plugin models during plugin installation.
 *
 * Class Schemas
 */
class Schemas
{
    /**
     * Creates the schemas for the additional models.
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public static function createSchemas()
    {
        $tool = new SchemaTool(Shopware()->Container()->get('models'));
        $classes = [
            Shopware()->Container()->get('models')->getClassMetadata(Backlog::class),
            Shopware()->Container()->get('models')->getClassMetadata(Config::class),
        ];
        $tool->createSchema($classes);
    }

    /**
     * Removes the schemas for the additional models.
     */
    public static function removeSchemas()
    {
        $tool = new SchemaTool(Shopware()->Container()->get('models'));
        $classes = [
            Shopware()->Container()->get('models')->getClassMetadata(Backlog::class),
            Shopware()->Container()->get('models')->getClassMetadata(Config::class),
        ];
        $tool->dropSchema($classes);
    }
}
