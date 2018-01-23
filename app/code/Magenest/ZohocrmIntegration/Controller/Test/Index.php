<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 14/11/2016
 * Time: 14:39
 */
namespace Magenest\ZohocrmIntegration\Controller\Test;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magenest\ZohocrmIntegration\Model\Connector $connector
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magenest\ZohocrmIntegration\Model\Connector $connector
    ) {
        $this->_connector = $connector;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data = [
            'salutationtype' => '',
            'firstname' => 'James',
            'lead_no' => 'LEA135',
            'phone' =>'1234567898765',
            'lastname' => 'Nguyen',
            'mobile' =>'123456789',
            'company' =>'Magenest',
            'fax' =>'',
            'designation' =>'',
            'email' => 'duccanhdhbkhn@gmail.com',
            'leadsource' =>'',
            'website' =>'',
            'industry' =>'',
            'leadstatus' =>'',
            'annualrevenue' => '0.00000000',
            'rating' =>'',
            'noofemployees' => '0',
            'assigned_user_id' => '19x1',
            'secondaryemail' =>'',
            'createdtime' => '2017-04-14 02:14:01',
            'modifiedtime' => '2017-04-17 01:27:11',
            'modifiedby' => '19x1',
            'lane' =>'',
            'code' =>'123456',
            'city' =>'',
            'country' =>'',
            'state' =>'',
            'pobox' =>'',
            'description' =>'',
            'emailoptout' => '0',
            'id' => '10x454',
        ];

//        $collection = $this->_connector->query('SalesOrder','subject','165');
        $collection = $this->_connector->checkInstallEE();

        $logger = $this->_objectManager->create('\Psr\Log\LoggerInterface');
        $logger->debug(print_r($collection, true));


//        print_r($collection);
        return $this->resultPageFactory->create();
    }
}
