<?php
declare(strict_types=1);

namespace Walley\Upsell\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walley\Upsell\Api\Data\UpsellConfigInterface;
use Walley\Upsell\Api\Data\UpsellConfigInterfaceFactory;
use Walley\Upsell\Api\Data\UpsellInterface;
use Walley\Upsell\Api\Data\UpsellInterfaceFactory;
use Walley\Upsell\Api\GetUpsellInterface;
use Walley\Upsell\Model\Config\ConfigProvider;
use Walley\Upsell\Model\Products\Converter\ProductToUpsellProduct;
use Walley\Upsell\Model\Products\GetUpsellProductsByOrder;

class GetUpsell implements GetUpsellInterface
{
    private OrderRepositoryInterface $orderRepository;
    private GetUpsellProductsByOrder $getUpsellProductsByOrder;
    private ProductToUpsellProduct $productToUpsellProduct;
    private UpsellInterfaceFactory $upsellInterfaceFactory;
    private UpsellConfigInterfaceFactory $upsellConfigInterfaceFactory;
    private Config\ConfigProvider $configProvider;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        GetUpsellProductsByOrder $getUpsellProductsByOrder,
        ProductToUpsellProduct $productToUpsellProduct,
        ConfigProvider $configProvider,
        UpsellConfigInterfaceFactory $upsellConfigInterfaceFactory,
        UpsellInterfaceFactory $upsellInterfaceFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->getUpsellProductsByOrder = $getUpsellProductsByOrder;
        $this->productToUpsellProduct = $productToUpsellProduct;
        $this->upsellInterfaceFactory = $upsellInterfaceFactory;
        $this->upsellConfigInterfaceFactory = $upsellConfigInterfaceFactory;
        $this->configProvider = $configProvider;
    }

    public function execute(int $orderId): UpsellInterface
    {
        $order = $this->orderRepository->get($orderId);
        $upsellProducts = $this->getUpsellProducts($order);

        /** @var UpsellInterface $upsell */
        $upsell = $this->upsellInterfaceFactory->create();
        $upsell->setUpsellProducts($upsellProducts)
            ->setConfig($this->getConfig());

        return $upsell;
    }

    public function upsellToArray(\Walley\Upsell\Api\Data\UpsellInterface $upsell):array
    {
        $configAsArray = $upsell->getConfig()->toArray();
        $upsellProductAsArray = [];
        $upsellProducts = $upsell->getUpsellProducts();
        foreach ($upsellProducts as $upsellProduct) {
            $upsellProductAsArray[] = $upsellProduct->toArray();
        }
        return [
            'config' => $configAsArray,
            'products' => $upsellProductAsArray
        ];
    }

    /**
     * @param OrderInterface $order
     * @return \Walley\Upsell\Api\Data\UpsellProductInterface[]
     */
    private function getUpsellProducts(OrderInterface $order):array
    {
        $products = $this->getUpsellProductsByOrder->execute($order);
        $upsell = [];
        foreach ($products as $product) {
            $upsell[] = $this->productToUpsellProduct->execute($product);
        }

        return $upsell;
    }

    private function getConfig():UpsellConfigInterface
    {
        /** @var \Walley\Upsell\Api\Data\UpsellConfigInterface $upsellConfig */
        $upsellConfig = $this->upsellConfigInterfaceFactory->create();
        $upsellConfig->setHeader($this->configProvider->getHeader())
            ->setCategoryId($this->configProvider->getCategoryId())
            ->setCountdownSeconds($this->configProvider->getCountdownSeconds())
            ->setIsActive($this->configProvider->getIsActive())
            ->setRecommendationType($this->configProvider->getRecommendationType())
            ->setLimitOfProducts($this->configProvider->getLimitOfProducts())
            ->setTimerText($this->configProvider->getTimerText());

        return $upsellConfig;
    }
}
