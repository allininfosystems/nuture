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

/**
 * Class BrandList
 * @package Mageplaza\Shopbybrand\Block\Brand
 */
class BrandList extends \Mageplaza\Shopbybrand\Block\Brand
{

    //*********************** Get Brand List by First Char ***********************
    /**
     * @param $char
     *
     * @return mixed
     */
	public function getCollectionByChar($char)
    {
        $specialChar = ['!','"','#','$','&','(',')','*','+',',','-','.','/',':',';','<','=','>','?','@','[',']','^','_','`','{','|','}','~'];
        if (in_array($char,$specialChar)){
            $sqlCond =  "IF(tsv.value_id > 0, tsv.value, tdv.value) LIKE "."'".$char."%'";
        }elseif($char=="'"){
            $sqlCond =  "IF(tsv.value_id > 0, tsv.value, tdv.value) LIKE ".'"'.$char.'%"';
        }else{
            $sqlCond =  "IF(tsv.value_id > 0, tsv.value, tdv.value) REGEXP BINARY "."'^".$char."'";
        }
        $result = $this->_brandFactory->create()
            ->getBrandCollection(null, [],$sqlCond);

        return $result;
    }

    //*********************** Get Category Filter Class for Mixitup ***********************
    /**
     * @param $optionId
     *
     * @return string
     */
    public function getCatFilterClass($optionId)
    {
        return $this->helper->getCatFilterClass($optionId);
    }

    /**
     * @param $char
     *
     * @return string
     */
    public function getOptionIdsByChar($char){
        $optionIds = [];

        $brandCollection = $this->getCollectionByChar($char);
        foreach ($brandCollection as $brand){
                $optionIds [] = $brand->getId();
        }
        $result = implode(',',$optionIds);
        unset($optionIds);

        return $result;
    }

}
