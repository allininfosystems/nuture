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



namespace Mirasvit\Rewards\Block\Buttons\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->context = $context;
        $this->urlBuilder = $context->getUrlBuilder();

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->urlBuilder->getCurrentUrl();
    }

    /**
     * @return string
     */
    public function getTweetUrl()
    {
        return $this->urlBuilder->getUrl('rewards/twitter/posttweet');
    }

    /**
     * @return string
     */
    public function getDefaultTweet()
    {
        return __('Check this out! %1', $this->urlBuilder->getCurrentUrl());
    }
}
