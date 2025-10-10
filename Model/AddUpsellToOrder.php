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
        if (!$order || (int)$order->getEntityId() !== (int)$orderId) {
            return $response->setStatus(401)
                ->setMessage('Failed to add upsell - please contact customer service');
        }
        $storeId  = (int)$order->getStoreId();
        $products = $this->getProductsByIds->execute($productIds, $storeId);
        if (empty($products)) {
            return $response->setStatus(422)->setMessage((string)__('No products to add'));
        }

        $walleyResponse = $this->addProductsToWalleyOrder->execute($products, $order);
        $status = (int)($walleyResponse['status'] ?? 500);

        switch ($status) {
            case 201:
                try{
                    $reauthorizationId = $walleyResponse['reauthorizationId'] ?? null;
                    $isSuccess = $this->addOrderItems->execute($order, $products);
                    if ($isSuccess) {
                        $reauthorizationId = $walleyResponse['reauthorizationId'] ?? null;
                    }
                } catch (\Throwable $e) {
                    return $response->setStatus(500)->setMessage((string)__('Failed please contact customer support'));
                }
                return $response->setStatus(201)->setMessage((string)$reauthorizationId);
            case 202:
                try {
                    $isSuccess = $this->addOrderItems->execute($order, $products);
                    if ($isSuccess) {
                        return $response->setStatus(202)->setMessage('Success - Items added');
                    }
                    return $response->setStatus(500)->setMessage((string)__('Failed please contact customer support'));
                } catch (\Throwable $e) {
                    return $response->setStatus(500)->setMessage((string)__('Failed please contact customer support'));
                }
            case 401:
                return $response->setStatus(401)
                    ->setMessage((string)__('Unauthorized, token verification needed. See: Authentication for more information'));
            case 403:
                return $response->setStatus(403)
                    ->setMessage((string)__('Permissions needed e.g. trying to handle content for a store you don\'t have permission to'));
            case 404:
                return $response->setStatus(404)->setMessage((string)__('Order not found'));
            case 422:
                return $response->setStatus(422)->setMessage((string)__('See error codes'));
            default:
                return $response->setStatus(500)->setMessage((string)__('Unrecognized response code'));
        }
    }
}
