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
namespace Magestore\Rewardpoints\Controller\Adminhtml\Widget;

class ChooserCustomer extends \Magento\Backend\App\Action
{
    /**
     * Prepare block for chooser
     *
     * @return void
     */
    public function execute()
    {

        $block = $this->_view->getLayout()->createBlock(
            'Magestore\Rewardpoints\Block\Adminhtml\Transaction\Widget\ChooserCustomer',
            'rewardpoints_widget_chooser_customer',
            ['data' => ['js_form_object' => $this->getRequest()->getParam('form')]]
        );
        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }
}
