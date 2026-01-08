<?php
namespace Octocub\StockPriceAlert\Model;

use Magento\Framework\Model\AbstractModel;

class Subscription extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Octocub\StockPriceAlert\Model\ResourceModel\Subscription::class);
    }
}
