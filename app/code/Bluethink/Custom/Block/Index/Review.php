<?php
/**
 * Copyright © 2015 Bluethink . All rights reserved.
 */
namespace Bluethink\Custom\Block\Index;
use Bluethink\Custom\Block\BaseBlock;
class Review extends BaseBlock
{
	public $hello='Hello World';
	 
	 public function getRatingSummary($product)
	{
	    $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
	    $ratingSummary = $product->getRatingSummary()->getRatingSummary();
	    return $ratingSummary;
	}
}
