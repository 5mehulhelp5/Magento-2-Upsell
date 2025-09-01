<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Products;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Walley\Upsell\Model\Config\ConfigProvider;
use Walley\Upsell\Model\Config\Source\RecommendationTypes;
use Walley\Upsell\Model\Products\Recommendation\GetCrossellByOrder;
use Walley\Upsell\Model\Products\Recommendation\GetCustomProducts;
use Walley\Upsell\Model\Products\Recommendation\GetCustomProductsByOrder;
use Walley\Upsell\Model\Products\Recommendation\GetProductsByCategory;
use Walley\Upsell\Model\Products\Recommendation\GetRelatedByOrder;
use Walley\Upsell\Model\Products\Recommendation\GetUpsellByOrder;

class GetUpsellProductsByOrder
{
    private ConfigProvider $configProvider;
    private GetCrossellByOrder $getCrossellByOrder;
    private GetCustomProductsByOrder $getCustomProductsByOrder;
    private GetProductsByCategory $getProductsByCategory;
    public function __construct(
        ConfigProvider $configProvider,
        GetCrossellByOrder $getCrossellByOrder,
        GetCustomProductsByOrder $getCustomProductsByOrder,
        GetProductsByCategory $getProductsByCategory
    ) {
        $this->configProvider = $configProvider;
        $this->getCrossellByOrder = $getCrossellByOrder;
        $this->getCustomProductsByOrder = $getCustomProductsByOrder;
        $this->getProductsByCategory = $getProductsByCategory;
    }

    /**
     * @param OrderInterface $order
     * @return ProductInterface[]
     */
    public function execute(OrderInterface $order): array
    {
        $recommendationType = $this->configProvider->getRecommendationType();

        $products = [];
        switch ($recommendationType) {
            case RecommendationTypes::RECOMMENDATION_TYPE_NOT_SET:
                $products = [];
                break;
            case RecommendationTypes::RECOMMENDATION_TYPE_CROSS_SELL:
                $products = $this->getCrossellByOrder->execute($order);
                break;
            case RecommendationTypes::RECOMMENDATION_TYPE_CUSTOM:
                $products = $this->getCustomProductsByOrder->execute($order);
                break;
        }
        $categoryId = $this->configProvider->getCategoryId();
        if (empty($products) && $categoryId > 0) {
            $products = $this->getProductsByCategory->execute();
        }

        return $products;
    }
}
