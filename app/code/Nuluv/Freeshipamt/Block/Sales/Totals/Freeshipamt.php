<?php

namespace Nuluv\Freeshipamt\Block\Sales\Totals;

class Freeshipamt extends \Magento\Framework\View\Element\Template {

	protected $_dataHelper;

	protected $_order;

	protected $_source;

	public function __construct(\Magento\Framework\View\Element\Template\Context $context,
		\Nuluv\Freeshipamt\Helper\Data $dataHelper,
		array $data = []
		) {
		$this -> _dataHelper = $dataHelper;
		parent :: __construct($context, $data);
	} 

	public function displayFullSummary() {
		return true;
	} 

	public function getSource() {
		return $this -> _source;
	} 

	public function getStore() {
		return $this -> _order -> getStore();
	} 

	public function getOrder() {
		return $this -> _order;
	} 

	public function getLabelProperties() {
		return $this -> getParentBlock() -> getLabelProperties();
	} 

	public function getValueProperties() {
		return $this -> getParentBlock() -> getValueProperties();
	} 

	public function initTotals() {
		$parent = $this -> getParentBlock();
		$this -> _order = $parent -> getOrder();
		$this -> _source = $parent -> getSource();

		$freeshipamt = new \Magento\Framework\DataObject([
			'code' => 'freeshipamt',
			'strong' => false,
			'value' => $this -> _source -> getFreeshipamt(),
			'label' => $this -> _dataHelper -> getFeeLabelFreeshipamt(),
			]
			);

		$parent -> addTotal($freeshipamt, 'freeshipamt');

		return $this;
	} 
} 