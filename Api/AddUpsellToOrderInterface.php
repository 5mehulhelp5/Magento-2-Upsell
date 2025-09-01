<?php

namespace Walley\Upsell\Api;

use Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface;

interface AddUpsellToOrderInterface
{
    /**
     * @param mixed $productIds
     * @param string $publicToken
     * @param int $orderId
     * @return \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface
     */
    public function execute($productIds, $publicToken, $orderId):\Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface;
}
