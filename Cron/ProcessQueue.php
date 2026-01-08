<?php
namespace Octocub\StockPriceAlert\Cron;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Octocub\StockPriceAlert\Model\Config;
use Octocub\StockPriceAlert\Model\StockResolver;
use Octocub\StockPriceAlert\Model\Email\Sender;
use Octocub\StockPriceAlert\Model\ResourceModel\Subscription\CollectionFactory as SubCollectionFactory;
use Octocub\StockPriceAlert\Model\ResourceModel\Subscription as SubResource;

class ProcessQueue
{
    public function __construct(
        private Config $config,
        private StoreManagerInterface $storeManager,
        private ProductRepositoryInterface $productRepository,
        private StockResolver $stockResolver,
        private Sender $sender,
        private SubCollectionFactory $subCollectionFactory,
        private SubResource $subResource
    ) {}

    public function execute()
    {
        $store = $this->storeManager->getStore();
        $storeId = (int)$store->getId();
        if (!$this->config->isEnabled($storeId)) {
            return;
        }

        if ($this->config->stockEnabled($storeId)) {
            $this->processStockAlerts($storeId, (int)$store->getWebsiteId());
        }

        if ($this->config->priceEnabled($storeId)) {
            $this->processPriceAlerts($storeId);
        }
    }

    private function processStockAlerts(int $storeId, int $websiteId): void
    {
        $collection = $this->subCollectionFactory->create();
        $collection->addFieldToFilter('alert_type', 'stock')
            ->addFieldToFilter('status', 'confirmed')
            ->setOrder('created_at', 'ASC');

        $subsByProduct = [];
        foreach ($collection as $sub) {
            $subsByProduct[(int)$sub->getData('product_id')][] = $sub;
        }

        foreach ($subsByProduct as $productId => $subs) {
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (\Throwable $e) {
                continue;
            }

            $sku = (string)$product->getSku();
            if (!$sku) continue;

            $salableQty = $this->stockResolver->getSalableQty($sku, $websiteId);
            if ($salableQty <= 0) continue;

            $mode = $this->config->notifyMode($storeId);
            if ($mode === 'unlimited') {
                $limit = count($subs);
            } elseif ($mode === 'fixed') {
                $limit = max(0, $this->config->fixedLimit($storeId));
            } else {
                $limit = (int)floor($salableQty);
                if ($limit < 1) continue;
            }

            $sendSubs = array_slice($subs, 0, $limit);

            foreach ($sendSubs as $sub) {
                $email = (string)$sub->getData('email');
                $groupId = (int)$sub->getData('customer_group_id');

                $vars = [
                    'product_name' => $product->getName(),
                    'product_url'  => $product->getProductUrl(),
                    'sku'          => $sku,
                    'store_name'   => $this->storeManager->getStore($storeId)->getName(),
                ];

                try {
                    $this->sender->send('stock', $email, $vars, $groupId, $storeId);
                    $sub->setData('status', 'notified');
                    $this->subResource->save($sub);
                } catch (\Throwable $e) {
                    // keep for retry
                }
            }
        }
    }

    private function processPriceAlerts(int $storeId): void
    {
        $collection = $this->subCollectionFactory->create();
        $collection->addFieldToFilter('alert_type', 'price')
            ->addFieldToFilter('status', 'confirmed')
            ->setOrder('created_at', 'ASC');

        foreach ($collection as $sub) {
            $productId = (int)$sub->getData('product_id');
            $target = $sub->getData('target_price');
            $target = $target !== null ? (float)$target : null;

            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (\Throwable $e) {
                continue;
            }

            $finalPrice = (float)$product->getFinalPrice();
            if ($finalPrice <= 0) continue;

            $shouldSend = false;
            if ($target === null) {
                $regular = (float)$product->getPrice();
                if ($regular > 0 && $finalPrice < $regular) $shouldSend = true;
            } else {
                if ($finalPrice <= $target) $shouldSend = true;
            }

            if (!$shouldSend) continue;

            $email = (string)$sub->getData('email');
            $groupId = (int)$sub->getData('customer_group_id');

            $vars = [
                'product_name' => $product->getName(),
                'product_url'  => $product->getProductUrl(),
                'sku'          => (string)$product->getSku(),
                'final_price'  => $finalPrice,
                'store_name'   => $this->storeManager->getStore($storeId)->getName(),
            ];

            try {
                $this->sender->send('price', $email, $vars, $groupId, $storeId);
                $sub->setData('status', 'notified');
                $this->subResource->save($sub);
            } catch (\Throwable $e) {
                // keep for retry
            }
        }
    }
}
