<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Magento\Framework\DataObject;
use Walley\Upsell\Api\Data\UpsellInterface;

class Upsell extends DataObject implements UpsellInterface
{

    public function getConfig(): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->_getData(self::CONFIG);
    }

    public function setConfig(\Walley\Upsell\Api\Data\UpsellConfigInterface $config): \Walley\Upsell\Api\Data\UpsellInterface
    {
        return $this->setData(self::CONFIG, $config);
    }

    public function getUpsellProducts(): array
    {
        return $this->_getData(self::PRODUCTS);
    }

    public function setUpsellProducts(array $upsellProducts): \Walley\Upsell\Api\Data\UpsellInterface
    {
        return $this->setData(self::PRODUCTS, $upsellProducts);
    }
}
