<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface;

class AddUpsellToOrderResponse extends \Magento\Framework\DataObject implements AddUpsellToOrderResponseInterface
{

    public function getStatus(): int
    {
        return $this->_getData(self::STATUS);
    }

    public function setStatus(int $status): \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getMessage(): string
    {
        return $this->_getData(self::MESSAGE);
    }

    public function setMessage(string $message): \Walley\Upsell\Api\Data\AddUpsellToOrderResponseInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
