<?php
namespace SwAlgolia\Services;

use AlgoliaSearch\Client;
use Doctrine\ORM\AbstractQuery;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ProductService;
use Shopware\Components;

/**
 * Class AlgoliaIndexService
 * @package SwAlgolia\Services
 */
class AlgoliaIndexService
{

    /**
     * @var null|Components\Logger
     */
    private $logger = null;

    /**
     * @var null|ContextService
     */
    private $context = null;

    /**
     * @var null|ProductService
     */
    private $productService = null;

    /**
     * AlgoliaIndexService constructor.
     * @param Components\Logger $logger
     */
    public function __construct(Components\Logger $logger, ContextService $context, ProductService $productService) {

        $this->logger = $logger;
        $this->context = $context;
        $this->productService = $productService;

        // Construct the context
        $repository = Shopware()->Container()->get('models')->getRepository('Shopware\Models\Shop\Shop');
        $shop = $repository->getActiveDefault();
        $shop->registerResources();
        //$this->context = Components\Routing\Context::createFromShop($shop, Shopware()->Container()->get('config'));
    }

    public function push() {

        // Grab the plugin config
        $pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');

        // Init the API client
        $client = new Client($pluginConfig['algolia-application-id'],$pluginConfig['algolia-admin-api-key']);
        $index = $client->initIndex($pluginConfig['index-name']);

        /**
         * @TODO Create article data here
         */
        // Build query
        $qb = Shopware()->Models()->createQueryBuilder()
            ->select('article')
            ->addSelect('detail')
            ->from('Shopware\Models\Article\Article', 'article')
            ->where('article.active = :active')
            ->leftJoin('article.mainDetail', 'detail')
            ->setParameter('active', true);

        $query = $qb->getQuery();
        $query->setHydrationMode(AbstractQuery::HYDRATE_OBJECT);
        $paginator = Shopware()->Container()->get('models')->createPaginator($query);
        $articles = $paginator->getIterator()->getArrayCopy();

        $data = [];
        $router = Shopware()->Container()->get('router');

        foreach($articles as $article) {

            // Get product object
            if (!$product = $this->productService->get($article->getMainDetail()->getNumber(), $this->context->getShopContext())):
                $this->logger->addAlert('Could not generate product struct for article {number} - {articleName} for export. Product not exported.', array('number' => $article->getMainDetail()->getNumber(), 'articleName' => $article->getName()));
                continue;
            endif;

            // Get the SEO URL
            $assembleParams = array(
                'module' => 'frontend',
                'sViewport' => 'detail',
                'sArticle' => $article->getId()
            );
            $link = $router->assemble($assembleParams);

            $data[] = array(
                'objectID' => $article->getMainDetail()->getNumber(),
                'name' => $article->getName(),
                'number' => $article->getMainDetail()->getNumber(),
                'manufacturer_name' => $article->getSupplier()->getName(),
                'price' => round($product->getCheapestPrice()->getCalculatedPrice(), 2),
                'link' => $link,
                'description' => strip_tags($article->getDescription()),
                'ean' => $article->getMainDetail()->getEan()
            );
            $this->logger->addInfo('Successfully exported article {number} - {articleName}', array('number' => $article->getMainDetail()->getNumber(), 'articleName' => $article->getName()));
        }

        $index->addObjects($data);

    }

}
