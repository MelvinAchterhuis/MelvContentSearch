<?php declare (strict_types=1);

namespace Melv\ContentSearch\Subscriber;

use Melv\ContentSearch\Helpers\SearchCategory;
use Melv\ContentSearch\Helpers\SearchLandingPage;
use Melv\ContentSearch\Helpers\SearchManufacturer;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SystemConfigService $systemConfigService,
        private SearchManufacturer $searchManufacturer,
        private SearchCategory $searchCategory,
        private SearchLandingPage $searchLandingPage,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SuggestPageLoadedEvent::class => 'extendSearchResults',
        ];
    }

    public function extendSearchResults(SuggestPageLoadedEvent $event)
    {
        $query = $event->getPage()->getSearchTerm();
        $salesChannelContext = $event->getSalesChannelContext();
        $salesChannelId = $salesChannelContext->getSalesChannel()->getId();

        if($this->systemConfigService->get('MelvContentSearch.config.searchManufacturers', $salesChannelId)) {
            $manufacturers = $this->searchManufacturer->getResults($query, $salesChannelContext);
            if($manufacturers->getTotal() > 0) {
                $event->getPage()->getSearchResult()->addExtension('manufacturers', $manufacturers);
            }
        }

        if($this->systemConfigService->get('MelvContentSearch.config.searchCategories', $salesChannelId)) {
            $categories = $this->searchCategory->getResults($query, $salesChannelContext);
            if ($categories->getTotal() > 0) {
                $event->getPage()->getSearchResult()->addExtension('categories', $categories);
            }
        }

        if($this->systemConfigService->get('MelvContentSearch.config.searchLandingPages', $salesChannelId)) {
            $landingPages = $this->searchLandingPage->getResults($query, $salesChannelContext);
            if ($landingPages->getTotal() > 0) {
                $event->getPage()->getSearchResult()->addExtension('landingPages', $landingPages);
            }
        }
    }
}
