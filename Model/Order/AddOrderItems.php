<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Order;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\ItemFactory;

class AddOrderItems
{
    private OrderRepositoryInterface $orderRepository;
    private CartItemInterfaceFactory $cartItemFactory;
    private CartRepositoryInterface $cartRepository;
    private ItemFactory $orderItemFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CartItemInterfaceFactory $cartItemInterfaceFactory,
        CartRepositoryInterface $cartRepository,
        ItemFactory $orderItemFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartItemFactory = $cartItemInterfaceFactory;
        $this->cartRepository = $cartRepository;
        $this->orderItemFactory = $orderItemFactory;
    }

    /**
     * @param OrderInterface $order
     * @param ProductInterface[] $products
     * @return bool
     */
    public function execute(OrderInterface $order, array $products)
    {
        $cart = $this->cartRepository->get($order->getQuoteId());
        $cart = $this->addProductsToQuote($cart, $products);
        $itemBySku = $this->getCartItemsByProducts($cart->getAllVisibleItems(), $products);
        foreach ($products as $product) {
            if (!isset($itemBySku[$product->getSku()])) {
                return false;
            }
            /** @var \Magento\Sales\Model\Order\Item $orderItem */
            $orderItem = $this->orderItemFactory->create();
            /** @var CartItemInterface $quoteItem */
            $quoteItem = $itemBySku[$product->getSku()];
            $orderItem
                ->setQuoteItemId($quoteItem->getItemId())
                ->setQtyOrdered($quoteItem->getQty())
                ->setPrice($quoteItem->getPrice())
                ->setBasePrice($quoteItem->getBasePrice())
                ->setOriginalPrice($quoteItem->getOriginalPrice())
                ->setBaseOriginalPrice($quoteItem->getBaseOriginalPrice())
                ->setPriceInclTax($quoteItem->getPriceInclTax())
                ->setBasePriceInclTax($quoteItem->getBasePriceInclTax())
                ->setRowTotal($quoteItem->getRowTotal())
                ->setBaseRowTotal($quoteItem->getBaseRowTotal())
                ->setRowTotalInclTax($quoteItem->getRowTotalInclTax())
                ->setBaseRowTotalInclTax($quoteItem->getBaseRowTotalInclTax())
                ->setWeight($quoteItem->getWeight())
                ->setProductType($quoteItem->getProductType())
                ->setProductId($quoteItem->getProduct()->getId())
                ->setSku($quoteItem->getSku())
                ->setTaxPercent($quoteItem->getTaxPercent())
                ->setTaxAmount($quoteItem->getTaxAmount())
                ->setBaseTaxAmount($quoteItem->getBaseTaxAmount())
                ->setName($quoteItem->getName())
                ->setIsVirtual($quoteItem->getIsVirtual());
            $order->addItem($orderItem);
        }
        $priceIncrease = $this->getPriceIncrease($products);
        $order->setBaseGrandTotal($order->getBaseGrandTotal() + $priceIncrease)
            ->setGrandTotal($order->getGrandTotal() + $priceIncrease)
            ->setBaseSubtotal($order->getBaseSubtotal() + $priceIncrease)
            ->setSubtotal($order->getSubtotal() + $priceIncrease)
            ->setBaseSubtotalInclTax($order->getBaseSubtotalInclTax() + $priceIncrease)
            ->setSubtotalInclTax($order->getSubtotalInclTax() + $priceIncrease)
            ->setTotalItemCount($order->getTotalItemCount() + count($products))
            ->setTotalQtyOrdered($order->getTotalQtyOrdered() + count($products));
        $this->orderRepository->save($order);

        return true;
    }

    /**
     * @param ProductInterface[] $products
     * @return float
     */
    private function getPriceIncrease(array $products):float
    {
        $result = 0.0;
        foreach ($products as $product) {
            $result += (float) $product->getFinalPrice();
        }

        return $result;
    }

    /**
     * @param CartItemInterface[] $cartItems
     * @param ProductInterface[] $products
     * @return array
     */
    private function getCartItemsByProducts(array $cartItems, array $products)
    {
        $result = [];
        foreach ($cartItems as $item) {
            if ($item->getAdditionalData() === 'upsell'
                && isset($products[$item->getSku()])) {
                $result[$item->getSku()] = $item;
            }
        }

        return $result;
    }

    /**
     * @param CartInterface $cart
     * @param ProductInterface[] $products
     * @return CartInterface
     */
    private function addProductsToQuote(CartInterface $cart, array $products): CartInterface
    {
        foreach ($products as $product) {
            /** @var CartItemInterface $quoteItem */
            $quoteItem = $this->cartItemFactory->create();
            $quoteItem->setProduct($product)
                ->setQty(1)
                ->setCustomPrice($product->getFinalPrice())
                ->setOriginalCustomPrice($product->getFinalPrice())
                ->setAdditionalData('upsell');
            $quoteItem->getProduct()
                ->setIsSuperMode(true);
            $cart->addItem($quoteItem);
        }
        $cart->collectTotals();

        $result = $this->cartRepository->save($cart);

        return $cart;
    }
}
