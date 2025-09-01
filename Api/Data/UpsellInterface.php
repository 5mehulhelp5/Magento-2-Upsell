<?php

namespace Walley\Upsell\Api\Data;

interface UpsellInterface
{
    public const CONFIG = 'config';
    public const PRODUCTS = 'products';

    /**
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function getConfig():\Walley\Upsell\Api\Data\UpsellConfigInterface;

    /**
     * @param \Walley\Upsell\Api\Data\UpsellConfigInterface $config
     * @return \Walley\Upsell\Api\Data\UpsellInterface
     */
    public function setConfig(\Walley\Upsell\Api\Data\UpsellConfigInterface $config):\Walley\Upsell\Api\Data\UpsellInterface;
    /**
     * @return \Walley\Upsell\Api\Data\UpsellProductInterface[]
     */
    public function getUpsellProducts():array;

    /**
     * @param \Walley\Upsell\Api\Data\UpsellProductInterface[] $upsellProducts
     * @return \Walley\Upsell\Api\Data\UpsellInterface
     */
    public function setUpsellProducts(array $upsellProducts):\Walley\Upsell\Api\Data\UpsellInterface;

}
