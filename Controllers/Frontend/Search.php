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

        $this->View()->loadTemplate('frontend/search/fuzzy.tpl');
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
}
