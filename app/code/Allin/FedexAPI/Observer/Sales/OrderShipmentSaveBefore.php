<?php


namespace Allin\FedexAPI\Observer\Sales;

use Magento\Framework\Module\Dir;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;

class OrderShipmentSaveBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
	 
	protected $path_to_wsdl;
	protected $path_to_csv;
	protected $final_csv;
	protected $billaccount;
	protected $dutyaccount;
	protected $keyp;
	protected $password;
	protected $shipaccount;
	protected $meter;
	protected $order;
	protected $_request;
	protected $getShippingAddress;
	protected $getBillingAddress;
	protected $unit_of_measure;
	protected $max_package_weight;
	protected $packaging;
	protected $scopeConfigInterface;
	protected $pdfPath;
    protected $_io;
    protected $_directoryList;
	protected $store_country_id;
	protected $store_region_id;
	protected $store_city;
	protected $store_street_line1;
	protected $store_postcode;
	protected $store_phone;
	protected $store_name;
	protected $store_username;
	protected $tracks;
	protected $grand_total;
	
	
	public function __construct(
		\Magento\Framework\Module\Dir\Reader $configReader,
		\Magento\Sales\Api\Data\OrderInterface $order,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
		File $io,
        DirectoryList $directoryList
		) {
			
			$this->scopeConfig = $scopeConfigInterface;
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
			$this->pdfPath = $wsdlBasePath = $configReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Allin_FedexAPI') . '/wsdl/';
			$this->path_to_wsdl = $wsdlBasePath . 'ShipService_v21.wsdl';
			$this->path_to_csv = $wsdlBasePath . 'FedexZipCodes.csv';
			$this->order = $order;
			$this->_request = $request;
			$this->unit_of_measure = $this->scopeConfig->getValue("carriers/fedex/unit_of_measure",$storeScope);
			//$this->max_package_weight = $this->scopeConfig->getValue("carriers/fedex/max_package_weight",$storeScope);
			$this->packaging = $this->scopeConfig->getValue("carriers/fedex/packaging",$storeScope);
			
			$this->store_country_id = $this->scopeConfig->getValue("general/store_information/country_id",$storeScope);
			$this->store_region_id = $this->scopeConfig->getValue("general/store_information/region_id",$storeScope);
			$this->store_city = $this->scopeConfig->getValue("general/store_information/city",$storeScope);
			$this->store_street_line1 = $this->scopeConfig->getValue("general/store_information/street_line1",$storeScope);
			$this->store_postcode = $this->scopeConfig->getValue("general/store_information/postcode",$storeScope);
			$this->store_phone = $this->scopeConfig->getValue("general/store_information/phone",$storeScope);
			$this->store_name = $this->scopeConfig->getValue("general/store_information/name",$storeScope);
			$this->store_username = $this->scopeConfig->getValue("general/store_information/name2",$storeScope);
			
			
			$this->_io = $io;
			$this->_directoryList = $directoryList;
			$customPath = $this->_directoryList->getPath('media').'/fedexpdf';
			if (!file_exists($customPath)) {
				$this->_io->mkdir($this->_directoryList->getPath('media').'/fedexpdf', 0777);
			} 
			//die('hello dear');
			
			$myarray = glob($this->path_to_csv); 
			usort($myarray, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));

			/*This will create an array of associative arrays with the first row column headers as the keys.*/
			$csv_map = array_map('str_getcsv', file($myarray[count($myarray)-1]));
			array_walk($csv_map, function(&$a) use ($csv_map) {
			$a = array_combine($csv_map[0], $a);
			});
			array_shift($csv_map);
			
			$this->final_csv = $csv_map;
	}
	
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
		
		$this->tracks = $observer->getEvent()->getTrack();
				
		$orderId = $this->_request->getParam('order_id');
        $orderData = $this->order->load($orderId);
		$this->grand_total = $orderData->getData('grand_total');

		$this->getShippingAddress = $orderData->getShippingAddress()->getData();
		$this->getBillingAddress = $orderData->getBillingAddress()->getData();
		
		//Your observer code
		//===========================================
		//Please include and reference in $path_to_wsdl variable.
		//$path_to_wsdl = "ShipService_v21.wsdl";
		
		
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		$this->keyp = $objectManager->create('Allin\FedexAPI\Helper\Data')->getProperty('key');
		$this->password = $objectManager->create('Allin\FedexAPI\Helper\Data')->getProperty('password');
		$this->shipaccount = $objectManager->create('Allin\FedexAPI\Helper\Data')->getProperty('shipaccount');
		$this->meter = $objectManager->create('Allin\FedexAPI\Helper\Data')->getProperty('meter');
		$this->billaccount = $objectManager->create('Allin\FedexAPI\Helper\Data')->getProperty('billaccount');
		$this->dutyaccount = $objectManager->create('Allin\FedexAPI\Helper\Data')->getProperty('dutyaccount');
		
		// PDF label files. Change to file-extension .png for creating a PNG label (e.g. shiplabel.png)

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$mediaDirectory = $objectManager->get('Magento\Framework\Filesystem') ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA); //media dir path change it as per your requirement
        //$pdf = $mediaDirectory->getAbsolutePath('fedexpdf/'.$orderId.'-shiplabel.pdf');
		$pdf = $this->_directoryList->getPath('media').'/fedexpdf/'.$orderId.'-shiplabel.pdf';
		//$pdf = $this->pdfPath.$orderId.'-shiplabel.pdf';
		//define('SHIP_LABEL', $pdf);  
		//define('COD_LABEL', $orderId.'-codlabel.pdf'); 

		ini_set("soap.wsdl_cache_enabled", "0");

		$client = new \SoapClient($this->path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key' => $this->keyp, 
				'Password' => $this->password
			)
		);

		$request['ClientDetail'] = array(
			'AccountNumber' => $this->shipaccount, 
			'MeterNumber' => $this->meter
		);
		
		$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Intra India Shipping Request using PHP ***');
		$request['Version'] = array(
			'ServiceId' => 'ship', 
			'Major' => '21', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		$request['RequestedShipment'] = array(
			'ShipTimestamp' => date('c'),
			'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
			'ServiceType' => 'FEDEX_EXPRESS_SAVER', // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_EXPRESS_SAVER
			'PackagingType' => $this->packaging, // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
			'Shipper' => $this->addShipper(),
			'Recipient' => $this->addRecipient(),
			'ShippingChargesPayment' => $this->addShippingChargesPayment(),
			//'SpecialServicesRequested' => $this->addSpecialServices1(), //Used for Intra-India shipping - cannot use with PRIORITY_OVERNIGHT
			'CustomsClearanceDetail' => $this->addCustomClearanceDetail(),                                                                                                      
			'LabelSpecification' => $this->addLabelSpecification(),
			//'CustomerSpecifiedDetail' => array('MaskedData'=> 'SHIPPER_ACCOUNT_NUMBER'), 
			'PackageCount' => 1,                                       
			'RequestedPackageLineItems' => array(
				'0' => $this->addPackageLineItem1()
			)
		);

		$billerPostCode = $this->getShippingAddress['postcode'];
		
		foreach($this->final_csv as $key=>$csv_maps){
				/* echo '<pre>';print_r($csv_maps);
				die('testinjg'); */

				if( $billerPostCode==trim($csv_maps['Postal_Code']) ){
					if(trim($csv_maps['COD_Serviceable'])=='COD' && $this->grand_total >= 999){
						$request['RequestedShipment']['SpecialServicesRequested'] = $this->addSpecialServices1(); //Used for Intra-India shipping - cannot use with PRIORITY_OVERNIGHT
					}				
	
				} 
				
		}

		try{
			if($objectManager->create('Allin\FedexAPI\Helper\Data')->setEndpoint('changeEndpoint')){
				$newLocation = $client->__setLocation($objectManager->create('Allin\FedexAPI\Helper\Data')->setEndpoint('endpoint'));
			}
			
			$response = $client->processShipment($request); // FedEx web service invocation

			if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
				//$this->printSuccess($client, $response);
				// Create PNG or PDF labels
				// Set LabelSpecification.ImageType to 'PNG' for generating a PNG labels
				$fp = fopen($pdf, 'wb');   
				fwrite($fp, ($response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image));
				fclose($fp);
				$finalTrackingNumber = $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
				//$this->tracks = $observer->getEvent()->getTrack();
				$this->tracks->setTrackNumber($finalTrackingNumber);
			}else{
				//printError($client, $response);
			}
			
			//writeToLog($client);    // Write to log file
		} catch (SoapFault $exception) {
			//printFault($exception, $client);
		}
		
		//===========================================
		
		

		
    }
	
	function addShipper(){
		
		$shipper = array(
			'Contact' => array(
				'PersonName' => $this->store_username,
				'CompanyName' => $this->store_name,
				'PhoneNumber' => $this->store_phone
			),
			'Address' => array(
				'StreetLines' => $this->store_street_line1,
				'City' => $this->store_city,
				'StateOrProvinceCode' => $this->store_region_id,
				'PostalCode' => $this->store_postcode,
				'CountryCode' => $this->store_country_id,
				'CountryName' => 'INDIA'
			)
		);
		return $shipper;
	}
	
	function addRecipient(){
		
		$personName = $companyName = $phoneNumber = $streetLines = $city = $stateOrProvinceCode = $postalCode = $countryCode='';
		
		$personName = $this->getShippingAddress['prefix'].' '.$this->getShippingAddress['firstname'].' '.$this->getShippingAddress['middlename'].' '.$this->getShippingAddress['lastname'];
		$companyName = $this->getShippingAddress['company'];
		$phoneNumber = $this->getShippingAddress['telephone'];
		$streetLines = $this->getShippingAddress['street'];
		$city = $this->getShippingAddress['city'];
		$stateOrProvinceCode = $this->getShippingAddress['region'];
		$postalCode = $this->getShippingAddress['postcode'];
		$countryCode = $this->getShippingAddress['country_id'];
		
		$recipient = array(
			'Contact' => array(
				'PersonName' => $personName,
				'CompanyName' => $companyName,
				'PhoneNumber' => $phoneNumber
			),
			'Address' => array(
				'StreetLines' => $streetLines,
				'City' => $city,
				'StateOrProvinceCode' => $stateOrProvinceCode,
				'PostalCode' => $postalCode,
				'CountryCode' => $countryCode,
				'CountryName' => 'INDIA',
				'Residential' => false
			)
		);
		return $recipient;	                                    
	}
	
	function addShippingChargesPayment(){
		$shippingChargesPayment = array(
			'PaymentType' => 'SENDER',
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => $this->billaccount,
					'Contact' => null,
					'Address' => array('CountryCode' => $this->getShippingAddress['country_id'])
				)
			)
		);
		return $shippingChargesPayment;
	}
	function addLabelSpecification(){
		$labelSpecification = array(
			'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
			'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
			'LabelStockType' => 'PAPER_7X4.75'
		);
		return $labelSpecification;
	}
	
	function addSpecialServices1(){
		$specialServices = array(
			'SpecialServiceTypes' => 'COD',
			'CodDetail' => array(
				'CodCollectionAmount' => array(
					'Currency' => 'INR', 
					'Amount' => 1
				),
				'CollectionType' => 'GUARANTEED_FUNDS',// ANY, GUARANTEED_FUNDS
				'FinancialInstitutionContactAndAddress' => array(
					'Contact' => array(
						'PersonName' =>  $this->store_username,
						'CompanyName' => $this->store_name,
						'PhoneNumber' => $this->store_phone
					),
					'Address' => array(
						'StreetLines' => $this->store_street_line1,
						'City' => $this->store_city,
						'StateOrProvinceCode' => $this->store_region_id,
						'PostalCode' => $this->store_postcode,
						'CountryCode' => $this->store_country_id,
						'CountryName' => 'INDIA'
					)
				),
				'RemitToName' => 'Remitter'
			)
		);
		return $specialServices; 
	}
	
	function addCustomClearanceDetail(){
		$customerClearanceDetail = array(
			                                                                                     
			'CustomsValue' => array(
				'Currency' => 'INR', 
				'Amount' => $this->grand_total //changen dynamic
			),
			'CommercialInvoice' => array(
				'Purpose' => 'SOLD'
			),
			'Commodities' => array(
				'NumberOfPieces' => 1,
				'Description' => 'Here you can wright custom description',
				'CountryOfManufacture' => 'IN',
				'Weight' => array(
					'Units' => 'KG', 
					'Value' => 1.0 //this is change from admin
				),
				'Quantity' => 1,
				'QuantityUnits' => 'KG',
				'UnitPrice' => array(
					'Currency' => 'INR', 
					'Amount' => 1.0
				)
			)
		);
		return $customerClearanceDetail;
	}
	
	function addPackageLineItem1(){
		
		$custom_weight = $this->tracks->getData('weight');
		if($custom_weight > 68){
			$custom_weight = null;
		}
		$packageLineItem = array(
			'Weight' => array(
				'Value' => $custom_weight,//$this->max_package_weight,//get from fedex config from admin 68
				'Units' => $this->unit_of_measure
			)
		);
		return $packageLineItem;
	}
	/**
	 *  Print SOAP Fault
	 */  
	function printFault($exception, $client) {
	   echo '<h2>Fault</h2>' . "<br>\n";                        
	   echo "<b>Code:</b>{$exception->faultcode}<br>\n";
	   echo "<b>String:</b>{$exception->faultstring}<br>\n";
	   writeToLog($client);
		
	  echo '<h2>Request</h2>' . "\n";
		echo '<pre>' . htmlspecialchars($client->__getLastRequest()). '</pre>';  
		echo "\n";
	}
	
	function printSuccess($client, $response) {
		$this->printReply($client, $response);
	}
	
	function printReply($client, $response){
		$highestSeverity=$response->HighestSeverity;
		if($highestSeverity=="SUCCESS"){echo '<h2>The transaction was successful.</h2>';}
		if($highestSeverity=="WARNING"){echo '<h2>The transaction returned a warning.</h2>';}
		if($highestSeverity=="ERROR"){echo '<h2>The transaction returned an Error.</h2>';}
		if($highestSeverity=="FAILURE"){echo '<h2>The transaction returned a Failure.</h2>';}
		echo "\n";
		$this->printNotifications($response -> Notifications);
		$this->printRequestResponse($client, $response);
	}
	function printNotifications($notes){
		foreach($notes as $noteKey => $note){
			if(is_string($note)){    
				echo $noteKey . ': ' . $note . Newline;
			}
			else{
				$this->printNotifications($note);
			}
		}
		echo Newline;
	}
	function printRequestResponse($client){
		echo '<h2>Request</h2>' . "\n";
		echo '<pre>' . htmlspecialchars($client->__getLastRequest()). '</pre>';  
		echo "\n";
	   
		echo '<h2>Response</h2>'. "\n";
		echo '<pre>' . htmlspecialchars($client->__getLastResponse()). '</pre>';
		echo "\n";
	}
	function printError($client, $response){
		$this->printReply($client, $response);
	}

	
}
