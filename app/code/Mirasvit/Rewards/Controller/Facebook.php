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

use Magento\Framework\App\Action\Action;

abstract class Facebook extends Action
{
    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    protected $rewardsSocialBalance;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

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
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Mirasvit\Rewards\Helper\Behavior                $rewardsBehavior
     * @param \Mirasvit\Rewards\Helper\Balance                 $rewardsSocialBalance
     * @param \Mirasvit\Rewards\Helper\Data                    $rewardsData
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Framework\App\Action\Context            $context
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior,
        \Mirasvit\Rewards\Helper\Balance $rewardsSocialBalance,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->rewardsBehavior = $rewardsBehavior;
        $this->rewardsSocialBalance = $rewardsSocialBalance;
        $this->rewardsData = $rewardsData;
        $this->customerFactory = $customerFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function _getCustomer()
    {
        return $this->customerFactory->create()->load($this->customerSession->getCustomerId());
    }
}
