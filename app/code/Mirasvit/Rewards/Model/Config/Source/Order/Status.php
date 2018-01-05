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



namespace Mirasvit\Rewards\Model\Config\Source\Order;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory
     */
    protected $orderStatusCollectionFactory;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollectionFactory
     * @param \Magento\Framework\Model\Context                                  $context
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollectionFactory,
        \Magento\Framework\Model\Context $context
    ) {
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->context = $context;
    }

    /**
     * @var array
     */
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->orderStatusCollectionFactory->create()
                ->load()->toOptionArray();
        }

        return $this->_options;
    }
}
