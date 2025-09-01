<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\WalleyOrder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Walley\Upsell\Model\Products\GetProductsByIds;
use Walley\Upsell\Model\WalleyOrder\Converter\ProductToWalleyItem;
use Webbhuset\CollectorCheckout\Adapter;
use Webbhuset\CollectorCheckout\Config\ConfigFactory;

/**
 * @property GetOrder $getOrder
 */
class AddProductsToOrder
{
    private CollectionFactory $productCollectionFactory;
    private ProductToWalleyItem $productToWalleyItem;
    private Adapter $adapter;
    private GetOrder $getOrder;
    private ConfigFactory $configFactory;
    private GetProductsByIds $getProductsByIds;

    public function __construct(
        GetOrder $getOrder,
        Adapter $adapter,
        ConfigFactory $configFactory,
        GetProductsByIds $getProductsByIds,
        CollectionFactory $productCollectionFactory,
        ProductToWalleyItem $productToWalleyItem
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productToWalleyItem = $productToWalleyItem;
        $this->getOrder = $getOrder;
        $this->adapter = $adapter;
        $this->configFactory = $configFactory;
        $this->getProductsByIds = $getProductsByIds;
    }

    /**
     * @param ProductInterface[] $products
     * @param OrderInterface $order
     * @return array response
     * @throws LocalizedException
     */
    public function execute(array $products, OrderInterface $order)
    {
        $walleyOrderId = $this->getWalleyOrderId($order);
        if (!$walleyOrderId) {
            return 500;
        }
        if (empty($products)) {
            return 500;
        }
        $walleyItemsToAdd = [];
        foreach ($products as $product) {
            $walleyItemsToAdd[] = $this->productToWalleyItem->execute($product);
        }
        $walleyOrder = $this->getOrder->execute($order);
        $walleyOrderItems = $this->getItemsFromWalleyOrder($walleyOrder);

        $items = $this->mergeItems($walleyOrderItems, $walleyItemsToAdd);
        $amount = $this->calculateItemsCost($items);
        $payload = [
            'amount' => $amount,
            'items' => $items,
        ];
        $config = $this->configFactory->create(['order' => $order]);
        /** @var \Webbhuset\CollectorCheckoutSDK\Adapter\CurlWithAccessKey $adapter */
        $adapter = $this->adapter->getAdapter($config);

        return $adapter->reauthorize($walleyOrderId, $payload);
    }

    private function calculateItemsCost(array $items): float
    {
        $total = 0.0;
        foreach($items as $item) {
            $total += $item['unitPrice'] * $item['quantity'];
        }
        return $total;
    }

    private function getItemsFromWalleyOrder(array $order): array
    {
        $result = [];
        if (!isset($order['data']['items'])) {
            return [];
        }

        foreach ($order['data']['items'] as $item) {
            $result[] = [
                'id' => $item['articleNumber'],
                'description' => $item['description'],
                'unitPrice' => $item['price'],
                'quantity' => $item['quantity'],
                'vat' => $item['vatRate'],
            ];
        }
        return $result;
    }

    public function mergeItems(array $items1, array $items2): array
    {
        $map1 = [];
        foreach ($items1 as $item) {
            $map1[$item['id']] = $item;
        }
        foreach ($items2 as $item) {
            if (isset($map1[$item['id']])) {
                if ($map1[$item['id']]['unitPrice'] == $item['unitPrice']) {
                    $map1[$item['id']]['quantity'] += $item['quantity'];
                } else {
                    $item['id'] = $item['id'] . '-2';
                    $map1[$item['id']] = $item;
                }
            } else {
                $map1[$item['id']] = $item;
            }
        }
        return array_values($map1);
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
