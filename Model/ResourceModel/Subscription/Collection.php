<?php
namespace Octocub\StockPriceAlert\Model\ResourceModel\Subscription;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Octocub\StockPriceAlert\Model\Subscription::class,
            \Octocub\StockPriceAlert\Model\ResourceModel\Subscription::class
        );
    }
}
