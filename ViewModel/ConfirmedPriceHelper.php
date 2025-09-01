<?php
declare(strict_types=1);

namespace Walley\Upsell\ViewModel;

use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ConfirmedPriceHelper implements ArgumentInterface
{

    private FormatInterface $format;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        FormatInterface $format,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->format = $format;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $currency = $this->storeManager->getStore()->getCurrentCurrencyCode();
        return $this->format->getPriceFormat(null, $currency);
    }
}
