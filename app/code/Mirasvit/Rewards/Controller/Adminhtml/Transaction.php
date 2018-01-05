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



namespace Mirasvit\Rewards\Controller\Adminhtml;

use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

abstract class Transaction extends \Magento\Backend\App\Action
{
    public function __construct(
        CookieManagerInterface $cookieManager,
        \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Magento\Framework\Registry $registry,
        FileFactory $fileFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->cookieManager = $cookieManager;
        $this->transactionFactory = $transactionFactory;
        $this->customerFactory    = $customerFactory;
        $this->rewardsBalance     = $rewardsBalance;
        $this->registry           = $registry;
        $this->fileFactory        = $fileFactory;
        $this->context            = $context;

        parent::__construct($context);

        $this->backendSession = $context->getSession();
        $this->resultFactory  = $context->getResultFactory();
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_Rewards::rewards');
        $resultPage->getConfig()->getTitle()->prepend(__('Reward Points'));
        $resultPage->getConfig()->getTitle()->prepend(__('Transactions'));

        return $resultPage;
    }

    /**
     * @return \Mirasvit\Rewards\Model\Transaction
     */
    public function _initTransaction()
    {
        $transaction = $this->transactionFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $transaction->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_transaction', $transaction);

        return $transaction;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Rewards::reward_points_transaction');
    }
}
