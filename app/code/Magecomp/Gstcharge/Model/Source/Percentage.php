<?php
 
namespace Magecomp\Gstcharge\Model\Source;
 
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource; 
/**
 * Custom Attribute Renderer
 *
 * @author      Webkul Core Team <support@webkul.com>
 */
class Percentage extends AbstractSource
{
    /**
     * @var OptionFactory
     */
    protected $optionFactory;
 
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        /* your Attribute options list*/
        $this->_options=[ 
						  ['label'=>'None', 'value'=>'-1'],
						  ['label'=>'0', 'value'=>'0'],
                          ['label'=>'0.25%', 'value'=>'0.25'],
						  ['label'=>'3%', 'value'=>'3'],
                          ['label'=>'5%', 'value'=>'5'],
                          ['label'=>'12%', 'value'=>'12'],
                          ['label'=>'18%', 'value'=>'18'],
                          ['label'=>'24%', 'value'=>'24'],						  						  
                          ['label'=>'28%', 'value'=>'28']						  						  						  
                         ];
        return $this->_options;
    }
 
    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
 
    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Custom Attribute Options  ' . $attributeCode . ' column',
            ],
        ];
    }
}