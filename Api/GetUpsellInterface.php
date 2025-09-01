<?php

namespace Walley\Upsell\Api;

use Walley\Upsell\Api\Data\UpsellInterface;

interface GetUpsellInterface
{
    /**
     * @param int $orderId
     * @return \Walley\Upsell\Api\Data\UpsellInterface
     */
    public function execute(int $orderId):\Walley\Upsell\Api\Data\UpsellInterface;

    /**
     * @param \Walley\Upsell\Api\Data\UpsellInterface $upsell
     * @return array
     */
    public function upsellToArray(\Walley\Upsell\Api\Data\UpsellInterface $upsell):array;
}
