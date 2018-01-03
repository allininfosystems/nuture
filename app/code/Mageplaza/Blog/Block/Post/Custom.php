<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-kb
 * @version   1.0.26
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mageplaza\Blog\Block\Post;

class Custom extends \Magento\Framework\View\Element\Template
{
 
      protected function _prepareLayout()
   {
        parent::_prepareLayout();
        $objectManager   = \Magento\Framework\App\ObjectManager::getInstance();
        $postcollection = $objectManager->create('Mageplaza\Blog\Model\ResourceModel\Post\Collection');
       
        $pagerr = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'fme.blog.pager'
        )->setAvailableLimit(array(2=>2,4=>4,8=>8))->setShowPerPage(true)->setCollection(
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
