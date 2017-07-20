<?php

use Shopware\Bundle\SearchBundle\SearchTermPreProcessorInterface;

/**
 * Class Shopware_Controllers_Frontend_Search.
 */
class Shopware_Controllers_Frontend_Search extends Enlight_Controller_Action
{
    /**
     * @return string
     */
    public function indexAction()
    {
        $this->forward('defaultSearch');

        return null;
    }

    /**
     * Default search.
     */
    public function defaultSearchAction()
    {
        // Get the sSearch term
        $term = $this->getSearchTerm();

        // If the "q" param for instantsearch is not set, redirect the user to the url with q param
        if (!$this->Request()->getParam('q') && $term && $term != '') {
            $this->redirect('search?q='.$term);
        }

        $this->View()->addTemplateDir(__DIR__ . '/../../Resources/views/');
        $this->View()->loadTemplate('frontend/test.tpl');

        $this->assignTemplateVars();
    }

    /**
     * @return string
     */
    private function getSearchTerm()
    {
        $term = $this->Request()->getParam('sSearch', '');

        /**
         * @var SearchTermPreProcessorInterface
         */
        $processor = $this->get('shopware_search.search_term_pre_processor');

        return $processor->process($term);
    }

    private function assignTemplateVars()
    {
        $pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');
        $syncHelperService = $this->container->get('sw_algolia.sync_helper_service');
        $this->View()->algoliaConfig = $this->container->get('sw_algolia.config_reader')->read(Shopware()->Shop());

        /**
         * Build the JS index for sort order based on replica configuration. First element in this
         * index is the main Algolia index.
         */
        $sortOrderArray = [
            [
                'name' => $syncHelperService->buildIndexName(Shopware()->Shop()), // The index which is used for this sort order
                'label' => Shopware()->Snippets()->getNamespace('bundle/translation')->get('sort_order_default'), // The name which should be shown to the customer
            ],
        ];
        $replicaIndices = explode('|', $pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach ($replicaIndices as $replicaIndex) {
            $replicaIndexSettings = explode(',', $replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(', $replicaIndexSettings[0]);
            $replicaIndexName = $syncHelperService->buildIndexName(Shopware()->Shop()).'_'.rtrim($nameElements[1], ')').'_'.$nameElements[0];

            $sortOrderArray[] = [
                'name' => $replicaIndexName, // The index which is used for this sort order
                'label' => Shopware()->Snippets()->getNamespace('bundle/translation')->get('sort_order_'.rtrim($nameElements[1], ')').'_'.$nameElements[0]), // The name which should be shown to the customer
            ];
        }

        $sortOrderIndex = htmlspecialchars(json_encode($sortOrderArray, JSON_HEX_APOS));
        $this->View()->assign('sortOrderIndex', $sortOrderIndex);
    }
}
