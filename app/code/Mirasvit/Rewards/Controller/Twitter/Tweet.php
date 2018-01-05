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



namespace Mirasvit\Rewards\Controller\Twitter;

use Magento\Framework\Controller\ResultFactory;

class Tweet extends \Mirasvit\Rewards\Controller\Twitter
{
    /**
     * @return $this|string
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $response = '';
        if (!$this->_getCustomer()) {
            return $response;
        }
        $url = $this->getRequest()->getParam('url');
        $result = $this->rewardsBehavior->addToQueueRule(
            \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_TWITTER_TWEET,
            $this->_getCustomer(),
            false,
            $url
        );

        if ($result) {
            $resultJson = $this->resultJsonFactory->create();

            $response = $resultJson->setJsonData(
                __("You'll get your%1 for Tweet shortly!", $this->rewardsData->formatPoints(''))
            );
        }

        return $response;
    }
}
