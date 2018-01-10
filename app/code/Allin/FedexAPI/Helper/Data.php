<?php

namespace Allin\FedexAPI\Helper;
								
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	public $scopeConfigInterface;
	public $fedex_account;
	public $fedex_meter_number;
	public $fedex_key;
	public $fedex_password;
	
	
	
	public function __construct(
	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
	) {
		$this->scopeConfig = $scopeConfigInterface;
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
		$this->fedex_account = $this->scopeConfig->getValue("carriers/fedex/account",$storeScope);
		$this->fedex_meter_number = $this->scopeConfig->getValue("carriers/fedex/meter_number",$storeScope);
		$this->fedex_key = $this->scopeConfig->getValue("carriers/fedex/key",$storeScope);
		$this->fedex_password = $this->scopeConfig->getValue("carriers/fedex/password",$storeScope);
		
		
	}
	
	public function fedexNumber(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$wsdlBasePath = $objectManager->create('Magento\Framework\Module\Dir\Reader')->getModuleDir(Dir::MODULE_ETC_DIR, 'Allin_FedexAPI') . '/wsdl/';
		$this->_shipServiceWsdl = $wsdlBasePath . 'ShipService_v21.wsdl';
	}
	
	public function getProperty($var){

			if($var == 'key') Return $this->fedex_key; 
			if($var == 'password') Return $this->fedex_password;
			if($var == 'parentkey') Return 'XXX'; 
			if($var == 'parentpassword') Return 'XXX'; 		
			if($var == 'shipaccount') Return $this->fedex_account;
			if($var == 'billaccount') Return $this->fedex_account;
			if($var == 'dutyaccount') Return $this->fedex_account; 
			if($var == 'freightaccount') Return 'XXX';  
			if($var == 'trackaccount') Return 'XXX'; 
			if($var == 'dutiesaccount') Return 'XXX';
			if($var == 'importeraccount') Return 'XXX';
			if($var == 'brokeraccount') Return 'XXX';
			if($var == 'distributionaccount') Return 'XXX';
			if($var == 'locationid') Return 'PLBA';
			if($var == 'printlabels') Return true;
			if($var == 'printdocuments') Return true;
			if($var == 'packagecount') Return '4';
			if($var == 'validateaccount') Return 'XXX';
			if($var == 'meter') Return $this->fedex_meter_number;
				
			if($var == 'shiptimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));

			if($var == 'spodshipdate') Return '2016-04-13';
			if($var == 'serviceshipdate') Return '2013-04-26';
			if($var == 'shipdate') Return '2016-04-21';

			if($var == 'readydate') Return '2014-12-15T08:44:07';
			//if($var == 'closedate') Return date("Y-m-d");
			if($var == 'closedate') Return '2016-04-18';
			if($var == 'pickupdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
			if($var == 'pickuptimestamp') Return mktime(8, 0, 0, date("m")  , date("d")+1, date("Y"));
			if($var == 'pickuplocationid') Return 'SQLA';
			if($var == 'pickupconfirmationnumber') Return '1';

			if($var == 'dispatchdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
			if($var == 'dispatchlocationid') Return 'NQAA';
			if($var == 'dispatchconfirmationnumber') Return '4';		
			
			if($var == 'tag_readytimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));
			if($var == 'tag_latesttimestamp') Return mktime(20, 0, 0, date("m"), date("d")+1, date("Y"));	

			if($var == 'expirationdate') Return date("Y-m-d", mktime(8, 0, 0, date("m"), date("d")+15, date("Y")));
			if($var == 'begindate') Return '2014-10-16';
			if($var == 'enddate') Return '2014-10-16';	

			if($var == 'trackingnumber') Return 'XXX';

			if($var == 'hubid') Return '5531';
			
			if($var == 'jobid') Return 'XXX';

			if($var == 'searchlocationphonenumber') Return '5555555555';
			if($var == 'customerreference') Return '39589';

			if($var == 'shipper') Return array(
				'Contact' => array(
					'PersonName' => 'Sender Name',
					'CompanyName' => 'Sender Company Name',
					'PhoneNumber' => '1234567890'
				),
				'Address' => array(
					'StreetLines' => array('Addres \r  s Line 1'),
					'City' => 'Collierville',
					'StateOrProvinceCode' => 'TN',
					'PostalCode' => '38017',
					'CountryCode' => 'US',
					'Residential' => 1
				)
			);
			if($var == 'recipient') Return array(
				'Contact' => array(
					'PersonName' => 'Recipient Name',
					'CompanyName' => 'Recipient Company Name',
					'PhoneNumber' => '1234567890'
				),
				'Address' => array(
					'StreetLines' => array('Address Line 1'),
					'City' => 'Herndon',
					'StateOrProvinceCode' => 'VA',
					'PostalCode' => '20171',
					'CountryCode' => 'US',
					'Residential' => 1
				)
			);	

			if($var == 'address1') Return array(
				'StreetLines' => array('10 Fed Ex Pkwy'),
				'City' => 'Memphis',
				'StateOrProvinceCode' => 'TN',
				'PostalCode' => '38115',
				'CountryCode' => 'US'
			);
			if($var == 'address2') Return array(
				'StreetLines' => array('13450 Farmcrest Ct'),
				'City' => 'Herndon',
				'StateOrProvinceCode' => 'VA',
				'PostalCode' => '20171',
				'CountryCode' => 'US'
			);					  
			if($var == 'searchlocationsaddress') Return array(
				'StreetLines'=> array('240 Central Park S'),
				'City'=>'Austin',
				'StateOrProvinceCode'=>'TX',
				'PostalCode'=>'78701',
				'CountryCode'=>'US'
			);
											  
			if($var == 'shippingchargespayment') Return array(
				'PaymentType' => 'SENDER',
				'Payor' => array(
					'ResponsibleParty' => array(
						'AccountNumber' => getProperty('billaccount'),
						'Contact' => null,
						'Address' => array('CountryCode' => 'US')
					)
				)
			);	
			if($var == 'shipperbilling') Return array(
				'Contact'=>array(
					'ContactId' => 'freight1',
					'PersonName' => 'Big Shipper',
					'Title' => 'Manager',
					'CompanyName' => 'Freight Shipper Co',
					'PhoneNumber' => '1234567890'
				),
				'Address'=>array(
					'StreetLines'=>array(
						'1202 Chalet Ln', 
						'Do Not Delete - Test Account'
					),
					'City' =>'Harrison',
					'StateOrProvinceCode' => 'AR',
					'PostalCode' => '72601-6353',
					'CountryCode' => 'US'
					)
			);
			
			if($var == 'freightbilling') Return array(
				'Contact'=>array(
					'ContactId' => 'freight1',
					'PersonName' => 'Big Shipper',
					'Title' => 'Manager',
					'CompanyName' => 'Freight Shipper Co',
					'PhoneNumber' => '1234567890'
				),
				'Address'=>array(
					'StreetLines'=>array(
						'1202 Chalet Ln', 
						'Do Not Delete - Test Account'
					),
					'City' =>'Harrison',
					'StateOrProvinceCode' => 'AR',
					'PostalCode' => '72601-6353',
					'CountryCode' => 'US'
					)
			);
	}
	
	function setEndpoint($var){
		if($var == 'changeEndpoint') Return false;
		if($var == 'endpoint') Return 'XXX';
	}
}