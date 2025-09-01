<?php
declare(strict_types=1);

namespace Walley\Upsell\Model\Products\Recommendation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;

class GetCustomProductsByOrder
{
    /**
     * @param OrderInterface $order
     * @return ProductInterface[]
     */
    public function execute(OrderInterface $order):array
    {
        return [];
    }
}
