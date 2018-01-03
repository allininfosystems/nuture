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

class Attribute extends AbstractAttribute
{
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
        $values = $this->_dataHelper->getSelectedAttributes();
        return $values;
    }

    /**
     * Get all attributes
     * @return Array
     */
    protected function _prepareAttributes()
    {
        $attributes = $this->_filterableAttributes->getFilters();
        $this->_active = array_flip(array_keys($this->_values));

        //Sort attributes by active and non active
        //value can be string or array
        foreach ($this->_values as $attrKey => $value) {
            if (!is_array($value)) {
                if ($attrKey == 'category') {

                }
                if (isset($attributes[$attrKey])) {
                    $this->_active[$attrKey] = $attributes[$attrKey];
                    unset($attributes[$attrKey]);
                }
            } else {
                $this->_active[$attrKey] = [
                    'group' => true,
                    'attributes' => []
                ];
                foreach ($value as $_attrKey => $item) {

                    $this->_active[$attrKey]['attributes'][$_attrKey] = $attributes[$_attrKey];
                    unset($attributes[$_attrKey]);
                }
            }
        }

        $this->_notActive = $attributes;
        asort($this->_notActive);

        return $this;
    }
}
