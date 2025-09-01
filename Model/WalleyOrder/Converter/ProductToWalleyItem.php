<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\WalleyOrder\Converter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config;

class ProductToWalleyItem
{
    protected $storeManager;
    protected $taxCalculation;
    protected $taxConfig;

    public function __construct(
        StoreManagerInterface $storeManager,
        TaxCalculationInterface $taxCalculation,
        Config $taxConfig
    ) {
        $this->storeManager = $storeManager;
        $this->taxCalculation = $taxCalculation;
        $this->taxConfig = $taxConfig;
    }

    public function execute(ProductInterface $product, int $qty = 1): array
    {
        return [
            "id" => $product->getSku(),
            "description" => $product->getName(),
            "unitPrice" => round((float)$product->getFinalPrice(), 2),
            "quantity" => $qty,
            "vat" => $this->getProductTaxRate($product)
        ];
    }

    private function getProductTaxRate(ProductInterface $product): float
    {
        $storeId = $this->storeManager->getStore()->getId();
        $taxClassId = $product->getTaxClassId();

        return (float) $this->taxCalculation->getCalculatedRate($taxClassId, null, $storeId);
    }
}
