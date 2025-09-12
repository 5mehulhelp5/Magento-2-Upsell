<?php
declare(strict_types=1);

namespace Walley\Upsell\Controller\Test;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Store\Model\StoreManagerInterface;
use Walley\Upsell\Model\WalleyOrder\GetProductVatRate;

class Product implements ActionInterface
{
    private RequestInterface $request;
    private RawFactory $resultRawFactory;
    private ProductRepositoryInterface $productRepository;
    private StoreManagerInterface $storeManager;
    private GetProductVatRate $getProductVatRate;

    public function __construct(
        RequestInterface $request,
        RawFactory $resultRawFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        GetProductVatRate $getProductVatRate
    ) {
        $this->request = $request;
        $this->resultRawFactory = $resultRawFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->getProductVatRate = $getProductVatRate;
    }

    public function execute()
    {
        $raw = $this->resultRawFactory->create();
        $productId = (int) $this->request->getParam('productId');

        if ($productId <= 0) {
            return $raw->setHttpResponseCode(400)->setContents('Missing or invalid productId');
        }

        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
            $product = $this->productRepository->getById($productId, false, $storeId, true);

            $rate = $this->getProductVatRate->execute($product, $storeId); // returns percent
            // print with 2 decimals
            return $raw->setHeader('Content-Type', 'text/plain', true)
                ->setContents(number_format($rate, 2, '.', ''));
        } catch (\Throwable $e) {
            return $raw->setHttpResponseCode(500)->setContents('Error: ' . $e->getMessage());
        }
    }
}
