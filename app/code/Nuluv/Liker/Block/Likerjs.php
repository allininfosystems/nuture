<?php

namespace Nuluv\Liker\Block;


use Magento\Framework\View\Element\Template;


class Likerjs extends Template
{


   protected $_registry;

   public function __construct(
    \Magento\Backend\Block\Template\Context $context,       
    \Magento\Framework\Registry $registry,
    array $data = []
    )
   {       
    $this->_registry = $registry;
    parent::__construct($context, $data);
}





}
