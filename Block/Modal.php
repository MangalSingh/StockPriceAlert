<?php
namespace Octocub\StockPriceAlert\Block;

use Magento\Framework\View\Element\Template;
use Octocub\StockPriceAlert\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

class Modal extends Template
{
    public function __construct(
        Template\Context $context,
        private Config $config,
        private StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getConsentRequired(): bool
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        return $this->config->consentRequired($storeId);
    }

    public function getConsentText(): string
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        return $this->config->consentText($storeId);
    }
}
