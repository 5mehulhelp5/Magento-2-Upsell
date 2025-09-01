<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Products;

use Magento\Framework\DataObject;
use Walley\Upsell\Api\Data\UpsellProductInterface;

class UpsellProduct extends DataObject implements UpsellProductInterface
{

    public function getImage(): string
    {
        return (string) $this->_getData(self::IMAGE);
    }

    public function getFormattedPrice(): string
    {
        return (string) $this->_getData(self::FORMATTED_PRICE);
    }

    public function getName(): string
    {
        return (string) $this->_getData(self::NAME);
    }

    public function getSku(): string
    {
        return (string) $this->_getData(self::SKU);
    }

    public function getProductId(): int
    {
        return (int)$this->_getData(self::PRODUCT_ID);
    }

    public function setImage(string $image): UpsellProductInterface
    {
        return $this->setData(self::IMAGE, $image);
    }

    public function setFormattedPrice(string $formattedPrice): UpsellProductInterface
    {
        return $this->setData(self::FORMATTED_PRICE, $formattedPrice);
    }


    public function setName(string $name): UpsellProductInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function setSku(string $sku): UpsellProductInterface
    {
        return $this->setData(self::SKU, $sku);
    }

    public function setProductId(int $productId): UpsellProductInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function getFinalPrice(): float
    {
        return (float)$this->_getData(self::FINAL_PRICE);
    }

    public function setFinalPrice(float $finalPrice): \Walley\Upsell\Api\Data\UpsellProductInterface
    {
        return $this->setData(self::FINAL_PRICE, $finalPrice);
    }
}
