<?php
namespace Bluethink\Faq\Block\Adminhtml\Faq\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;


class Content extends AbstractRenderer
{
    private $_storeManager;
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context, StoreManagerInterface $storemanager, array $data = [])
    {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
    /**
     * Renders grid column
     *
     * @param Object $row
     * @return  string
     */
    public function render(DataObject $row)
    {    

        
        $value = $row->getData($this->getColumn()->getIndex());
       // echo "string+++++++++++++++++++";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $body = $objectManager->get('Bluethink\Faq\Model\Faqcategory')->load($value);
        $data=$body->getFaqCatName();

        return $data;



        
    }
}