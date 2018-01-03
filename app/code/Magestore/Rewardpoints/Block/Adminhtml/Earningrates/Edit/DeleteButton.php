<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Block\Adminhtml\Earningrates\Edit;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context, $registry);
        $this->_request = $context->getRequest();
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if($this->_request->getParam('id')){
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'id' => 'earning-delete-button',
                'data_attribute'=> [
                    'url' => $this->getDeleteUrl(),
                ],
                'on_click' =>'',
                'sort_order' => 20,
            ];
            return $data;
        }
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->_request->getParam('id')]);
    }
}
