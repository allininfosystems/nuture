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

namespace Mageplaza\Shopbybrand\Block\Link;

/**
 * Class CategoryMenu
 *
 * @package Mageplaza\Shopbybrand\Block\Link
 */
class CategoryMenu extends \Mageplaza\Shopbybrand\Block\Brand
{
    protected $_template = 'Mageplaza_Shopbybrand::position/topmenu.phtml';

    //************************* Get Count for Category Mega Menu  ***************************
    /**
     * @return int
     */
    public function getBrandCount(){
        $brands = $this->getCollection();
        $count = ceil(count($brands)/12);
        return (int) $count;
    }

    /**
     * @return array
     */
    public function getBrands(){
        $brands = $this->getCollection();
        $result=[];
        $i=0;
        $count = 0;
        foreach ($brands as $brand){
            $count++;
            $result[$i][]=$brand;
            if($count==12){
                $count=0;
                $i++;
            }
        }
        return $result;
    }
}
