<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\WalleyOrder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Pricing\Price\FinalPrice as FinalPriceCode;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Pricing\Adjustment as TaxAdjustment;

class GetProductVatRate
{
    /** @var Calculation */
    protected $calculation;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        Calculation $calculation,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->calculation = $calculation;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(ProductInterface $product, int $storeId): float
    {
        $product->setStoreId($storeId);

        $productClassId = (int)$product->getTaxClassId();
        if ($productClassId <= 0) {
            return 0.0;
        }

        $country  = (string)$this->scopeConfig->getValue('tax/defaults/country',  ScopeInterface::SCOPE_STORE, $storeId);
        $regionId = (int)$this->scopeConfig->getValue('tax/defaults/region',     ScopeInterface::SCOPE_STORE, $storeId);
        $postcode = (string)$this->scopeConfig->getValue('tax/defaults/postcode', ScopeInterface::SCOPE_STORE, $storeId);

        $shippingAddress = new DataObject([
            'country_id' => $country ?: null,
            'region_id'  => $regionId ?: null,
            'postcode'   => $postcode ?: null,
        ]);

        $customerClassId = (int)$this->scopeConfig->getValue(
            'tax/classes/default_customer_tax_class',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: null;

        $request = $this->calculation->getRateRequest(
            $shippingAddress,
            null,
            $customerClassId,
            $storeId
        );
        $request->setProductClassId($productClassId);

        $rate = (float)$this->calculation->getRate($request);
        if ($rate > 0.0) {
            return round($rate, 2);
        }

        $amount = $product->getPriceInfo()
            ->getPrice(FinalPriceCode::PRICE_CODE)
            ->getAmount();

        $excl = (float)$amount->getValue();
        $tax  = (float)$amount->getAdjustmentAmount(TaxAdjustment::ADJUSTMENT_CODE);

        if ($excl > 0.0 && $tax > 0.0) {
            return round(($tax / $excl) * 100, 2);
        }

        return 0.0;
    }
}
