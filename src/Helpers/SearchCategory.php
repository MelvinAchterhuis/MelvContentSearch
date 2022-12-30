<?php declare (strict_types=1);

namespace Melv\ContentSearch\Helpers;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

class SearchCategory
{
    public function __construct(
        private EntityRepository $categoryRepository
    ) {}

    public function getResults(string $query, $salesChannelContext): EntitySearchResult
    {
        //TODO: Only fetch categories from category tree connected to the current sales channel
        $criteria = new Criteria();
        $criteria->addAssociation('media');
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new ContainsFilter('name', $query),
            new ContainsFilter('metaTitle', $query),
            new ContainsFilter('metaDescription', $query),
            new ContainsFilter('keywords', $query),
            new ContainsFilter('customFields', $query),
        ]));

        return $this->categoryRepository->search($criteria, $salesChannelContext->getContext());
    }
}
