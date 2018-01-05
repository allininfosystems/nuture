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
use Abraham\TwitterOAuth\TwitterOAuth;

class PostTweet extends \Mirasvit\Rewards\Controller\Twitter
{
    /**
     * @return string
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $message = $this->getRequest()->getParam('message');
        $url = $this->getRequest()->getParam('url');
        $data = $this->customerSession->getData('oauth_token');
        $resultJson = $this->resultJsonFactory->create();
        if (!is_array($data) || empty($data['oauth_token'])) {
            return $resultJson->setData(['errors' => __('Error. PLease reload page and login twitter')]);
        }

        $connection = new TwitterOAuth(
            $this->getConfig()->getTwitterConsumerKey(), $this->getConfig()->getTwitterConsumerSecret()
        );
        $connection->setOauthToken($data['oauth_token'], $data['oauth_token_secret']);
        $data = $connection->post("statuses/update", ["status" => $message]);

        if (!isset($data->errors) && isset($data->id)) {
            $transaction = $this->rewardsBehavior->processRule(
                \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_TWITTER_TWEET,
                $this->_getCustomer(),
                false,
                $url
            );
            $text = '';
            if ($transaction) {
                $text = __("You've earned %1 for Tweet!", $this->rewardsData->formatPoints($transaction->getAmount()));
            }

            $response = $resultJson->setData(['message' => $text]);
        } else {
            $errors = [];
            foreach ($data->errors as $error) {
                $errors[] = $error->message;
            }

            $response = $resultJson->setData(['errors' => $errors]);
        }

        return $response;
    }
}
