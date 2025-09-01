<?php

namespace Walley\Upsell\Api;

use Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface;

interface StatusUpsellToOrderInterface
{
    /**
     * @param string $publicToken
     * @param int $orderId
     * @param string $reauthorizationId
     * @return \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface
     */
    public function execute($publicToken, $orderId, $reauthorizationId):\Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface;
}
