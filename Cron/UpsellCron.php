<?php
namespace Walley\Upsell\Cron;

use Walley\Upsell\Model\ChangeOrderStatusTimeout;

class UpsellCron
{
    private ChangeOrderStatusTimeout $changeOrderStatusTimeout;

    public function __construct(
        ChangeOrderStatusTimeout $changeOrderStatusTimeout
    ) {
        $this->changeOrderStatusTimeout = $changeOrderStatusTimeout;
    }

    public function execute()
    {
        $this->changeOrderStatusTimeout->execute();
    }
}
