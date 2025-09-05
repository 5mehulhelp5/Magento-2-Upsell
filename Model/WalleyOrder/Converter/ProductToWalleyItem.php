<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\WalleyOrder\Converter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config;
use Walley\Upsell\Model\GetFinalPriceIncludingTax;

class ProductToWalleyItem
{
    protected $taxCalculation;
    protected $taxConfig;
    private GetFinalPriceIncludingTax $getFinalPriceIncludingTax;

    public function __construct(
        TaxCalculationInterface $taxCalculation,
        GetFinalPriceIncludingTax $getFinalPriceIncludingTax,
        Config $taxConfig
    ) {
        $this->taxCalculation = $taxCalculation;
        $this->taxConfig = $taxConfig;
        $this->getFinalPriceIncludingTax = $getFinalPriceIncludingTax;
    }

    public function execute(ProductInterface $product, int $storeId, int $qty = 1): array
    {
        return [
            "id" => $product->getSku(),
            "description" => $product->getName(),
            "unitPrice" => $this->getFinalPriceIncludingTax->execute($product, $storeId),
            "quantity" => $qty,
            "vat" => $this->getProductTaxRate($product, $storeId)
        ];
    }

    private function getProductTaxRate(ProductInterface $product, int $storeId): float
    {
        $taxClassId = $product->getTaxClassId();

        return (float) $this->taxCalculation->getCalculatedRate($taxClassId, null, $storeId);
    }
}
