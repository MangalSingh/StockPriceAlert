<?php
namespace Octocub\StockPriceAlert\Controller\Subscribe;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Octocub\StockPriceAlert\Model\Config;
use Octocub\StockPriceAlert\Model\SubscriptionFactory;
use Octocub\StockPriceAlert\Model\ResourceModel\Subscription as SubscriptionResource;

class Index extends Action
{
    public function __construct(
        Context $context,
        private JsonFactory $jsonFactory,
        private StoreManagerInterface $storeManager,
        private CustomerSession $customerSession,
        private RemoteAddress $remoteAddress,
        private Config $config,
        private SubscriptionFactory $subscriptionFactory,
        private SubscriptionResource $subscriptionResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        $store = $this->storeManager->getStore();
        $storeId = (int)$store->getId();

        if (!$this->config->isEnabled($storeId)) {
            return $result->setData(['success' => false, 'message' => 'Module disabled']);
        }

        $productId = (int)$this->getRequest()->getParam('product_id');
        $alertType = (string)$this->getRequest()->getParam('alert_type'); // stock|price
        $email = trim((string)$this->getRequest()->getParam('email'));
        $targetPrice = $this->getRequest()->getParam('target_price');

        $consent = (int)$this->getRequest()->getParam('consent');
        $ua = substr((string)$this->getRequest()->getServer('HTTP_USER_AGENT'), 0, 255);
        $ip = (string)$this->remoteAddress->getRemoteAddress();

        if (!$productId || !in_array($alertType, ['stock','price'], true)) {
            return $result->setData(['success' => false, 'message' => 'Invalid request']);
        }

        $isLoggedIn = $this->customerSession->isLoggedIn();

        if (!$isLoggedIn) {
            if (!$this->config->allowGuest($storeId)) {
                return $result->setData(['success' => false, 'message' => 'Guest subscribe disabled']);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $result->setData(['success' => false, 'message' => 'Valid email required']);
            }
        } else {
            $email = (string)$this->customerSession->getCustomer()->getEmail();
        }

        if ($this->config->consentRequired($storeId) && $consent !== 1) {
            return $result->setData(['success' => false, 'message' => 'Consent is required']);
        }

        // Prevent duplicates
        $conn = $this->subscriptionResource->getConnection();
        $table = $this->subscriptionResource->getMainTable();

        $exists = (int)$conn->fetchOne(
            $conn->select()
                ->from($table, ['subscription_id'])
                ->where('product_id = ?', $productId)
                ->where('alert_type = ?', $alertType)
                ->where('email = ?', $email)
                ->where('status IN (?)', ['pending','confirmed'])
                ->limit(1)
        );

        if ($exists) {
            return $result->setData(['success' => true, 'message' => 'Already subscribed']);
        }

        $subscription = $this->subscriptionFactory->create();
        $subscription->setData([
            'product_id' => $productId,
            'customer_id' => $isLoggedIn ? (int)$this->customerSession->getCustomerId() : null,
            'email' => $email,
            'alert_type' => $alertType,
            'website_id' => (int)$store->getWebsiteId(),
            'store_id' => $storeId,
            'customer_group_id' => $isLoggedIn ? (int)$this->customerSession->getCustomerGroupId() : 0,
            'target_price' => ($alertType === 'price' && $targetPrice !== null) ? (float)$targetPrice : null,
            'status' => 'confirmed',
            'consent_accepted' => $consent ? 1 : 0,
            'consent_text_snapshot' => $consent ? $this->config->consentText($storeId) : null,
            'consent_at' => $consent ? date('Y-m-d H:i:s') : null,
            'consent_ip' => $consent ? $ip : null,
            'consent_user_agent' => $consent ? $ua : null,
        ]);

        $this->subscriptionResource->save($subscription);

        return $result->setData(['success' => true, 'message' => 'Subscription saved']);
    }
}
