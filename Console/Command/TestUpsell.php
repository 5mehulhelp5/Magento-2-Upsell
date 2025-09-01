<?php
declare(strict_types=1);

namespace Walley\Upsell\Console\Command;

use Magento\Framework\App\State as AppState;
use Magento\Store\Model\App\Emulation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Walley\Upsell\Model\Order\GetOrderByIncrementId;

class TestUpsell extends Command
{
    /**
     * @var AppState
     */
    private AppState $appState;

    private Emulation $emulation;
    private GetOrderByIncrementId $getOrderByIncrementId;

    public function __construct(
        AppState $appState,
        GetOrderByIncrementId $getOrderByIncrementId,
        Emulation $emulation
    ) {
        parent::__construct();

        $this->appState = $appState;
        $this->emulation = $emulation;
        $this->getOrderByIncrementId = $getOrderByIncrementId;
    }

    protected function configure()
    {
        $options = [
            new InputOption(
                'incrementId',
                null,
                InputOption::VALUE_REQUIRED,
                'incrementId'
            )
        ];
        $this->setName('walley:test:upsell')
            ->setDescription('Test upsell for order and store. It uses the store id for the order.')
            ->setDefinition($options);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('adminhtml');

        $incrementId = (string) $input->getOption('incrementId');
        $order = $this->getOrderByIncrementId->execute($incrementId);

        $this->emulation->startEnvironmentEmulation((int) $order->getStoreId(), 'frontend');

        $orderId = (int) $order->getId();
        $output->writeln($orderId);

        $this->emulation->stopEnvironmentEmulation();

        return 1;
    }
}
