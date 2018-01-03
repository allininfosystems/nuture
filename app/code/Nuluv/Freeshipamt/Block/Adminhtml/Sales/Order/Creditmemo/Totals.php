<?php

namespace Nuluv\Freeshipamt\Block\Adminhtml\Sales\Order\Creditmemo;

class Totals extends \Magento\Framework\View\Element\Template {

	protected $_creditmemo = null;

	protected $_source;

	protected $_dataHelper;

	public function __construct(\Magento\Framework\View\Element\Template\Context $context,
		\Nuluv\Freeshipamt\Helper\Data $dataHelper,
		array $data = []
		) {
		$this -> _dataHelper = $dataHelper;
		parent :: __construct($context, $data);
	} 

	public function getSource() {
		return $this -> getParentBlock() -> getSource();
	} 

	public function getCreditmemo() {
		return $this -> getParentBlock() -> getCreditmemo();
	} 

	public function initTotals() {

		$this -> getParentBlock();
		$this -> getCreditmemo();
		$this -> getSource();

		
		if ($this -> getSource() -> getFreeshipamt()) {
		$total = new \Magento\Framework\DataObject([
			'code' => 'freeshipamt',
		    'strong' => false,
			'value' => $this -> getSource() -> getFreeshipamt(),
			'label' => $this -> _dataHelper -> getFeeLabelFreeshipamt(),
			]
			);
		$this -> getParentBlock() -> addTotalBefore($total, 'grand_total');
		}
	

		return $this;
	} 
} 
