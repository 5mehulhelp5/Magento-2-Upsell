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

        $rowExcl = 0.0;       $baseRowExcl = 0.0;
        $rowIncl = 0.0;       $baseRowIncl = 0.0;
        $taxInc  = 0.0;       $baseTaxInc  = 0.0;
        $qtyInc  = 0.0;       $itemInc     = 0;

        foreach ($products as $product) {
            if (!isset($itemBySku[$product->getSku()])) {
                return false;
            }

            /** @var \Magento\Sales\Model\Order\Item $orderItem */
            $orderItem = $this->orderItemFactory->create();
            /** @var \Magento\Quote\Api\Data\CartItemInterface $quoteItem */
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

            $rowExcl     += (float)$quoteItem->getRowTotal();
            $baseRowExcl += (float)$quoteItem->getBaseRowTotal();
            $rowIncl     += (float)$quoteItem->getRowTotalInclTax();
            $baseRowIncl += (float)$quoteItem->getBaseRowTotalInclTax();
            $taxInc      += (float)$quoteItem->getTaxAmount();
            $baseTaxInc  += (float)$quoteItem->getBaseTaxAmount();
            $qtyInc      += (float)$quoteItem->getQty();
            $itemInc++;
        }

        $order->setSubtotal($order->getSubtotal() + $rowExcl)
            ->setBaseSubtotal($order->getBaseSubtotal() + $baseRowExcl)
            ->setSubtotalInclTax(($order->getSubtotalInclTax() ?: 0) + $rowIncl)
            ->setBaseSubtotalInclTax(($order->getBaseSubtotalInclTax() ?: 0) + $baseRowIncl)
            ->setTaxAmount($order->getTaxAmount() + $taxInc)
            ->setBaseTaxAmount($order->getBaseTaxAmount() + $baseTaxInc)
            ->setGrandTotal($order->getGrandTotal() + $rowIncl)
            ->setBaseGrandTotal($order->getBaseGrandTotal() + $baseRowIncl)
            ->setTotalDue(($order->getTotalDue() ?: 0) + $rowIncl)
            ->setTotalItemCount($order->getTotalItemCount() + $itemInc)
            ->setTotalQtyOrdered($order->getTotalQtyOrdered() + $qtyInc);

        $this->orderRepository->save($order);
        return true;
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
