<?php
declare(strict_types=1);

namespace Walley\Upsell\ViewModel;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walley\Upsell\Api\Data\UpsellInterface;
use Walley\Upsell\Api\GetUpsellInterface;

class UpsellDataProvider implements ArgumentInterface
{
    private GetUpsellInterface $getUpsell;
    private UrlInterface $url;
    private IsUpsellAllowedForOrder $isUpsellAllowedForOrder;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        GetUpsellInterface $getUpsell,
        OrderRepositoryInterface $orderRepository,
        IsUpsellAllowedForOrder $isUpsellAllowedForOrder,
        UrlInterface $url
    ) {
        $this->getUpsell = $getUpsell;
        $this->url = $url;
        $this->isUpsellAllowedForOrder = $isUpsellAllowedForOrder;
        $this->orderRepository = $orderRepository;
    }

    public function execute(int $orderId): UpsellInterface
    {
        return $this->getUpsell->execute($orderId);
    }

    public function getOrderById(int $orderId):OrderInterface
    {
        return $this->orderRepository->get($orderId);
    }
    public function getProductPriceConfig(UpsellInterface $upsell):array
    {
        $productPriceArray = [];
        $products = $upsell->getUpsellProducts();
        foreach ($products as $product) {
            $productPriceArray[$product->getProductId()] = [
                'id' => $product->getProductId(),
                'price' => $product->getFinalPrice(),
            ];
        }

        return $productPriceArray;
    }

    public function getAddToOrderUrl():string
    {
        return $this->url->getUrl('rest/V1/walleyupsell');
    }

    public function getStatusForOrderUrl():string
    {
        return $this->url->getUrl('rest/V1/walleyupsellstatus');
    }

    public function secondsToHourFormat(int $countDownSeconds): string
    {
        return gmdate("H:i:s", $countDownSeconds);
    }

    public function isUpsellAllowedForOrder(int $orderId):bool
    {
        $order = $this->orderRepository->get($orderId);

        return $this->isUpsellAllowedForOrder->execute($order);
    }
}
