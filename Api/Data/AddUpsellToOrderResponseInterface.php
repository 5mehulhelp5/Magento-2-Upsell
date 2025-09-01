<?php

namespace Walley\Upsell\Api\Data;

interface AddUpsellToOrderResponseInterface
{
    public const STATUS = 'status';
    public const MESSAGE = 'message';
    /**
     * @return int
     */
    public function getStatus():int;

    /**
     * @param int $status
     * @return \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface
     */
    public function setStatus(int $status):\Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface;

    /**
     * @return string
     */
    public function getMessage():string;

    /**
     * @param string $message
     * @return \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface;
     */
    public function setMessage(string $message):\Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface;

}
