<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\WalleyOrder\Converter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config;
use Walley\Upsell\Model\GetFinalPriceIncludingTax;
use Walley\Upsell\Model\WalleyOrder\GetProductVatRate;

class ProductToWalleyItem
{
    protected $taxCalculation;
    protected $taxConfig;
    protected $getFinalPriceIncludingTax;
    protected $getProductVatRate;

    public function __construct(
        TaxCalculationInterface $taxCalculation,
        GetFinalPriceIncludingTax $getFinalPriceIncludingTax,
        GetProductVatRate $getProductVatRate,
        Config $taxConfig
    ) {
        $this->taxCalculation = $taxCalculation;
        $this->taxConfig = $taxConfig;
        $this->getFinalPriceIncludingTax = $getFinalPriceIncludingTax;
        $this->getProductVatRate = $getProductVatRate;
    }

    public function execute(ProductInterface $product, int $storeId, int $qty = 1): array
    {
        return [
            "id" => $product->getSku(),
            "description" => $product->getName(),
            "unitPrice" => $this->getFinalPriceIncludingTax->execute($product, $storeId),
            "quantity" => $qty,
            "vat" => $this->getProductVatRate->execute($product, $storeId)
        ];
    }
}
