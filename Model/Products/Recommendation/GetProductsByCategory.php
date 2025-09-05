<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Products\Recommendation;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Walley\Upsell\Model\Config\ConfigProvider;

class GetProductsByCategory
{
    protected $categoryRepository;
    protected $productCollectionFactory;
    protected $catalogConfig;
    private ConfigProvider $configProvider;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $productCollectionFactory,
        ConfigProvider $configProvider,
        Config $catalogConfig
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->configProvider = $configProvider;
    }

    /**
     * @return ProductInterface[]
     */
    public function execute(): array
    {
        $categoryId = $this->configProvider->getCategoryId();
        if (!$categoryId) {
            return [];
        }

        $category = $this->categoryRepository->get($categoryId);
        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addCategoryFilter($category)
            ->addAttributeToFilter('type_id', Type::TYPE_SIMPLE)
            ->setPageSize($this->configProvider->getLimitOfProducts())
            ->getItems();

        return $products;
    }
}
