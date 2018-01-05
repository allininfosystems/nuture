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



namespace Mirasvit\Rewards\Ui\Earning\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory;
use Magento\Framework\Registry;

class ProductDataProvider extends AbstractDataProvider
{
    public function __construct(
        CollectionFactory $collectionFactory,
        Registry $registry,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->registry = $registry;
        $this->collection = $collectionFactory->create()
            ->addWebsiteColumn()
            ->addCustomerGroupColumn();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }


    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection as $item) {
            $result[$item->getId()] = $this->prepareItem($item->getData());
        }

        return $result;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function prepareItem($item)
    {
        $earningRule = $this->registry->registry('current_earning_rule');
        if ($earningRule) {
            $item['product_notification'] = $earningRule->getProductNotification();
        }

        return $item;
    }
}
