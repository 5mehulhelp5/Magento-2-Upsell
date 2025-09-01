<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Walley\Upsell\Api\Data\UpsellConfigInterface;

class ConfigProvider
{
    public const XML_PATH_UPSELL_IS_ACTIVE = 'payment/collectorbank_checkout/upsell/active';
    public const XML_PATH_UPSELL_RECOMMENDATION_TYPE = 'payment/collectorbank_checkout/upsell/recommendation_type';
    public const XML_PATH_UPSELL_CATEGORY_ID = 'payment/collectorbank_checkout/upsell/category_id';
    public const XML_PATH_UPSELL_COUNTDOWN_SECONDS = 'payment/collectorbank_checkout/upsell/countdown_seconds';
    public const XML_PATH_UPSELL_TIMER_TEXT = 'payment/collectorbank_checkout/upsell/timertext';
    public const XML_PATH_UPSELL_HEADER = 'payment/collectorbank_checkout/upsell/header';
    public const XML_PATH_UPSELL_TEST_PAGE = 'payment/collectorbank_checkout/upsell/enable_test';
    public const XML_PATH_UPSELL_ALLOWED_PAYMENT_METHODS = 'payment/collectorbank_checkout/upsell/allowed_payment_methods';
    public const XML_PATH_UPSELL_ORDER_STATUS = 'payment/collectorbank_checkout/upsell/order_upsell_status';

    private const LIMIT_OF_PRODUCTS = 10;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getIsActive($storeId = null):bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_IS_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return int
     */
    public function getLimitOfProducts($storeId = null):int
    {
        return self::LIMIT_OF_PRODUCTS;
    }

    /**
     * @return int
     */
    public function getRecommendationType($storeId = null):int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_RECOMMENDATION_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getTimerText($storeId = null):string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_TIMER_TEXT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getUpsellOrderStatus($storeId = null):string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getHeader($storeId = null):string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_HEADER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return int
     */
    public function getCategoryId($storeId = null):int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_CATEGORY_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool
     */
    public function isTestPageEnabled($storeId = null):bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_TEST_PAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return array
     */
    public function getAllowedPaymentMethods($storeId = null):array
    {
        $paymentMethodsAsString = $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_ALLOWED_PAYMENT_METHODS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (!$paymentMethodsAsString) {
            return [];
        }
        return explode(',', $paymentMethodsAsString);
    }

    /**
     * @return int
     */
    public function getCountdownSeconds($storeId = null):int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_UPSELL_COUNTDOWN_SECONDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getAcknowledgeOrderStatus(int $storeId): string
    {
        return (string)$this->scopeConfig->getValue(
            'payment/collectorbank_checkout/configuration/order_accepted_status',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
