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



namespace Mirasvit\Rewards\Controller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

abstract class Account extends Action
{
    /**
     * @var \Mirasvit\Rewards\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @param \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Framework\App\Action\Context      $context
     */
    public function __construct(
        \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $action = $this->getRequest()->getActionName();
        if ($action != 'external' && $action != 'postexternal') {
            $url = $this->_url->getUrl(\Magento\Customer\Model\Url::ROUTE_ACCOUNT_LOGIN);
            if (!$this->customerSession->authenticate($url)) {
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            }
        }

        return parent::dispatch($request);
    }

    /**
     * @return \Mirasvit\Rewards\Model\Transaction
     */
    protected function _initTransaction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $transaction = $this->transactionFactory->create()->load($id);
            if ($transaction->getId() > 0) {
                $this->registry->register('current_transaction', $transaction);

                return $transaction;
            }
        }
    }

    /************************/
}
