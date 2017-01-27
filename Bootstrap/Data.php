<?php

namespace SwAlgolia\Bootstrap;
use Shopware\Models\Config\Element;


/**
 *
 * This class is responsible for data creation and manipulation on plugin installation.
 */
class Data
{

    /**
     * Initializor for data manipulation
     */
    public static function manipulate()
    {
        self::addConfig();

    }

    /**
     * Add additional config elements
     */
    private function addConfig()
    {

        $em = Shopware()->Container()->get('models');

        // Add "waiting_for_prescription" state
        if(!$em->getRepository('\Shopware\Models\Config\Element')->findOneBy(array('name' => 'lastSwAlgoliaBacklogId'))):

            $opts = [
                'label' => 'Last processed Algolia Backlog ID',
                'value' => 0,
                'required' => false,
                'scope' => 0,
                'position' => 0
            ];

            $config = new Element('number', 'lastSwAlgoliaBacklogId',$opts);
            $config->setForm($em->getRepository('\Shopware\Models\Config\Form')->findOneBy(array(
                'name' => 'Base',
                'label' => 'Shopeinstellungen'
            )));
            $em->persist($config);
            $em->flush();

        endif;

    }

}