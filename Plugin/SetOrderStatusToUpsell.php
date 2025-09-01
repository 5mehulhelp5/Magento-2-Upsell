<?php

declare(strict_types=1);

namespace Walley\Upsell\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Walley\Upsell\Model\Config\ConfigProvider;
use Walley\Upsell\ViewModel\IsUpsellAllowedForOrder;
use Webbhuset\CollectorCheckout\Checkout\Order\Manager;

class SetOrderStatusToUpsell
{
    private ConfigProvider $configProvider;
    private IsUpsellAllowedForOrder $isUpsellAllowedForOrder;

    public function __construct(
        ConfigProvider $configProvider,
        IsUpsellAllowedForOrder $isUpsellAllowedForOrder
    ) {
        $this->configProvider = $configProvider;
        $this->isUpsellAllowedForOrder = $isUpsellAllowedForOrder;
    }

    public function aroundUpdateOrderStatus(
        Manager $subject,
        callable $proceed,
        $order,
        $status,
        $state
    ) {
        /** @var OrderInterface $order */
        if (!$this->isUpsellAllowedForOrder->execute($order)){
            return $proceed($order, $status, $state);
        }
        $upsellOrderStatus = $this->configProvider->getUpsellOrderStatus((int)$order->getStoreId());
        return $proceed($order, $upsellOrderStatus, $state);
    }
}
