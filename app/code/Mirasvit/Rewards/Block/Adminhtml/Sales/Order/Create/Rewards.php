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



namespace Mirasvit\Rewards\Block\Adminhtml\Sales\Order\Create;

class Rewards extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Mirasvit\Rewards\Helper\Checkout $rewardsCheckout,
        \Magento\Sales\Model\AdminOrder\Create $salesOrderCreate,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->salesOrderCreate = $salesOrderCreate;
        $this->rewardsCheckout  = $rewardsCheckout;

        $this->rewardsCheckout->processAdminRequest($this->getOrderQuote());
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    protected function getOrderQuote()
    {
        return $this->salesOrderCreate->getQuote();
    }
}
