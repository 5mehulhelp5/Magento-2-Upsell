<?php
namespace Walley\Upsell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Walley\Upsell\Model\ChangeOrderStatusTimeout;

class ChangeOrderStatus extends Command
{
    private $changeOrderStatusTimeout;

    public function __construct(ChangeOrderStatusTimeout $changeOrderStatusTimeout)
    {
        $this->changeOrderStatusTimeout = $changeOrderStatusTimeout;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('walley:upsell:change-order-status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->changeOrderStatusTimeout->execute();
        return 0;
    }
}
