<?php
namespace Octocub\StockPriceAlert\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Subscription extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('octocub_spa_subscription', 'subscription_id');
    }
}
