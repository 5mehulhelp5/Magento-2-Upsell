<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\WalleyOrder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Catalog\Pricing\Price\FinalPrice as FinalPriceCode;

class GetProductVatRate
{
    /** @var CatalogHelper */
    protected $catalogHelper;

    /** @var TaxHelper */
    protected $taxHelper;

    public function __construct(
        CatalogHelper $catalogHelper,
        TaxHelper $taxHelper
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->taxHelper = $taxHelper;
    }

    public function execute(ProductInterface $product, int $storeId): float
    {
        $product->setStoreId($storeId);

        $baseFinal = (float)$product->getPriceInfo()
            ->getPrice(FinalPriceCode::PRICE_CODE)
            ->getValue();

        if ($baseFinal <= 0.0) {
            return 0.0;
        }
        $priceIncludesTax = $this->taxHelper->priceIncludesTax($storeId);
        $excl = (float)$this->catalogHelper->getTaxPrice(
            $product,
            $baseFinal,
            false,
            null,
            null,
            null,
            $storeId,
            $priceIncludesTax
        );

        $incl = (float)$this->catalogHelper->getTaxPrice(
            $product,
            $baseFinal,
            true,
            null,
            null,
            null,
            $storeId,
            $priceIncludesTax
        );

        if ($excl <= 0.0 || $incl <= $excl) {
            return 0.0;
        }

        return round((($incl - $excl) / $excl) * 100, 2);
    }
}
