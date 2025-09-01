<?php

namespace Walley\Upsell\Api\Data;

interface UpsellConfigInterface
{
    public const IS_ACTIVE = 'is_active';
    public const LIMIT = 'limit';
    public const RECOMMENDATION_TYPE = 'recommendation_type';
    public const TIMERTEXT = 'timertext';
    public const HEADER = 'header';
    public const CATEGORY_ID = 'category_id';
    public const COUNTDOWN_SECONDS = 'countdown_seconds';
    public const ALLOWED_PAYMENT_METHODS = 'allowed_payment_methods';

    /**
     * @return bool
     */
    public function getIsActive():bool;

    /**
     * @param bool $isActive
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function setIsActive(bool $isActive):\Walley\Upsell\Api\Data\UpsellConfigInterface;
    /**
     * @return int
     */
    public function getLimitOfProducts():int;

    /**
     * @param int $limit
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function setLimitOfProducts(int $limit):\Walley\Upsell\Api\Data\UpsellConfigInterface;
    /**
     * @return int
     */
    public function getRecommendationType():int;

    /**
     * @param int $type
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function setRecommendationType(int $type):\Walley\Upsell\Api\Data\UpsellConfigInterface;

    /**
     * @return string
     */
    public function getTimerText():string;

    /**
     * @param string $timertext
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function setTimerText(string $timertext):\Walley\Upsell\Api\Data\UpsellConfigInterface;

    /**
     * @return string
     */
    public function getHeader():string;

    /**
     * @param string $header
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */

    /**
     * @return array
     */
    public function getAllowedPaymentMethods():array;

    /**
     * @param string $allowedPaymentMethods
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function setAllowedPaymentMethods(array $allowedPaymentMethods):\Walley\Upsell\Api\Data\UpsellConfigInterface;

    /**
     * @param string $header
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function setHeader(string $header):\Walley\Upsell\Api\Data\UpsellConfigInterface;
    /**
     * @return int
     */
    public function getCategoryId():int;

    /**
     * @param int $categoryId
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function setCategoryId(int $categoryId):\Walley\Upsell\Api\Data\UpsellConfigInterface;

    /**
     * @return int
     */
    public function getCountdownSeconds():int;

    /**
     * @param int $seconds
     * @return \Walley\Upsell\Api\Data\UpsellConfigInterface
     */
    public function setCountdownSeconds(int $seconds):\Walley\Upsell\Api\Data\UpsellConfigInterface;
}
