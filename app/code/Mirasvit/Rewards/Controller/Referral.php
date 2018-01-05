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
use Magento\Customer\Model\Session;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Referral extends Action
{
    /**
     * @var \Mirasvit\Rewards\Model\ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\ReferralLink\CollectionFactory
     */
    protected $referralLinkCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Customer
     */
    protected $customer;

    /**
     * @var \Mirasvit\Rewards\Helper\Referral
     */
    protected $rewardsReferral;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

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
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @param \Mirasvit\Rewards\Model\ReferralFactory                              $referralFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\ReferralLink\CollectionFactory $referralLinkCollectionFactory
     * @param \Mirasvit\Rewards\Model\Customer                                     $customer
     * @param \Mirasvit\Rewards\Helper\Referral                                    $rewardsReferral
     * @param \Magento\Store\Model\StoreManagerInterface                           $storeManager
     * @param \Magento\Framework\Registry                                          $registry
     * @param \Magento\Customer\Model\Session                                      $customerSession
     * @param \Magento\Framework\Session\SessionManagerInterface                   $session
     * @param \Magento\Framework\App\Action\Context                                $context
     */
    public function __construct(
        \Mirasvit\Rewards\Model\ReferralFactory $referralFactory,
        \Mirasvit\Rewards\Model\ResourceModel\ReferralLink\CollectionFactory $referralLinkCollectionFactory,
        \Mirasvit\Rewards\Model\Customer $customer,
        \Mirasvit\Rewards\Helper\Referral $rewardsReferral,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->referralFactory = $referralFactory;
        $this->referralLinkCollectionFactory = $referralLinkCollectionFactory;
        $this->customer = $customer;
        $this->rewardsReferral = $rewardsReferral;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->context = $context;
        $this->session = $session;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $action = $this->getRequest()->getActionName();
        if ($action != 'invite' && $action != 'referralVisit') {
            $url = $this->_url->getUrl(\Magento\Customer\Model\Url::ROUTE_ACCOUNT_LOGIN);
            if (!$this->customerSession->authenticate($url)) {
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            }
        }

        return parent::dispatch($request);
    }

    /**
     * @return \Mirasvit\Rewards\Model\Referral
     */
    protected function _initReferral()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $referral = $this->referralFactory->create()->load($id);
            if ($referral->getId() > 0) {
                $this->registry->register('current_referral', $referral);

                return $referral;
            }
        }
    }

    /************************/
}
