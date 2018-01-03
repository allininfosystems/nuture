<?php
/**
 * Webinse
 *
 * PHP Version 5.6.23
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */
/**
 * Button to auto-generate barcodes for products.
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    /**
     * @param AbstractElement $element
     * @return mixed
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $url = $this->getUrl('adminhtml/generator/autoGenerate');

        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'id' => 'generate_button',
                'label' => __('Generate'),
                'onclick' => "window.location='{$url}';",
            ]);

        return $button->toHtml();
    }
}