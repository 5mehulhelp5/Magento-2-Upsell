<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\StoreManager;
use Walley\Upsell\Model\Config\ConfigProvider;

class ChangeOrderStatusTimeout
{
    private StoreManager $storeManager;
    private OrderCollectionFactory $orderCollectionFactory;
    private Order $orderResource;
    private ConfigProvider $configProvider;

    public function __construct(
        StoreManager           $storeManager,
        ConfigProvider         $configProvider,
        Order                  $orderResource,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderResource = $orderResource;
        $this->configProvider = $configProvider;
    }

    public function execute(): void
    {
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $storeId = (int)$store->getId();
            if (!$this->configProvider->getIsActive($storeId)) {
                continue;
            }
            $orders = $this->getOrders($this->configProvider->getUpsellOrderStatus($storeId), $storeId);
            $ordersTimedOut = $this->getTimedOutOrders($orders->getItems());
            $this->changeOrderStatus($ordersTimedOut);
        }
    }

    public function getTimedOutOrders(array $orders): array
    {
        $ordersTimedOut = [];
        foreach ($orders as $order) {
            if ($this->hasTimeoutPassed($order)) {
                $ordersTimedOut[] = $order;
            }
        }
        return $ordersTimedOut;
    }

    /**
     * @param OrderInterface[] $orders
     * @return void
     */
    public function changeOrderStatus(array $orders):void
    {
        foreach ($orders as $order) {
            $storeId = $order->getStoreId();
            $status = $this->configProvider->getAcknowledgeOrderStatus((int) $storeId);
            $order->setStatus($status);
            try {
                $this->orderResource->save($order);
            } catch (AlreadyExistsException $e) {
            }
        }
    }

    public function hasTimeoutPassed(OrderInterface $order): bool
    {
        $storeId = (int) $order->getStoreId();
        $timeout = $this->configProvider->getCountDownSeconds($storeId);
        $lastUpdated = strtotime((string)$order->getUpdatedAt());
        if ($lastUpdated === false) {
            return false;
        }
        return (time() - $lastUpdated) > ($timeout + 300);
    }

    private function getOrders(string $orderStatus, int $storeId)
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter('status', $orderStatus)
            ->addFieldToFilter('store_id', $storeId);
        return $collection;
    }
}
