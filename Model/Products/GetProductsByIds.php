<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Products;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class GetProductsByIds
{
    private CollectionFactory $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int[] $productIds
     * @return ProductInterface[]
     */
    public function execute(array $productIds, int $storeId): array
    {
        if (empty($productIds)) {
            return [];
        }
        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => $productIds]);

        $result = [];
        foreach ($collection->getItems() as $product) {
            $product->setStoreId($storeId);
            $result[$product->getSku()] = $product;
        }

        return $result;
    }
}
