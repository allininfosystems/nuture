<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Plugin\Link;

use Magento\Framework\View\Element\Template;

class TopmenuPorto
{
    public function afterGetMegamenuHtml(\Smartwave\Megamenu\Block\Topmenu $topmenu, $html)
    {
        $brandHtml = $topmenu->getLayout()->createBlock('Mageplaza\Shopbybrand\Block\Brand')
            ->setTemplate('Mageplaza_Shopbybrand::position/topmenuporto.phtml')->toHtml();
        return $html.$brandHtml;
    }

}
