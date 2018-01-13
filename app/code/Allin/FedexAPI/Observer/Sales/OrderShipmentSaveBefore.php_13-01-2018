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
			$this->order = $order;
			$this->_request = $request;
			$this->unit_of_measure = $this->scopeConfig->getValue("carriers/fedex/unit_of_measure",$storeScope);
			$this->max_package_weight = $this->scopeConfig->getValue("carriers/fedex/max_package_weight",$storeScope);
			$this->packaging = $this->scopeConfig->getValue("carriers/fedex/packaging",$storeScope);
			$this->_io = $io;
			$this->_directoryList = $directoryList;
			$customPath = $this->_directoryList->getPath('media').'/fedexpdf';
			if (!file_exists($customPath)) {
				$this->_io->mkdir($this->_directoryList->getPath('media').'/fedexpdf', 0777);
			} 
			//die('hello dear');
	}
	
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
		//echo 'hello';
		//die('testing');
		
		$orderId = $this->_request->getParam('order_id');
        $orderData = $this->order->load($orderId);

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
			'ServiceType' => 'STANDARD_OVERNIGHT', // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_EXPRESS_SAVER
			'PackagingType' => $this->packaging, // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
			'Shipper' => $this->addShipper(),
			'Recipient' => $this->addRecipient(),
			'ShippingChargesPayment' => $this->addShippingChargesPayment(),
			'SpecialServicesRequested' => $this->addSpecialServices1(), //Used for Intra-India shipping - cannot use with PRIORITY_OVERNIGHT
			'CustomsClearanceDetail' => $this->addCustomClearanceDetail(),                                                                                                      
			'LabelSpecification' => $this->addLabelSpecification(),
			'CustomerSpecifiedDetail' => array('MaskedData'=> 'SHIPPER_ACCOUNT_NUMBER'), 
			'PackageCount' => 1,                                       
			'RequestedPackageLineItems' => array(
				'0' => $this->addPackageLineItem1()
			)
		);



		try{
			if($objectManager->create('Allin\FedexAPI\Helper\Data')->setEndpoint('changeEndpoint')){
				$newLocation = $client->__setLocation($objectManager->create('Allin\FedexAPI\Helper\Data')->setEndpoint('endpoint'));
			}
			
			$response = $client->processShipment($request); // FedEx web service invocation
/* echo '<pre>';
print_r($response);
die('testingssssss'); */
			if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
			   //$this->printSuccess($client, $response);

				// Create PNG or PDF labels
				// Set LabelSpecification.ImageType to 'PNG' for generating a PNG labels
				$fp = fopen($pdf, 'wb');   
				fwrite($fp, ($response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image));
				fclose($fp);
				/*
				echo 'Label <a href="./'.SHIP_LABEL.'">'.SHIP_LABEL.'</a> was generated.';           
				
				$fp = fopen(COD_LABEL, 'wb');   
				fwrite($fp, ($response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image));
				fclose($fp);
				echo 'Label <a href="./'.COD_LABEL.'">'.COD_LABEL.'</a> was generated.';  
				echo $response->CompletedShipmentDetail->AssociatedShipments->TrackingId->TrackingNumber;
				echo $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
				*/
				
				$finalTrackingNumber = $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
				$tracks = $observer->getEvent()->getTrack();
				$tracks->setTrackNumber($finalTrackingNumber);
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

		$personName = $companyName = $phoneNumber = $streetLines = $city = $stateOrProvinceCode = $postalCode = $countryCode='';
		
		$personName = $this->getShippingAddress['prefix'].' '.$this->getShippingAddress['firstname'].' '.$this->getShippingAddress['middlename'].' '.$this->getShippingAddress['lastname'];
		$companyName = $this->getShippingAddress['company'];
		$phoneNumber = $this->getShippingAddress['telephone'];
		$streetLines = $this->getShippingAddress['street'];
		$city = $this->getShippingAddress['city'];
		$stateOrProvinceCode = $this->getShippingAddress['region'];
		$postalCode = $this->getShippingAddress['postcode'];
		$countryCode = $this->getShippingAddress['country_id'];
		$shipper = array(
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
				'CountryName' => 'INDIA'
			)
		);
		return $shipper;
	}
	
	function addRecipient(){
		
		$personName = $companyName = $phoneNumber = $streetLines = $city = $stateOrProvinceCode = $postalCode = $countryCode='';
		
		$personName = $this->getBillingAddress['prefix'].' '.$this->getBillingAddress['firstname'].' '.$this->getBillingAddress['middlename'].' '.$this->getBillingAddress['lastname'];
		$companyName = $this->getBillingAddress['company'];
		$phoneNumber = $this->getBillingAddress['telephone'];
		$streetLines = $this->getBillingAddress['street'];
		$city = $this->getBillingAddress['city'];
		$stateOrProvinceCode = $this->getBillingAddress['region'];
		$postalCode = $this->getBillingAddress['postcode'];
		$countryCode = $this->getBillingAddress['country_id'];
		
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
					'Amount' => 400
				),
				'CollectionType' => 'GUARANTEED_FUNDS',// ANY, GUARANTEED_FUNDS
				'FinancialInstitutionContactAndAddress' => array(
					'Contact' => array(
						'PersonName' => 'Financial Contact',
						'CompanyName' => 'Financial Company',
						'PhoneNumber' => '8888888888'
					),
					'Address' => array(
						'StreetLines' => '1 FINANCIAL STREET',
						'City' => 'NEWDELHI',
						'StateOrProvinceCode' => 'DL',
						'PostalCode' => '110010',
						'CountryCode' => 'IN',
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
			'DutiesPayment' => array(
				'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
				'Payor' => array(
					'ResponsibleParty' => array(
						'AccountNumber' => $this->dutyaccount,
						'Contact' => null,
						'Address' => array(
							'CountryCode' => 'IN'
						)
					)
				)
			),
			'DocumentContent' => 'NON_DOCUMENTS',                                                                                            
			'CustomsValue' => array(
				'Currency' => 'INR', 
				'Amount' => 400.0
			),
			'CommercialInvoice' => array(
				'Purpose' => 'SOLD',
				'CustomerReferences' => array(
					'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
					'Value' => '1234'
				)
			),
			'Commodities' => array(
				'NumberOfPieces' => 1,
				'Description' => 'Books',
				'CountryOfManufacture' => 'IN',
				'Weight' => array(
					'Units' => 'KG', 
					'Value' => 1.0 //this is change from admin
				),
				'Quantity' => 1,
				'QuantityUnits' => 'KG',
				'UnitPrice' => array(
					'Currency' => 'INR', 
					'Amount' => 100.000000
				),
				'CustomsValue' => array(
					'Currency' => 'INR', 
					'Amount' => 400.000000
				)
			)
		);
		return $customerClearanceDetail;
	}
	
	function addPackageLineItem1(){
		$packageLineItem = array(
			'SequenceNumber'=>1,
			'GroupPackageCount'=>1,
			/* 'InsuredValue' => array(
				'Amount' => 100.00, 
				'Currency' => 'INR'
			), */
			'Weight' => array(
				'Value' => 68,//$this->max_package_weight,//get from fedex config from admin
				'Units' => $this->unit_of_measure
			)/* ,
			'Dimensions' => array(
				'Length' => 20,
				'Width' => 10,
				'Height' => 10,
				'Units' => 'IN'
			) */,
			'CustomerReferences' => array(
				'CustomerReferenceType' => 'CUSTOMER_REFERENCE', // valid values CUSTOMER_REFERENCE, INVOICE_NUMBER, P_O_NUMBER and SHIPMENT_INTEGRITY
				'Value' => 'GR4567892'
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
