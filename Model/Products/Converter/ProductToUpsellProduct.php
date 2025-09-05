<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Products\Converter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Framework\Pricing\Render;
use Magento\Store\Model\StoreManagerInterface;
use Walley\Upsell\Api\Data\UpsellProductInterface;
use Walley\Upsell\Api\Data\UpsellProductInterfaceFactory;
use Walley\Upsell\Model\GetFinalPriceIncludingTax;

class ProductToUpsellProduct
{
    private UpsellProductInterfaceFactory $upsellProductInterfaceFactory;
    private ProductHelper $productHelper;
    private Render $priceRenderer;
    private GetFinalPriceIncludingTax $getFinalPriceIncludingTax;
    private StoreManagerInterface $storeManager;

    public function __construct(
        UpsellProductInterfaceFactory $upsellProductInterfaceFactory,
        ProductHelper $productHelper,
        StoreManagerInterface $storeManager,
        GetFinalPriceIncludingTax $getFinalPriceIncludingTax,
        Render $priceRenderer
    ) {
        $this->upsellProductInterfaceFactory = $upsellProductInterfaceFactory;
        $this->productHelper = $productHelper;
        $this->priceRenderer = $priceRenderer;
        $this->getFinalPriceIncludingTax = $getFinalPriceIncludingTax;
        $this->storeManager = $storeManager;
    }

    public function execute(ProductInterface $product):UpsellProductInterface
    {
        /** @var UpsellProductInterface $upsellProduct */
        $upsellProduct = $this->upsellProductInterfaceFactory->create();
        $upsellProduct->setProductId((int)$product->getId())
            ->setSku($product->getSku())
            ->setName($product->getName())
            ->setImage($this->getImage($product))
            ->setFormattedPrice($this->getFormattedPrice($product))
            ->setFinalPrice((float)$this->getFinalPriceIncludingTax->execute($product, (int) $this->storeManager->getStore()->getId()));

        return $upsellProduct;
    }

    private function getFormattedPrice(ProductInterface $product):string
    {
        return $this->priceRenderer->render('final_price', $product,
            [
                'include_container' => false,
                'display_minimal_price' => false,
                'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                'list_category_page' => false
            ]
        );
    }

    private function getImage(ProductInterface $product):string
    {
        return $this->productHelper->getSmallImageUrl($product);
    }
}
