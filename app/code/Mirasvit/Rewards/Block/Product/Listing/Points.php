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



namespace Mirasvit\Rewards\Block\Product\Listing;

class Points extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Earn
     */
    protected $earnOutput;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Helper\Output\Earn            $earnOutput
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Output\Earn $earnOutput,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->earnOutput = $earnOutput;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getProductPoints()
    {
        return $this->earnOutput->getProductPoints($this->getProduct());
    }
}
