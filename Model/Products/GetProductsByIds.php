<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Products;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class GetProductsByIds
{
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductInterface[] $productIds
     * @return array
     */
    public function execute(array $productIds):array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', $productIds, 'in')
            ->create();

        $products = $this->productRepository->getList($searchCriteria)->getItems();
        $result = [];
        foreach ($products as $product) {
            $result[$product->getSku()] = $product;
        }

        return $result;
    }
}
