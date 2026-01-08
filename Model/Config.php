<?php
namespace Octocub\StockPriceAlert\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XML_PATH_ENABLED = 'octocub_spa/general/enabled';
    public const XML_PATH_ENABLE_STOCK = 'octocub_spa/general/enable_stock';
    public const XML_PATH_ENABLE_PRICE = 'octocub_spa/general/enable_price';
    public const XML_PATH_ALLOW_GUEST = 'octocub_spa/general/allow_guest';
    public const XML_PATH_DOUBLE_OPTIN = 'octocub_spa/general/double_opt_in';

    public const XML_PATH_CONSENT_REQUIRED = 'octocub_spa/consent/consent_required';
    public const XML_PATH_CONSENT_TEXT = 'octocub_spa/consent/consent_text';

    public const XML_PATH_NOTIFY_MODE = 'octocub_spa/send/notify_mode';
    public const XML_PATH_FIXED_LIMIT = 'octocub_spa/send/fixed_limit';
    public const XML_PATH_EMAIL_SENDER = 'octocub_spa/send/email_sender';

    public const XML_PATH_STOCK_TEMPLATE_DEFAULT = 'octocub_spa/templates/stock_template_default';
    public const XML_PATH_PRICE_TEMPLATE_DEFAULT = 'octocub_spa/templates/price_template_default';
    public const XML_PATH_STOCK_TEMPLATE_BY_GROUP = 'octocub_spa/templates/stock_template_by_group';
    public const XML_PATH_PRICE_TEMPLATE_BY_GROUP = 'octocub_spa/templates/price_template_by_group';

    public function __construct(private ScopeConfigInterface $scopeConfig) {}

    public function isEnabled(?int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function stockEnabled(?int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_STOCK, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function priceEnabled(?int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_PRICE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function allowGuest(?int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ALLOW_GUEST, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function consentRequired(?int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_CONSENT_REQUIRED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function consentText(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_CONSENT_TEXT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function notifyMode(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_NOTIFY_MODE, ScopeInterface::SCOPE_STORE, $storeId) ?: 'match_qty';
    }

    public function fixedLimit(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_FIXED_LIMIT, ScopeInterface::SCOPE_STORE, $storeId) ?: 50;
    }

    public function emailSender(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId) ?: 'general';
    }

    public function stockTemplateDefault(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_STOCK_TEMPLATE_DEFAULT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function priceTemplateDefault(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_PRICE_TEMPLATE_DEFAULT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function stockTemplateByGroupRaw(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_STOCK_TEMPLATE_BY_GROUP, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function priceTemplateByGroupRaw(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_PRICE_TEMPLATE_BY_GROUP, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
