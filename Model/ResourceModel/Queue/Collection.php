<?php
namespace Octocub\StockPriceAlert\Model\ResourceModel\Queue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Octocub\StockPriceAlert\Model\Queue::class,
            \Octocub\StockPriceAlert\Model\ResourceModel\Queue::class
        );
    }
}
