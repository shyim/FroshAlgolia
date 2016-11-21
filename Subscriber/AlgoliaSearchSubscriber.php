<?php

namespace SwAlgolia\Subscriber;

use Doctrine\ORM\EntityManager;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Event_EventArgs;
use Shopware\Models\Property\Option;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AlgoliaSearchSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $viewDir;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AlgoliaSearchSubscriber constructor.
     *
     * @param string             $viewDir
     * @param ContainerInterface $container
     */
    public function __construct(
        $viewDir,
        ContainerInterface $container
    ){
        $this->viewDir = $viewDir;
        $this->container = $container;
    }

    /**
     * @return array
     *
     * Return an array of all subscribed events in this class
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'initAlgoliaSearch'
        ];
    }

    /**
     * Create view variables for Algolia search
     * @param Enlight_Event_EventArgs $args
     */
    public function initAlgoliaSearch(Enlight_Event_EventArgs $args)
    {
        $pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');
        $syncHelperService = $this->container->get('sw_algolia.sync_helper_service');

        // Get the shop instance
        $shopId = $this->container->get('router')->getContext()->getShopId();
        $shop = Shopware()->Container()->get('models')->getRepository('Shopware\Models\Shop\Shop')->getActiveById($shopId);

        /** @var Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();

        /** @var $em EntityManager */
        $em = $this->container->get('models');
        $filterOptions = $em->getRepository(Option::class)->findAll();

        /**
         * Build the JS index for sort order based on replica configuration. First element in this
         * index is the main Algolia index.
         */
        $sortOrderArray=[
            array(
                'name' =>  $syncHelperService->buildIndexName($shop), // The index which is used for this sort order
                'label' => Shopware()->Snippets()->getNamespace('bundle/translation')->get('sort_order_default') // The name which should be shown to the customer
            )
        ];
        $replicaIndices = explode('|',$pluginConfig['index-replicas-custom-ranking-attributes']);
        foreach($replicaIndices as $replicaIndex):

            $replicaIndexSettings = explode(',',$replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(',$replicaIndexSettings[0]);
            $replicaIndexName = $syncHelperService->buildIndexName($shop) . '_'. rtrim($nameElements[1],')') . '_' . $nameElements[0];

            $sortOrderArray[] = array(
                'name' =>  $replicaIndexName, // The index which is used for this sort order
                'label' => Shopware()->Snippets()->getNamespace('bundle/translation')->get('sort_order_'.rtrim($nameElements[1],')') . '_' . $nameElements[0]) // The name which should be shown to the customer
            );

        endforeach;
        $sortOrderIndex = htmlspecialchars(json_encode($sortOrderArray,JSON_HEX_APOS));

        // Assign data to view
        $view->addTemplateDir($this->viewDir);
        $view->assign('algoliaApplicationId', $pluginConfig['algolia-application-id']);
        $view->assign('algoliaSearchOnlyApiKey', $pluginConfig['algolia-search-only-api-key']);
        $view->assign('indexName', $syncHelperService->buildIndexName($shop));
        $view->assign('showAlgoliaLogo',$pluginConfig['show-algolia-logo']);
        $view->assign('facetFilterWidgetConfig',json_decode($pluginConfig['facet-filter-widget-config']));
        $view->assign('filterOptions',$filterOptions);
        $view->assign('sortOrderIndex',$sortOrderIndex);
        
        
    }
}
