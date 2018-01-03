<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Config Field Separator Block
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Adminhtml\System\Config\Form\Field;

class Separator extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * render separator config row
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $htmlId = $element->getHtmlId();
        $html  = '<tr id="row_' . $htmlId . '">'
            . '<td class="label" colspan="3">';
//        $marginTop = '0px';
//        if(isset($fieldConfig['margin_top']) && $fieldConfig['margin_top']){
//            $marginTop = $fieldConfig['margin_top'];
//        }
        $customStyle = '';
        if(isset($fieldConfig['style']) && $fieldConfig['style']){
            $customStyle = $fieldConfig['style'];
        }

        $html .= '<div style="margin-top:10px; font-weight: bold; border-bottom: 1px solid #dfdfdf;text-align:left;'
            . $customStyle .'">';
        $html .= $element->getLabel();
        $html .= '</div></td></tr>';
        return $html;
    }
}
