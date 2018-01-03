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
 * @package   mirasvit/module-core
 * @version   1.2.21
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Observer;

use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Core\Model\LicenseFactory;
use Mirasvit\Core\Model\NotificationFeedFactory;

class OnActionPredispatchObserver implements ObserverInterface
{
    /**
     * @var NotificationFeedFactory
     */
    protected $feedFactory;

    /**
     * @param NotificationFeedFactory $feedFactory
     */
    public function __construct(
        NotificationFeedFactory $feedFactory
    ) {
        $this->feedFactory = $feedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(EventObserver $observer)
    {
        $feedModel = $this->feedFactory->create();
        $feedModel->checkUpdate();
    }
}