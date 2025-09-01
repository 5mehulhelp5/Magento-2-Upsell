<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\WalleyOrder;

use Magento\Sales\Api\Data\OrderInterface;
use Webbhuset\CollectorCheckout\Adapter;
use Webbhuset\CollectorCheckout\Config\ConfigFactory;

class GetReauthorizeStatus
{
    private Adapter $adapter;
    private ConfigFactory $configFactory;

    public function __construct(
        Adapter $adapter,
        ConfigFactory $configFactory
    ) {
        $this->adapter = $adapter;
        $this->configFactory = $configFactory;
    }

    public function execute($reathorizeId, OrderInterface $order)
    {
        $walleyOrderId = $this->getWalleyOrderId($order);
        if (!$walleyOrderId) {
            return 500;
        }
        $config = $this->configFactory->create(['order' => $order]);
        /** @var \Webbhuset\CollectorCheckoutSDK\Adapter\CurlWithAccessKey $adapter */
        $adapter = $this->adapter->getAdapter($config);

        return $adapter->reauthorizeStatus($walleyOrderId, $reathorizeId);
    }

    private function getWalleyOrderId(OrderInterface $order):?string
    {
        $additionalInformation = $order->getPayment()->getAdditionalInformation();
        if (!isset($additionalInformation['order_id'])) {
            return null;
        }
        return $additionalInformation['order_id'];
    }
}
