<?php declare (strict_types=1);

namespace Melv\ContentSearch\Helpers;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

class SearchManufacturer
{
    public function __construct(
        private EntityRepository $manufacturerRepository
    ) {}

    public function getResults(string $query, $salesChannelContext): EntitySearchResult
    {
        //TODO: Not all manufacturers are necessarily tied to the current sales channel, however by default it is not possible to tie a manufacturer to a sales channel
        $criteria = new Criteria();
        $criteria->addAssociation('media');
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new ContainsFilter('name', $query),
            new ContainsFilter('description', $query),
            new ContainsFilter('customFields', $query),
        ]));

        return $this->manufacturerRepository->search($criteria, $salesChannelContext->getContext());
    }
}
