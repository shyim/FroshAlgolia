<?php declare(strict_types=1);

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

    public function preDispatch()
    {
        parent::preDispatch();
        $this->View()->loadTemplate('frontend/search/fuzzy.tpl');
    }

    /**
     * Default search.
     */
    public function defaultSearchAction()
    {
        // Get the sSearch term
        $term = $this->getSearchTerm();

        if (!$this->Request()->getParam('q') && $term && $term != '') {
            $this->redirect('search?q=' . $term);
        }

        $this->View()->addTemplateDir(__DIR__ . '/../../Resources/views/');

        $this->assignDefaultShopwareVariables($term);
    }

    /**
     * @throws Exception
     *
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

    private function assignDefaultShopwareVariables($term)
    {
        $request = $this->Request()->getParams();
        $request['sSearchOrginal'] = $term;

        $pageCounts = $this->get('config')->get('fuzzySearchSelectPerPage');

        $this->View()->assign([
            'term' => $term,
            'sPage' => $this->Request()->getParam('sPage', 1),
            'sSort' => $this->Request()->getParam('sSort', 7),
            'sTemplate' => $this->Request()->getParam('sTemplate'),
            'sPerPage' => array_values(explode('|', $pageCounts)),
            'sRequests' => $request,
            'pageSizes' => array_values(explode('|', $pageCounts)),
            'ajaxCountUrlParams' => [],
            'sSearchResults' => [
                'sArticles' => [],
                'sArticlesCount' => null,
            ],
            'productBoxLayout' => $this->get('config')->get('searchProductBoxLayout'),
        ]);
    }
}
