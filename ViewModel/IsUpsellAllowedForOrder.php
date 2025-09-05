<?php
declare(strict_types=1);

namespace Walley\Upsell\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Walley\Upsell\Model\Config\ConfigProvider;
use Walley\Upsell\Model\GetWalleyPaymentMethod;

class IsUpsellAllowedForOrder implements ArgumentInterface
{
    private ConfigProvider $configProvider;
    private GetWalleyPaymentMethod $getWalleyPaymentMethod;

    public function __construct(
        ConfigProvider $configProvider,
        GetWalleyPaymentMethod $getWalleyPaymentMethod
    ) {
        $this->configProvider = $configProvider;
        $this->getWalleyPaymentMethod = $getWalleyPaymentMethod;
    }

    public function execute(OrderInterface $order):bool
    {
        $storeId = $order->getStoreId();
        if (!$this->configProvider->getIsActive($storeId)) {
            return false;
        }

        $allowedPaymentMethods = $this->configProvider->getAllowedPaymentMethods();
        $orderPaymentMethod = $this->getOrderPaymentMethod($order);

        return in_array($orderPaymentMethod, $allowedPaymentMethods);
    }

    public function getOrderPaymentMethod(OrderInterface $order):?string
    {
        $payment = $order->getPayment();
        if (!$payment) {
            return null;
        }
        $additionalInformation = $payment->getAdditionalInformation();
        if (empty($additionalInformation)
            || !is_array($additionalInformation)
            || !isset($additionalInformation['payment_name'])
        ) {
            return $this->getWalleyPaymentMethod->execute($order);
        }
        return $additionalInformation['payment_name'];
    }
}
