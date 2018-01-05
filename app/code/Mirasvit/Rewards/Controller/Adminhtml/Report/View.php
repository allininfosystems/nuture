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


namespace Mirasvit\Rewards\Controller\Adminhtml\Report;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;

class View extends Action
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;
    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        Registry $registry,
        Context $context
    ) {
        $this->reportRepository = $reportRepository;
        $this->registry         = $registry;
        $this->context          = $context;
        $this->backendSession   = $context->getSession();

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_Rewards::reward_points_report');
        $resultPage->getConfig()->getTitle()->prepend(__('Rewards'));
        $resultPage->getConfig()->getTitle()->prepend(__('Reports'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $report = $this->reportRepository->get('rewards_overview');
            $this->registry->register('current_report', $report);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('You have not any statistic. Please refresh statistic.'));
            $this->_redirect('*/earning_rule/');

            return;
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->initPage($resultPage)
            ->getConfig()->getTitle()->prepend($report->getName());

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Rewards::reward_points_report');
    }
}