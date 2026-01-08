<?php
namespace Octocub\StockPriceAlert\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class EnqueueOnInventoryChange implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        // Intentionally minimal (cron-based processing covers reliability).
        return;
    }
}
