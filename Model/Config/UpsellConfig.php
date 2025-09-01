<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Walley\Upsell\Api\Data\UpsellConfigInterface;

class UpsellConfig extends DataObject implements UpsellConfigInterface
{
    public function getIsActive():bool
    {
        return (bool) $this->_getData(self::IS_ACTIVE);
    }

    /**
     * @return int
     */
    public function getLimitOfProducts():int
    {
        return (int) $this->_getData(self::LIMIT);
    }

    /**
     * @return int
     */
    public function getRecommendationType():int
    {
        return (int) $this->_getData(self::RECOMMENDATION_TYPE);
    }

    /**
     * @return string
     */
    public function getTimerText():string
    {
        return (string) $this->_getData(self::TIMERTEXT);
    }

    /**
     * @return string
     */
    public function getHeader():string
    {
        return (string) $this->_getData(self::HEADER);
    }

    /**
     * @return int
     */
    public function getCategoryId():int
    {
        return (int) $this->_getData(self::CATEGORY_ID);
    }

    /**
     * @return int
     */
    public function getCountdownSeconds():int
    {
        return (int) $this->_getData(self::COUNTDOWN_SECONDS);
    }

    public function setIsActive(bool $isActive): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function setLimitOfProducts(int $limit): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->setData(self::LIMIT, $limit);
    }

    public function setRecommendationType(int $type): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->setData(self::RECOMMENDATION_TYPE, $type);
    }

    public function setTimerText(string $timertext): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->setData(self::TIMERTEXT, $timertext);
    }

    public function setHeader(string $header): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->setData(self::HEADER, $header);
    }

    public function setCategoryId(int $categoryId): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    public function setCountdownSeconds(int $seconds): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->setData(self::COUNTDOWN_SECONDS, $seconds);
    }

    public function getAllowedPaymentMethods(): array
    {
        return $this->_getData(self::ALLOWED_PAYMENT_METHODS);
    }

    public function setAllowedPaymentMethods(array $allowedPaymentMethods): \Walley\Upsell\Api\Data\UpsellConfigInterface
    {
        return $this->setData(self::ALLOWED_PAYMENT_METHODS, $allowedPaymentMethods);
    }
}
