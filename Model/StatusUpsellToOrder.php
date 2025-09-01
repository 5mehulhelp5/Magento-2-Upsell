<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterfaceFactory as ResponseInterfaceFactory;
use Walley\Upsell\Api\StatusUpsellToOrderInterface;
use Walley\Upsell\Model\Order\AddOrderItems;
use Walley\Upsell\Model\Order\GetOrderByPublicToken;
use Walley\Upsell\Model\Products\GetProductsByIds;
use Walley\Upsell\Model\WalleyOrder\AddProductsToOrder;
use Walley\Upsell\Model\WalleyOrder\GetReauthorizeStatus;

class StatusUpsellToOrder implements StatusUpsellToOrderInterface
{
    private ResponseInterfaceFactory $responseInterfaceFactory;
    private GetOrderByPublicToken $getOrderByPublicToken;
    private GetReauthorizeStatus $getReauthorizeStatus;

    public function __construct(
        ResponseInterfaceFactory $responseInterfaceFactory,
        GetOrderByPublicToken $getOrderByPublicToken,
        GetReauthorizeStatus $getReauthorizeStatus

    ) {
        $this->responseInterfaceFactory = $responseInterfaceFactory;
        $this->getOrderByPublicToken = $getOrderByPublicToken;
        $this->getReauthorizeStatus = $getReauthorizeStatus;
    }


    public function execute($publicToken, $orderId, $reauthorizationId): \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface
    {
        /** @var \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface $response */
        $response = $this->responseInterfaceFactory->create();
        $order = $this->getOrderByPublicToken->execute($publicToken);
        if (!$order || (int)$order->getEntityId() !== $orderId) {
            $response->setStatus(401)
                ->setMessage('Failed to get order');
        }
        $statusCode = $this->getReauthorizeStatus->execute($reauthorizationId, $order);
        $response->setStatus($statusCode)
            ->setMessage('Status is');

        return $response;
    }
}
