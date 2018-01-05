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


namespace Mirasvit\Rewards\Block\Product;

class Message extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Mirasvit\Rewards\Helper\Message $messageHelper,
        \Mirasvit\Rewards\Helper\Balance\Earn $earnHelper,
        \Mirasvit\Rewards\Helper\Output\Earn $earnOutput,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->messageHelper   = $messageHelper;
        $this->earnHelper      = $earnHelper;
        $this->earnOutput      = $earnOutput;
        $this->registry        = $registry;
        $this->customerSession = $customerSession;
        $this->context         = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('product');
    }


    /**
     * @return string
     */
    public function getMessage()
    {
        $this->earnOutput->getProductPoints($this->getProduct()); //to collect messages
        $html = '';
        $messages = $this->earnHelper->getProductMessages($this->getProduct()->getId());
        foreach ($messages as $message) {
            $html .= $this->messageHelper->processNotificationVariables($message);
        }

        return $html;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}
