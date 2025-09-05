<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Walley\Upsell\Api\AddUpsellToOrderInterface;
use Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterfaceFactory as ResponseInterfaceFactory;
use Walley\Upsell\Model\Order\AddOrderItems;
use Walley\Upsell\Model\Order\GetOrderByPublicToken;
use Walley\Upsell\Model\Products\GetProductsByIds;
use Walley\Upsell\Model\WalleyOrder\AddProductsToOrder;

class AddUpsellToOrder implements AddUpsellToOrderInterface
{
    private ResponseInterfaceFactory $responseInterfaceFactory;
    private GetOrderByPublicToken $getOrderByPublicToken;
    private AddProductsToOrder $addProductsToWalleyOrder;
    private GetProductsByIds $getProductsByIds;
    private AddOrderItems $addOrderItems;

    public function __construct(
        ResponseInterfaceFactory $responseInterfaceFactory,
        GetOrderByPublicToken $getOrderByPublicToken,
        AddProductsToOrder $addProductsToOrder,
        AddOrderItems $addOrderItems,
        GetProductsByIds $getProductsByIds
    ) {
        $this->responseInterfaceFactory = $responseInterfaceFactory;
        $this->getOrderByPublicToken = $getOrderByPublicToken;
        $this->addProductsToWalleyOrder = $addProductsToOrder;
        $this->getProductsByIds = $getProductsByIds;
        $this->addOrderItems = $addOrderItems;
    }

    public function execute($productIds, $publicToken, $orderId): \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface
    {
        /** @var \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface $response */
        $response = $this->responseInterfaceFactory->create();
        $order = $this->getOrderByPublicToken->execute($publicToken);
        if (!$order || (int)$order->getEntityId() !== $orderId) {
            $response->setStatus(401)
                ->setMessage('Failed to add upsell - please contact customer service');
        }
        $storeId = (int) $order->getStoreId();
        $products = $this->getProductsByIds->execute($productIds, $storeId);
        $isSuccess = $this->addOrderItems->execute($order, $products);

        $walleyResponse = $this->addProductsToWalleyOrder->execute($products, $order);
        $status = $walleyResponse['status'];
        switch ($status) {
            case 201:
                $reauthorizationId = $walleyResponse['reauthorizationId'];
                $response->setStatus(201)
                    ->setMessage($reauthorizationId);
                break;
            case 202:

                if ($isSuccess) {
                    $response->setStatus(202)
                        ->setMessage('Success - Items added');
                } else {
                    $response->setStatus(500)
                        ->setMessage((string)__("Failed please contact customer support"));
                }
                break;
            case 401:
                $response->setStatus(401)
                    ->setMessage((string)__("Unauthorized, token verification needed. See: Authentication for more information"));
                break;
            case 403:
                $response->setStatus(403)
                    ->setMessage((string)__("Permissions needed e.g. trying to handle content for a store you don't have permission to"));
                break;
            case 404:
                $response->setStatus(404)
                    ->setMessage((string)__("Order not found"));
                break;
            case 422:
                $response->setStatus(422)
                    ->setMessage((string)__("See error codes"));
                break;
            default:
                $response->setStatus(500)
                    ->setMessage((string)__("Unrecognized response code"));
        }
        return $response;
    }

}
