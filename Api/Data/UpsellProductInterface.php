<?php

namespace Walley\Upsell\Api\Data;

interface UpsellProductInterface
{
    public const PRODUCT_ID = 'product_id';
    public const SKU = 'sku';
    public const NAME = 'name';
    public const IMAGE = 'image';
    public const FORMATTED_PRICE = 'formatted_price';
    public const FINAL_PRICE = 'final_price';

    /**
     * @return string
     */
    public function getImage():string;

    /**
     * @param string $image
     * @return \Walley\Upsell\Api\Data\UpsellProductInterface;
     */
    public function setImage(string $image):\Walley\Upsell\Api\Data\UpsellProductInterface;

    /**
     * @return string
     */
    public function getFormattedPrice():string;

    /**
     * @param string $formattedPrice
     * @return \Walley\Upsell\Api\Data\UpsellProductInterface;
     */
    public function setFormattedPrice(string $formattedPrice):\Walley\Upsell\Api\Data\UpsellProductInterface;

    /**
     * @return float
     */
    public function getFinalPrice():float;

    /**
     * @param string $finalPrice
     * @return \Walley\Upsell\Api\Data\UpsellProductInterface;
     */
    public function setFinalPrice(float $finalPrice):\Walley\Upsell\Api\Data\UpsellProductInterface;


    /**
     * @return string
     */
    public function getName():string;

    /**
     * @param string $name
     * @return \Walley\Upsell\Api\Data\UpsellProductInterface;
     */
    public function setName(string $name):\Walley\Upsell\Api\Data\UpsellProductInterface;

    /**
     * @return string
     */
    public function getSku():string;

    /**
     * @param string $sku
     * @return \Walley\Upsell\Api\Data\UpsellProductInterface;
     */
    public function setSku(string $sku):\Walley\Upsell\Api\Data\UpsellProductInterface;

    /**
     * @return int
     */
    public function getProductId():int;

    /**
     * @param int $productId
     * @return \Walley\Upsell\Api\Data\UpsellProductInterface;
     */
    public function setProductId(int $productId):\Walley\Upsell\Api\Data\UpsellProductInterface;
}
