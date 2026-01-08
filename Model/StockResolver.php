<?php
namespace Octocub\StockPriceAlert\Model;

use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;

class StockResolver
{
    public function __construct(
        private StockResolverInterface $stockResolver,
        private GetProductSalableQtyInterface $getProductSalableQty,
        private IsProductSalableInterface $isProductSalable
    ) {}

    public function getStockIdByWebsite(int $websiteId): int
    {
        $stock = $this->stockResolver->execute('website', (string)$websiteId);
        return (int)$stock->getStockId();
    }

    public function getSalableQty(string $sku, int $websiteId): float
    {
        $stockId = $this->getStockIdByWebsite($websiteId);
        return (float)$this->getProductSalableQty->execute($sku, $stockId);
    }

    public function isSalable(string $sku, int $websiteId): bool
    {
        $stockId = $this->getStockIdByWebsite($websiteId);
        return (bool)$this->isProductSalable->execute($sku, $stockId);
    }
}
