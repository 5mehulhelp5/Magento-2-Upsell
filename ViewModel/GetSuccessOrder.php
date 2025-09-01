<?php
declare(strict_types=1);

namespace Walley\Upsell\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Webbhuset\CollectorCheckout\Checkout\Order\Manager;

class GetSuccessOrder implements ArgumentInterface
{
    private Manager $manager;
    private RequestInterface $request;

    public function __construct(
        Manager $manager,
        RequestInterface $request
    ) {
        $this->manager = $manager;
        $this->request = $request;
    }

    public function execute(): ?OrderInterface
    {
        $publicToken = $this->request->getParam('reference');
        try {
            $order = $this->manager->getOrderByPublicToken($publicToken);
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $order;
    }
}
