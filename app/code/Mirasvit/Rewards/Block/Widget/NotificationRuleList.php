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


namespace Mirasvit\Rewards\Block\Widget;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Widget\Block\BlockInterface;

class NotificationRuleList extends \Magento\Framework\View\Element\Template implements BlockInterface, IdentityInterface
{
    public function __construct(
        \Mirasvit\Rewards\Model\ResourceModel\Notification\Rule\CollectionFactory $ruleCollectionFactory,
        \Mirasvit\Rewards\Helper\Rule\Notification $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Message $messageHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->rewardsPurchase       = $rewardsPurchase;
        $this->messageHelper         = $messageHelper;
        $this->httpContext           = $httpContext;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags'     => [\Mirasvit\Rewards\Model\Notification\Rule::CACHE_TAG,
        ], ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'REWARDS_NOTIFICATION_RULE_LIST_WIDGET',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            serialize($this->getRequest()->getParams())
        ];
    }

    /**
     * @var bool|array
     */
    protected $rules = false;

    /**
     * @return array|bool
     */
    public function getRules()
    {
        if (!$this->rules) {
            $this->rules = $this->rewardsPurchase->calcNotificationRulesWidget();
        }

        return $this->rules;
    }

    /**
     * @param \Mirasvit\Rewards\Model\Notification\Rule $rule
     * @return string
     */
    public function prepareRuleMessage($rule)
    {
        return $this->messageHelper->processNotificationVariables($rule->getMessage());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getRules()) {
            /** @var \Mirasvit\Rewards\Model\Notification\Rule $rule */
            foreach ($this->getRules() as $rule) {
                $identities = array_merge($identities, $rule->getIdentities());
            }
        }

        return $identities ?: [\Mirasvit\Rewards\Model\Notification\Rule::CACHE_TAG];
    }

    /**
     * Get value of widgets' title parameter
     *
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }
}
