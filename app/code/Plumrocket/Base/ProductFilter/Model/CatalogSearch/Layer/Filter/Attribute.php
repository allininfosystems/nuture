<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Model\CatalogSearch\Layer\Filter;

class Attribute extends \Magento\Catalog\Model\Layer\Filter\Attribute
{
    /**
     * {@inheritdoc}
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);
        if (empty($attributeValue)) {
            return $this;
        }

        $attributeValue = explode(',', $attributeValue);
        if (!count($attributeValue)) {
            return $this;
        }

        $attribute = $this->getAttributeModel();
        $productCollection = $this->getLayer()
            ->getProductCollection();

        $productCollection->addFieldToFilter($attribute->getAttributeCode(), ['in' => $attributeValue]);

        foreach ($attributeValue as $attributeVal) {

            $label = $this->getOptionText($attributeVal);
            $this->getLayer()
                ->getState()
                ->addFilter($this->_createItem($label, $attributeVal)->setIsActive(true));

        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function isOptionReducesResults($optionCount, $totalSize)
    {
        return true;
    }
}
