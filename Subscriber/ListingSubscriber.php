<?php /** @noinspection TransitiveDependenciesUsageInspection */

namespace FroshAlgolia\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use FroshAlgolia\Components\Service\CategoryService;

/**
 * Class ListingSubscriber
 * @package FroshAlgolia\Subscriber
 */
class ListingSubscriber implements SubscriberInterface
{
    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * ListingSubscriber constructor.
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend_Listing' => 'onPostDispatchListing'
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchListing(Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Listing $subject */
        $subject = $args->getSubject();
        $categoryContent = $subject->View()->getAssign('sCategoryContent');

        $context = Shopware()->Container()->get('shopware_storefront.context_service')->getContext();

        $path = array_filter(explode('|', $categoryContent['path']));
        $path[] = $categoryContent['id'];
        array_shift($path);

        $subject->View()->assign('algoliaCategory', $this->categoryService->buildPath($path, $context));
    }
}