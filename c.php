
<?php
    use Magento\Framework\App\Bootstrap;
    require __DIR__ . '/app/bootstrap.php';
    $params = $_SERVER;
    $bootstrap = Bootstrap::create(BP, $params);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $objectManager = $bootstrap->getObjectManager();
    $state = $objectManager->get('Magento\Framework\App\State');
    $state->setAreaCode('frontend');
    $storeManager   = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    $store          = $storeManager->getStore();
    $baseurl        = $store->getBaseUrl();
    $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection     = $resource->getConnection();


    $check           = 'abc123'; 
    $resource        = $objectManager->get('Bluethink\Refferalcode\Model\ResourceModel\Rewardpoint\Collection')->addFieldToFilter('refferalcode', array('eq' =>$check));
    
       
        
       