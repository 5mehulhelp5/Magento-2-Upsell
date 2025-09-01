<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Walley\Upsell\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class AddUpsellOrderStatus implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;
    private StatusFactory $statusFactory;
    private StatusResource $statusResource;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StatusFactory $statusFactory,
        StatusResource $statusResource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->statusFactory = $statusFactory;
        $this->statusResource = $statusResource;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $orderStatus = [
            'status' => 'collectorbank_upsell',
            'label' => 'Walley - Upsell waiting'
        ];
        $status = $this->statusFactory->create();
        $status->setData($orderStatus);
        try {
            $this->statusResource->save($status);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {}
        $status->assignState(\Magento\Sales\Model\Order::STATE_PROCESSING, false, true);
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
