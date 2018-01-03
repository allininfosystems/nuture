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
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Block\Brand;

use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Mageplaza\Shopbybrand\Model\CategoryFactory;

/**
 * Class Category
 * @package Mageplaza\Shopbybrand\Block\Brand
 */
class Category extends \Mageplaza\Shopbybrand\Block\Brand
{
    /**
     * @return mixed
     */
    public function getCollectionByChar($char)
    {
        return $this->getCollection(
            \Mageplaza\Shopbybrand\Helper\Data::BRAND_FIRST_CHAR,
            $this->getOptionIds(), $char
        );
    }

}
