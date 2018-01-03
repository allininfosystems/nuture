<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Megamenu
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Block\Adminhtml\Transaction\TabContent\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Tab GeneralTab
 */
class AddNew extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected  $_storeManager;

    /**
     * GeneralTab constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_storeManager= $context->getStoreManager();
        $this->_systemStore =$systemStore;
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Transaction information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Transaction information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {

        return parent::_prepareForm();
    }


}
