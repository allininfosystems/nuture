<?php
namespace Bluethink\Productimport\Controller\Adminhtml\Import;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
class Save extends \Magento\Backend\App\Action
{

    protected $_storeManager;
    public $manager;
    public function __construct(
    \Magento\Backend\App\Action\Context $context,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_storeManager;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
     
    }
	public function execute()
    {
		
        if(isset($_FILES['csv_file']['name']) && $_FILES['csv_file']['name'] != '')
        {
            $csv_extension=explode('.', $_FILES['csv_file']['name']);
            if ($csv_extension[1]!='csv') 
            {
                $this->messageManager->addError('Only CSV file allowed..');
                return $this->_redirect('*/*/new');
            }
            else
            {
                $result = $this->_uploadCsvFile($_FILES['csv_file']);
                $path = $result['path'].$result['file'];
                $bb=explode('storefile',$path);
                $b=$bb['0'];
                $handle=fopen($path, 'r');
                $row=0;
                while (($fdata = fgetcsv($handle)) !== FALSE)
                {
                    if ($row>0) 
                    {  

                                $categories_id=explode(",", $fdata[11]);                            
                                $today_date = date("m/d/Y");
                                $added_date = date('m/d/Y',strtotime("+17 day"));
                                $product=$this->_objectManager->create('\Magento\Catalog\Model\Product');
                                $product->setWebsiteIds(array(1));
                                $product->setAttributeSetId(4);
                                $product->setTypeId('simple');
                                $product->setCreatedAt(strtotime('now')); 
                                $product->setName($fdata[0]); 
                                $product->setSku($fdata[1]);
                                $product->setWeight($fdata[2]);
                                $product->setStatus($fdata[3]);
                                $product->setCategoryIds($categories_id); 
                                $product->setTaxClassId($fdata[15]); 
                                $product->setVisibility(4); 
                                $product->setNewsFromDate($today_date); 
                                $product->setNewsToDate($added_date); 
                                $product->setCountryOfManufacture('IN'); 
                                $product->setPrice($fdata[4]);
                                $product->setMetaTitle($fdata[6]);
                                $product->setMetaKeyword($fdata[7]);
                                $product->setMetaDescription($fdata[8]);
                                $product->setDescription($fdata[9]);
                                $product->setShortDescription($fdata[10]);
                               
                                $product->setStockData(
                                    array(
                                    'use_config_manage_stock' => 0, 
                                    'manage_stock' => 1, 
                                    'min_sale_qty' => 1, 
                                    'max_sale_qty' => 2, 
                                    'is_in_stock' => 1, 
                                    'qty' => $fdata[5] 
                                    )
                                );  
                                $collection =$this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
                                $product_sku = array();
                                    foreach ($collection as $_pcollection) 
                                    {
                                        $product_sku[]=$_pcollection['sku'];
                                    }
                                    if (in_array($fdata[1],$product_sku))
                                    {
                                        echo " Product sku already exist :: ".$fdata[1]. "</br>";
                                    }
                            else{
                                    $product->save();
                                     if (!empty($fdata[15]))
                                    {
                                       $product->setUrlKey($fdata[15]);
                                       $product->save();
                                    }
                                    $get_product_id = $product->getId();
                                    echo "Upload simple product id created sucessfully:: ".$get_product_id."</br>";
                                    $model = $this->_objectManager->create('Bluethink\Productimport\Model\Import');
                                    $model->setProductId($product->getId());
                                    $model->setProductSku($product->getSku());
                                    $model->save();
                                    $storeManager1 = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
                                        $currentStore = $storeManager1->getStore();
                                        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                                     
                                            $imgUrls=explode(",",$fdata[14]);
                                            foreach ($imgUrls as $imgUrl)
                                            {
                                                $dir=$b."zipped/";

                                                //$dir = "/var/www/html/mage2/pub/media/zipped/";
                                                $imagexp=explode("?",$imgUrl);
                                                $Rpath = $dir.basename($imagexp['0']);
                                                $ch = curl_init ($imgUrl);
                                                curl_setopt($ch, CURLOPT_HEADER, 0);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
                                                $rawdata=curl_exec($ch);
                                                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                curl_close($ch);
                                                if(file_exists($Rpath))
                                                {
                                                    unlink($Rpath);
                                                }
                                                $fp = fopen($Rpath,'w');
                                                $r = fwrite($fp,$rawdata);
                                                fclose($fp);
                                                //$output = shell_exec('sudo chmod -R 777 /var/www/html/mage2/pub');
                                                $output = shell_exec('sudo chmod -R 777 pub');
                                                echo "<pre>$output</pre>";
                                                $product->addImageToMediaGallery($Rpath, array('image', 'small_image', 'thumbnail'), true, false);
                                                $product->save();
                                                
                                        }
                                        
                                    }
                                }
                            $row++;
                        }
                    }
                }
        }

    public function _uploadCsvFile($_csvfile)
    {
        $uploader = $this->_fileUploaderFactory->create(array('fileId' => 'csv_file'));
        $uploader->setAllowedExtensions(array('csv'));
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $mediaDirectory = $this->_objectManager
                        ->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(DirectoryList::MEDIA);
        $_result = $uploader->save($mediaDirectory->getAbsolutePath('/storefile'));
        return $_result;
    }
}
