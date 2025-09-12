<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Pricing\Price\FinalPrice as FinalPriceCode;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Tax\Helper\Data as TaxHelper;

class GetFinalPriceIncludingTax
{
    /**
     * @var CatalogHelper
     */
    protected $catalogHelper;

    /**
     * @var TaxHelper
     */
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

        $final = $product->getPriceInfo()
            ->getPrice(FinalPriceCode::PRICE_CODE)
            ->getValue();

        $priceIncludesTax = $this->taxHelper->priceIncludesTax($storeId);

        $incl = $this->catalogHelper->getTaxPrice(
            $product,
            $final,
            true,
            null,
            null,
            null,
            $storeId,
            $priceIncludesTax
        );

        return (float)$incl;
    }
}
