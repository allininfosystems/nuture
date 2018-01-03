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

namespace Plumrocket\ProductFilter\Block\Adminhtml\System\Config\Form;

class CustomOption extends AbstractAttribute
{

    /**
     * Filter List
     * @var Magento\Catalog\Model\Layer\Category\FilterableAttributeList
     */
    protected $_filterList;

    /**
     * Is custom options
     * @var boolean
     */
    protected $_isCustomOptions = true;

    /**
     * Data helper
     * @var Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters
     */
    protected $_additionalFilters;

    /**
     * Prepare selected attributes
     * @param  string $value
     * @return Array
     */
    protected function _prepareValue()
    {
        $values = $this->_dataHelper->getSelectedCustomOptions();
        return $values;
    }

    /**
     * Get all custom options
     * @return Array
     */
    protected function _prepareAttributes()
    {

        $connection = $this->_resourceConnection->getConnection();

        $select = $connection->select()
              ->from(
                  ['option_title' => $this->_resourceConnection->getTableName('catalog_product_option_title')]
              )
              ->group('title');
        $attrs = $connection->fetchAll($select);

        $attributes = [];
        foreach ($attrs as $attr) {
            $attributes[$attr['title']] = new \Magento\Framework\DataObject(['attribute_code' => $attr['title'], 'frontend_label' => $attr['title']]);
        }

        //Sort attributes by active and non active
        foreach ($attributes as $attributeCode => $attribute) {
            if (in_array($attributeCode, $this->_values)) {
                $this->_active[$attributeCode] = $attribute;
            } else {
                $this->_notActive[$attributeCode] = $attribute;
            }
        }

        return $this;
    }
}
