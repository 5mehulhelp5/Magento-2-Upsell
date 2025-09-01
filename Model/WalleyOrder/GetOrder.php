<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\WalleyOrder;

use Magento\Sales\Api\Data\OrderInterface;
use Webbhuset\CollectorCheckout\Adapter;
use Webbhuset\CollectorCheckout\Config\ConfigFactory;

class GetOrder
{
    private ConfigFactory $configFactory;
    private Adapter $adapter;

    public function __construct(
        ConfigFactory $configFactory,
        Adapter $adapter
    ) {
        $this->configFactory = $configFactory;
        $this->adapter = $adapter;
    }

    /**
     * Gets the order information from walley
     */
    public function execute(OrderInterface $order):array
    {
        $config = $this->configFactory->create(['order' => $order]);
        /** @var \Webbhuset\CollectorCheckoutSDK\Adapter\CurlWithAccessKey $adapter */
        $adapter = $this->adapter->getAdapter($config);
        $additionalInformation = $order->getPayment()->getAdditionalInformation();
        if (!isset($additionalInformation['order_id'])) {
            return [];
        }
        $walleyOrderId = $additionalInformation['order_id'];

        return $adapter->getOrder($walleyOrderId);
    }
}
