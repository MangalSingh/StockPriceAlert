<?php
namespace Octocub\StockPriceAlert\Model\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Octocub\StockPriceAlert\Model\Config;

class Sender
{
    public function __construct(
        private TransportBuilder $transportBuilder,
        private Config $config,
        private TemplateResolver $templateResolver
    ) {}

    public function send(string $type, string $toEmail, array $vars, int $customerGroupId, int $storeId): void
    {
        $template = $this->templateResolver->resolve($type, $customerGroupId, $storeId);
        if (!$template) {
            return;
        }

        $sender = $this->config->emailSender($storeId);

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($template)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
            ->setTemplateVars($vars)
            ->setFromByScope($sender, $storeId)
            ->addTo($toEmail)
            ->getTransport();

        $transport->sendMessage();
    }
}
