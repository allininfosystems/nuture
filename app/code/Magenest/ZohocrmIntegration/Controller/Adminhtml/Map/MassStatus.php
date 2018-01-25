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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Registry;
use Magenest\ZohocrmIntegration\Controller\Adminhtml\Map as MapController;
use Magenest\ZohocrmIntegration\Model\MapFactory;
use Magenest\ZohocrmIntegration\Model\ResourceModel\Map\CollectionFactory as MapCollectionFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassStatus Mapping
 *
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Map
 */
class MassStatus extends MapController
{
    /**
     * Mass Action Filter
     *
     * @var Filter
     */
    protected $_filter;

    /**
     * Collection Factory
     *
     * @var MapCollectionFactory
     */
    protected $_collectionFactory;


    /**
     * @param Context                                    $context
     * @param Registry                                   $coreRegistry
     * @param PageFactory                                $resultPageFactory
     * @param MapFactory                                 $mapFactory
     * @param Filter                                     $filter
     * @param MapCollectionFactory                       $collectionFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        MapFactory  $mapFactory,
        MapCollectionFactory $collectionFactory,
        Filter $filter
    ) {
        $this->_filter            = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $mapFactory, $collectionFactory);
    }

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $status     = (int) $this->getRequest()->getParam('status');
        $totals     = 0;
        try {
            foreach ($collection as $item) {
                /** @var \Magenest\ZohocrmIntegration\Model\Map $item */
                $item->setStatus($status)->save();
                $totals++;
            }

            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $totals));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the mapping(s) status.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
