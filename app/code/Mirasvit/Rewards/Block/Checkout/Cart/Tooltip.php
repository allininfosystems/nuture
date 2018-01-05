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



namespace Mirasvit\Rewards\Block\Checkout\Cart;

use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

class Tooltip extends \Magento\Framework\View\Element\Messages
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $sessionFactory;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    protected $rewardsPurchase;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Magento\Customer\Model\Session                  $sessionFactory
     * @param \Mirasvit\Rewards\Helper\Purchase                $rewardsPurchase
     * @param \Mirasvit\Rewards\Helper\Data                    $rewardsData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Message\Factory               $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory     $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface      $messageManager
     * @param InterpretationStrategyInterface                  $interpretationStrategy
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $sessionFactory,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        array $data = []
    ) {
        $this->sessionFactory = $sessionFactory;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->rewardsData = $rewardsData;
        $this->context = $context;
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
    }

    /**
     * @return bool
     */
    public function hasGuestNote()
    {
        if ($this->sessionFactory->isLoggedIn() && $this->sessionFactory->getCustomer()->getId()) {
            return false;
        }

        return true;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $points = 0;
        if ($this->rewardsPurchase->getPurchase()) {
            $points = $this->rewardsPurchase->getPurchase()->getEarnPoints();
        }
        if ($points) {
            $this->messageManager->addSuccess(
                __('Checkout now and earn <b>%1</b> for this order.', $this->rewardsData->formatPoints($points))
            );
            if ($this->hasGuestNote()) {
                $this->messageManager->addSuccess(__('Applies only to registered customers, may vary when logged in.'));
            }

            $this->addMessages($this->messageManager->getMessages(true));
        }

        return parent::_prepareLayout();
    }
}
