<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Map;

use Magenest\ZohocrmIntegration\Controller\Adminhtml\Map as MapController;
use Magento\Framework\Controller\ResultFactory;
use Magenest\ZohocrmIntegration\Model\ResourceModel\Map\CollectionFactory as MapCollectionFactory;
use Magenest\ZohocrmIntegration\Model\MapFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Class Edit
 *
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Map
 */
class Edit extends MapController
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param MapFactory $mapFactory
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param MapCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MapFactory $mapFactory,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        MapCollectionFactory $collectionFactory
    ) {
        $this->coreRegistry         = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $mapFactory, $collectionFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_ZohocrmIntegration::zohocrm_integration');
        $id    = $this->getRequest()->getParam('id');
        $model = $this->_mapFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This mapping no longer exists.'));
                /*
                    * \Magento\Backend\Model\View\Result\Redirect $resultRedirect
                */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('zoho_mapping', $model);
        /*
            * @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Mapping') : __('New Mapping'));

        $resultPage->addContent($resultPage->getLayout()->createBlock('Magenest\ZohocrmIntegration\Block\Adminhtml\Map\Edit'));
        return $resultPage;
    }
}
