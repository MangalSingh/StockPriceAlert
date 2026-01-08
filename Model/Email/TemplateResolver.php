<?php
namespace Octocub\StockPriceAlert\Model\Email;

use Magento\Framework\Serialize\Serializer\Json;
use Octocub\StockPriceAlert\Model\Config;

class TemplateResolver
{
    public function __construct(
        private Config $config,
        private Json $serializer
    ) {}

    public function resolve(string $type, int $customerGroupId, int $storeId): string
    {
        if ($type === 'stock') {
            $mapRaw = $this->config->stockTemplateByGroupRaw($storeId);
            $default = $this->config->stockTemplateDefault($storeId);
        } else {
            $mapRaw = $this->config->priceTemplateByGroupRaw($storeId);
            $default = $this->config->priceTemplateDefault($storeId);
        }

        $map = [];
        try {
            $map = $mapRaw ? (array)$this->serializer->unserialize($mapRaw) : [];
        } catch (\Throwable $e) {
            $map = [];
        }

        return $map[(string)$customerGroupId] ?? $default;
    }
}
