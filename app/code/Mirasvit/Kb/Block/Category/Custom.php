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



namespace Mirasvit\Kb\Block\Category;

class Custom extends \Magento\Framework\View\Element\Template
{
    protected function _prepareLayout()
  {
        parent::_prepareLayout();
        $objectManager   = \Magento\Framework\App\ObjectManager::getInstance();
        $articles4 = $objectManager->get('Mirasvit\Kb\Model\ResourceModel\Article\Collection');
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'fme.kb.pager'
        )->setAvailableLimit(array(8=>8,12=>12,16=>16,20=>20))->setShowPerPage(true)->setCollection(
            $articles4);
        $this->setChild('pager', $pager);
        $articles4->load();
        return $this;
    }
   
       public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
   
}
