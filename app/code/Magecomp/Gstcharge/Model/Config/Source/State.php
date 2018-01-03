<?php
namespace Magecomp\Gstcharge\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Directory\Model\RegionFactory;

class State implements ArrayInterface
{
	protected $regionCollection; 
	public function __construct(
	RegionFactory $regionCollection)
	{
		 $this->regionCollection = $regionCollection;
	}
    public function toOptionArray()
    {
		$colllection = $this->regionCollection->create()->getCollection()->addFieldToFilter('country_id','IN');
     		foreach ($colllection as $_stateVal) 
			{
				$state_data []= array('value' => $_stateVal->getRegionId(), 'label'=>__($_stateVal->getName()));
			}
		return $state_data;	
    }
}
