<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class GetOrderByPublicToken
{
    private CollectionFactory $orderCollectionFactory;

    public function __construct(CollectionFactory $orderCollectionFactory)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    public function execute(string $publicToken): ?OrderInterface
    {
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('collectorbank_public_id', $publicToken);
        $order = $orderCollection->getFirstItem();

        if ($order && $order->getId()) { // Check if the order exists
            return $order;
        }

        return null;
    }
}
