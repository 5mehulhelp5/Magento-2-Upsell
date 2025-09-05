<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Pricing\Price\FinalPrice as FinalPriceCode;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Pricing\Adjustment as TaxAdjustment;

class GetFinalPriceIncludingTax
{
    public function execute(ProductInterface $product, int $storeId): float
    {
        $product->setStoreId($storeId);
        $amount = $product->getPriceInfo()
            ->getPrice(FinalPriceCode::PRICE_CODE)
            ->getAmount();

        return $amount->getValue();
    }
}
