<?php
declare(strict_types=1);

namespace Walley\Upsell\Plugin;

use Magento\Quote\Model\ChangeQuoteControl;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Item;
use Walley\Upsell\Model\Config\ConfigProvider;

class AllowChangeQuoteControl
{
    private ConfigProvider $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function afterIsAllowed(
        ChangeQuoteControl $subject,
                           $result,
        CartInterface $quote
    ): bool {
        if ($this->configProvider->getIsActive() && $this->hasQuoteUpsell($quote)) {
            return true;
        }

        return $result;
    }

    private function hasQuoteUpsell(CartInterface $quote): bool
    {
        foreach ($quote->getAllItems() as $item) {
            if ($item->getAdditionalData() === 'upsell') {
                return true;
            }
        }

        return false;
    }
}
