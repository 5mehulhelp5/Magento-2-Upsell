<?php
declare(strict_types=1);

namespace Walley\Upsell\Controller\Test;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Walley\Upsell\Model\Config\ConfigProvider;
use Walley\Upsell\Model\Order\GetOrderByIncrementId;
use Walley\Upsell\ViewModel\IsUpsellAllowedForOrder;
use Webbhuset\CollectorCheckout\Config\Config;
use Webbhuset\CollectorCheckoutSDK\Config\IframeConfig;
use Webbhuset\CollectorCheckoutSDK\Iframe;

class Index implements ActionInterface
{
    private ResultFactory $resultFactory;
    private ConfigProvider $configProvider;
    private RequestInterface $request;
    private GetOrderByIncrementId $getOrderByIncrementId;
    private IsUpsellAllowedForOrder $isUpsellAllowedForOrder;
    private Config $walleyConfig;

    /**
     * Index constructor.
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        ResultFactory $resultFactory,
        GetOrderByIncrementId $getOrderByIncrementId,
        ConfigProvider $configProvider,
        Config $walleyConfig,
        IsUpsellAllowedForOrder $isUpsellAllowedForOrder,
        RequestInterface $request
    ) {
        $this->resultFactory = $resultFactory;
        $this->configProvider = $configProvider;
        $this->request = $request;
        $this->getOrderByIncrementId = $getOrderByIncrementId;
        $this->isUpsellAllowedForOrder = $isUpsellAllowedForOrder;
        $this->walleyConfig = $walleyConfig;
    }

    public function execute()
    {
        if (!$this->configProvider->isTestPageEnabled()) {
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');

            return $resultForward;
        }
        /** @var Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $incrementOrderId = $this->request->getParam('id');
        $order = $this->getOrderByIncrementId->execute($incrementOrderId);
        $isUpsellAllowed = $this->isUpsellAllowedForOrder->execute($order);

        if (!$isUpsellAllowed) {
            $page->getConfig()->getTitle()->set(__('Upsell is not allowed for this order'));
        }

        $publicToken = $order->getCollectorbankPublicId();
        $iframeConfig = new IframeConfig(
            $publicToken
        );
        $iframe = Iframe::getScript($iframeConfig, $this->walleyConfig->getMode());

        $layout = $page->getLayout();
        $layout->getBlock('success_upsell')
            ->setIsUpsellAllowed($isUpsellAllowed)
            ->setSuccessOrder($order);

        $layout->getBlock('collectorbank_success_iframe')
            ->setIframe($iframe)
            ->setSuccessOrder($order);

        return $page;
    }
}
