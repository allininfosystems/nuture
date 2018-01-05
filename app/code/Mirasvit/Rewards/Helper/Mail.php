<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rewards\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\State as AppState;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $emailTemplateFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @param AppState                                           $appState
     * @param \Magento\Email\Model\TemplateFactory               $emailTemplateFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder  $transportBuilder
     * @param \Mirasvit\Rewards\Model\Config                     $config
     * @param \Mirasvit\Rewards\Helper\Data                      $rewardsData
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\View\Asset\Repository           $assetRepo
     * @param \Magento\Framework\Filesystem                      $filesystem
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\ResourceConnection          $resource
     * @param \Magento\Framework\App\Helper\Context              $context
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        AppState $appState,
        \Magento\Email\Model\TemplateFactory $emailTemplateFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->appState = $appState;
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
        $this->rewardsData = $rewardsData;
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        $this->filesystem = $filesystem;
        $this->inlineTranslation = $inlineTranslation;
        $this->context = $context;
        $this->resource = $resource;
        parent::__construct($context);
    }

    /**
     * @var array
     */
    public $emails = [];

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    protected function getSender()
    {
        return 'general';
    }

    /**
     * @param string $templateName
     * @param string $senderName
     * @param string $senderEmail
     * @param string $recipientEmail
     * @param string $recipientName
     * @param array  $variables
     * @param int    $storeId
     * @return bool|void
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function send(
        $templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId
    ) {
        // during setup simulate sending
        if ($this->appState->getAreaCode() == 'setup') {
            return true;
        }
        if (!$senderEmail) {
            return false;
        }

        $this->inlineTranslation->suspend();
        $this->transportBuilder
            ->setTemplateIdentifier($templateName)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId,
                ]
            )
            ->setTemplateVars($variables);

        $this->transportBuilder
            ->setFrom(
                [
                    'name' => $senderName,
                    'email' => $senderEmail,
                ]
            )
            ->addTo($recipientEmail, $recipientName)
            ->setReplyTo($senderEmail);
        $transport = $this->transportBuilder->getTransport();

        try {
            /* @var \Magento\Framework\Mail\Transport $transport */
            $transport->sendMessage();
        } catch (\Exception $e) {

        }

        $this->inlineTranslation->resume();

        return true;
    }

    /**
     * @param \Mirasvit\Rewards\Model\Transaction $transaction
     * @param bool|false                          $emailMessage
     * @return bool
     */
    public function sendNotificationBalanceUpdateEmail($transaction, $emailMessage = false)
    {
        if ($emailMessage) {
            $emailMessage = $this->parseVariables($emailMessage, $transaction);
        }

        $customer     = $transaction->getCustomer();
        $store        = $customer ? $customer->getData('store_id') : null;
        $templateName = $this->getConfig()->getNotificationBalanceUpdateEmailTemplate($store);
        if ($templateName == 'none' || !$customer || !$this->isCustomerSubscribed($customer)) {
            return false;
        }
        $storeId        = $customer->getStore()->getId();
        $recipientName  = $customer->getName();
        $recipientEmail = $customer->getEmail();
        $this->rewardsData->setCurrentStore($customer->getStore());
        $variables = [
            'customer'              => $customer,
            'store'                 => $customer->getStore(),
            'transaction'           => $transaction,
            'transaction_days_left' => $transaction->getDaysLeft(),
            'transaction_amount'    => $this->rewardsData->formatPoints($transaction->getAmount()),
            'transaction_comment'   => $transaction->getComment(),
            'balance_total'         => $this->rewardsData->formatPoints($this->getBalancePoints($customer)),
            'message'               => $this->rewardsData->convertToHtml($emailMessage),
            'no_message'            => $emailMessage == false || $emailMessage == '',
        ];

        $senderName = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/name");
        $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/email");
        $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
    }

    /**
     * @param \Mirasvit\Rewards\Model\Transaction $transaction
     * @return bool
     */
    public function sendNotificationPointsExpireEmail($transaction)
    {
        $customer     = $transaction->getCustomer();
        $store        = $customer ? $customer->getData('store_id') : null;
        $templateName = $this->getConfig()->getNotificationPointsExpireEmailTemplate($store);
        if ($templateName == 'none' || !$this->isCustomerSubscribed($customer)) {
            return false;
        }
        $storeId        = $customer->getStore()->getId();
        $recipientName  = $customer->getName();
        $recipientEmail = $customer->getEmail();
        $variables = [
            'customer'              => $customer,
            'store'                 => $customer->getStore(),
            'transaction'           => $transaction,
            'transaction_days_left' => $transaction->getDaysLeft(),
            'transaction_amount'    => $this->rewardsData->formatPoints($transaction->getAmount()),
        ];
        $senderName  = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/name");
        $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/email");
        $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
    }

    /**
     * @param  \Mirasvit\Rewards\Model\Referral $referral
     * @param string                            $message
     * @return bool
     */
    public function sendReferralInvitationEmail($referral, $message)
    {
        $store = $referral ? $referral->getData('store_id') : null;
        $templateName = $this->getConfig()->getReferralInvitationEmailTemplate($store);
        if ($templateName == 'none') {
            return false;
        }
        $recipientEmail = $referral->getEmail();
        $recipientName  = $referral->getName();
        $storeId  = $referral->getStoreId();
        $customer = $referral->getCustomer();
        $variables = [
            'customer'       => $customer,
            'name'           => $referral->getName(),
            'message'        => $message,
            'invitation_url' => $referral->getInvitationUrl(),
        ];
        $senderName = $this->context->getScopeConfig()->getValue(
            "trans_email/ident_{$this->getSender()}/name",
            ScopeInterface::SCOPE_STORE
        );
        $senderEmail = $this->context->getScopeConfig()->getValue(
            "trans_email/ident_{$this->getSender()}/email",
            ScopeInterface::SCOPE_STORE
        );

        $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
    }

    /**
     * Can parse template and return ready text.
     *
     * @param string $variable  Text with variables like {{var customer.name}}.
     * @param array  $variables Array of variables.
     * @param int    $storeId
     *
     * @return string - ready text
     */
    public function processVariable($variable, $variables, $storeId)
    {
        $template = $this->emailTemplateFactory->create();
        $template->setDesignConfig([
            'area'  => 'frontend',
            'store' => $storeId,
        ]);
        $template->setTemplateText($variable);
        $html = $template->getProcessedTemplate($variables);

        return $html;
    }

    /**
     * @param string                              $text
     * @param \Mirasvit\Rewards\Model\Transaction $transaction
     * @return string
     */
    public function parseVariables($text, $transaction)
    {
        $customer = $transaction->getCustomer();
        $variables = [
            'customer' => $customer,
            'store' => $customer->getStore(),
            'transaction' => $transaction,
            'transaction_days_left' => $transaction->getDaysLeft(),
            'transaction_amount' => $this->rewardsData->formatPoints($transaction->getAmount()),
            'balance_total' => $this->rewardsData->formatPoints($this->getBalancePoints($customer)),
        ];
        $text = $this->processVariable($text, $variables, $customer->getStore()->getId());

        return $text;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return bool
     */
    public function isCustomerSubscribed($customer)
    {
        // rewards_subscription = 0 - customer set manually email cancellation. Default true.
        return $customer->getData('rewards_subscription') !== 0 && $customer->getData('rewards_subscription') !== '0';
    }

    /**
     * This is a dublicate of function Balance::getBalancePoints
     * we created it because of circular dependency problem
     * need to find a more elegant solution
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return int
     */
    private function getBalancePoints($customer)
    {
        if (is_object($customer)) {
            $customer = $customer->getId();
        }
        $resource = $this->resource;
        $table = $resource->getTableName('mst_rewards_transaction');

        return (int)$resource->getConnection()->fetchOne(
            "SELECT SUM(amount) FROM $table WHERE customer_id=?", [(int)$customer]
        );
    }

}
