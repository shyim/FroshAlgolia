<?php declare(strict_types=1);


namespace FroshAlgolia\AlgoliaBundle\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use FroshAlgolia\AlgoliaBundle\Service\CategoryServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;

/**
 * Class ListingSubscriber
 */
class ListingSubscriber implements SubscriberInterface
{
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * ListingSubscriber constructor.
     *
     * @param CategoryServiceInterface $categoryService
     * @param ContextServiceInterface $contextService
     */
    public function __construct(CategoryServiceInterface $categoryService, ContextServiceInterface $contextService)
    {
        $this->categoryService = $categoryService;
        $this->contextService = $contextService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend_Listing' => 'onPostDispatchListing',
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

        $context = $this->contextService->getContext();

        $path = array_filter(explode('|', $categoryContent['path']));
        $path[] = $categoryContent['id'];
        array_shift($path);

        $subject->View()->assign('algoliaCategory', $this->categoryService->buildPath($path, $context));
    }
}
