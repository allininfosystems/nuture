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
namespace Mageplaza\Blog\Block\Post;

use Mageplaza\Blog\Block\Frontend;

/**
 * Class Listpost
 * @package Mageplaza\Blog\Block\Post
 */
class Listpost extends Frontend
{

	/**
	 * @return string
	 */
    public function checkRss()
    {
        return $this->helperData->getBlogUrl('post/rss');
    }
       protected function _prepareLayout()
   {
        parent::_prepareLayout();
        $objectManager   = \Magento\Framework\App\ObjectManager::getInstance();
        $postcollection = $objectManager->create('Mageplaza\Blog\Model\ResourceModel\Post\Collection');
        $pagerr = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'fme.blog.pager'
        )->setAvailableLimit(array(3=>3,6=>6,9=>9))->setShowPerPage(true)->setCollection(
            $postcollection);
        $this->setChild('pager', $pagerr);
        $postcollection->load();
        return $this;
    }
   
       public function getPagerHtmll()
    {
        return $this->getChildHtml('pager');
    }
   
}
