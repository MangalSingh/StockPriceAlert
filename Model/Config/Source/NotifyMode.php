<?php
namespace Octocub\StockPriceAlert\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class NotifyMode implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'match_qty', 'label' => __('Match Salable Qty (FIFO)')],
            ['value' => 'fixed', 'label' => __('Fixed Limit per Run')],
            ['value' => 'unlimited', 'label' => __('Unlimited')],
        ];
    }
}
