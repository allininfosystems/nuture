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



namespace Mirasvit\Rewards\Model\ResourceModel\Earning\Rule;

class Queue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $resourcePrefix;

    /**
     * @param \Mirasvit\Rewards\Model\Config                    $config
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string                                            $resourcePrefix
     */
    public function __construct(
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        $this->config = $config;
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mst_rewards_earning_rule_queue', 'queue_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rewards\Model\Earning\Rule\Queue $object */
        if (!$object->getIsMassDelete()) {
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rewards\Model\Earning\Rule\Queue $object */
        if (!$object->getId()) {
            if (!$object->getCreatedAt()) {
                $object->setCreatedAt(
                    (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
                );
            }
        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rewards\Model\Earning\Rule\Queue $object */
        if (!$object->getIsMassStatus()) {
        }

        return parent::_afterSave($object);
    }

    /************************/
}
