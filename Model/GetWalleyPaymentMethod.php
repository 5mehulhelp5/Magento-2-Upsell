<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Magento\Sales\Api\Data\OrderInterface;

class GetWalleyPaymentMethod
{
    private \Webbhuset\CollectorCheckout\Data\OrderHandler $orderHandler;
    private \Webbhuset\CollectorCheckout\AdapterFactory $collectorAdapter;
    private \Webbhuset\CollectorCheckout\Config\OrderConfigFactory $configFactory;

    public function __construct(
        \Webbhuset\CollectorCheckout\Data\OrderHandler $orderHandler,
        \Webbhuset\CollectorCheckout\AdapterFactory $collectorAdapter,
        \Webbhuset\CollectorCheckout\Config\OrderConfigFactory $configFactory
    ) {
        $this->orderHandler = $orderHandler;
        $this->collectorAdapter = $collectorAdapter;
        $this->configFactory = $configFactory;
    }


    public function execute(OrderInterface $order):string
    {
        $collectorBankPrivateId = $this->orderHandler->getPrivateId($order);
        $checkoutAdapter = $this->collectorAdapter->create();

        $config = $this->configFactory->create(['order' => $order]);
        $checkoutData = $checkoutAdapter->acquireCheckoutInformation($config, $collectorBankPrivateId);

        return $checkoutData->getPurchase()->getPaymentName();
    }
}
