<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Model\Earning\Rule\Condition;

use \Magento\Eav\Model as EavModel;

/**
 * @method \Mirasvit\Rewards\Model\Earning\Rule\Condition\Product setAttributeOption(string $param)
 * @method string getAttribute()
 * @method $this setAttribute(string $param)
 *
 * @SuppressWarnings(PHPMD)
 */
//class Product extends \Magento\Rule\Model\Condition\AbstractCondition
class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    public function __construct(
        \Magento\CatalogInventory\Model\Stock\ItemFactory $stockItemFactory,
        EavModel\ResourceModel\Entity\Attribute\Set\CollectionFactory $entityAttributeSetCollectionFactory,
        \Magento\Catalog\Model\Product\Type $productType,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    ) {
        $this->stockItemFactory                    = $stockItemFactory;
        $this->entityAttributeSetCollectionFactory = $entityAttributeSetCollectionFactory;
        $this->productFactory                      = $productFactory;
        $this->config                              = $config;
        $this->productType                         = $productType;
        $this->backendUrlManager                   = $backendUrlManager;
        $this->localeFormat                        = $localeFormat;
        $this->assetRepo                           = $context->getAssetRepository();
        $this->filesystem                          = $filesystem;
        $this->context                             = $context;

        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
    }

    /**
     * @var array
     */
    protected $_entityAttributeValues = null;

    /**
     * @param array &$attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes = array_merge($attributes, [
            'attribute_set_id' => __('Attribute Set'),
            'category_ids' => __('Category'),
            'qty' => __('Quantity'),
            'type_id' => __('Product Type'),
            'image' => __('Base Image'),
            'thumbnail' => __('Thumbnail'),
            'small_image' => __('Small Image'),
            'image_size' => __('Base Image Size (bytes)'),
            'thumbnail_size' => __('Thumbnail Size (bytes)'),
            'small_image_size' => __('Small Image Size (bytes)'),
            'php' => __('PHP Condition'),
        ]);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareValueOptions()
    {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        if ($this->getAttribute() === 'attribute_set_id') {
            $entityTypeId = $this->config
                ->getEntityType('catalog_product')->getId();
            $selectOptions = $this->entityAttributeSetCollectionFactory->create()
                ->setEntityTypeFilter($entityTypeId)
                ->load()
                ->toOptionArray();
        } elseif ($this->getAttribute() === 'type_id') {
            $typeOptions = $this->productType->getOptionArray();
            $selectOptions = [];
            foreach ($typeOptions as $key => $option) {
                $selectOptions[] = ['label' => $option, 'value' => $key];
            }
        } elseif (is_object($this->getAttributeObject())) {
            $attributeObject = $this->getAttributeObject();
            if ($attributeObject->usesSource()) {
                /* @noinspection PhpUndefinedMethodInspection */
                if ($attributeObject->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }
                $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
            }
        }

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = [];
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    /**
     * Retrieve value by option.
     *
     * @param string $option
     *
     * @return string
     */
    public function getValueOption($option = null)
    {
        $this->_prepareValueOptions();

        return $this->getData('value_option'.($option !== null ? '/'.$option : ''));
    }

    /**
     * Retrieve select option values.
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();

        return $this->getData('value_select_options');
    }

    /**
     * Retrieve after element HTML.
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids':
                    $image = $this->assetRepo->getUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger">
                <img src="'.$image.'" alt="" class="v-middle rule-chooser-trigger" title="'
                .__('Open Chooser').'" /></a>';
        }

        return $html;
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        /* @noinspection PhpUndefinedMethodInspection */
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();

        if (!in_array($attribute, ['category_ids', 'qty', 'php'])) {
            if ($attribute == 'image_size'
                || $attribute == 'small_image_size'
                || $attribute == 'thumbnail_size') {
                $attribute = str_replace('_size', '', $attribute);
            }

            $attributes = $this->getRule()->getCollectedAttributes();
            $attributes[$attribute] = true;
            $this->getRule()->setCollectedAttributes($attributes);
            $productCollection->addAttributeToSelect($attribute, 'left');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() === 'attribute_set_id'
            || $this->getAttribute() === 'type_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            case 'boolean':
                return 'boolean';

            default:
                return 'string';
        }
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() === 'attribute_set_id'
            || $this->getAttribute() === 'type_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
            case 'boolean':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }

    /**
     * @return $this
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    $element->setImage($this->assetRepo->getUrl('images/grid-cal.gif'));
                    break;
            }
        }

        return $element;
    }

    /**
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser'
                    .'/attribute/'.$this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/'.$this->getJsFormObject();
                }
                break;
        }

        return $url !== false ? $this->backendUrlManager->getUrl($url) : '';
    }

    /**
     * @return bool
     */
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids': case 'php':
                return true;
        }
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    return true;
            }
        }

        return false;
    }

    /**
     * @param array $arr
     * @return $this
     */
    public function loadArray($arr)
    {
        $this->setAttribute(isset($arr['attribute']) ? $arr['attribute'] : false);
        $attribute = $this->getAttributeObject();

        if ($attribute && $attribute->getBackendType() == 'decimal') {
            if (isset($arr['value'])) {
                if (!empty($arr['operator'])
                    && in_array($arr['operator'], ['!()', '()'])
                    && false !== strpos($arr['value'], ',')) {
                    $tmp = [];
                    foreach (explode(',', $arr['value']) as $value) {
                        $tmp[] = $this->localeFormat->getNumber($value);
                    }
                    $arr['value'] = implode(',', $tmp);
                } else {
                    $arr['value'] = $this->localeFormat->getNumber($arr['value']);
                }
            } else {
                $arr['value'] = false;
            }
            $arr['is_value_parsed'] = isset($arr['is_value_parsed'])
                ? $this->localeFormat->getNumber($arr['is_value_parsed']) : false;
        }

        return parent::loadArray($arr);
    }

    /**
     * Validate product attrbute value for condition.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        $attrCode = $this->getAttribute();

        switch ($attrCode) {
            case 'category_ids':
                $this->fixCategoryOperator();
                return $this->validateAttribute($object->getCategoryCollection()->getAllIds());
                break;

            case 'qty':
                /** @var \Magento\Catalog\Model\Product $object */
                $stockItem = $this->stockItemFactory->create()->load($object->getId(), 'product_id');

                if ($object->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                    return $this->validateAttribute($stockItem->getQty());
                }

                return true;
                break;

            case 'image_size':
            case 'small_image_size':
            case 'thumbnail_size':
                $imageCode = str_replace('_size', '', $attrCode);

                $imagePath = $object->getData($imageCode);
                $path = $this->filesystem
                        ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                        ->getAbsolutePath().'/catalog/product'.$imagePath;

                $size = 0;
                if (file_exists($path) && is_file($path)) {
                    $size = filesize($path);
                }

                return $this->validateAttribute($size);
                break;

            case 'php':
                $object = $object->load($object->getId());
                extract($object->getData());
                $expr = 'return '.$this->getValue().';';
                $value = eval($expr);

                if ($this->getOperator() == '==') {
                    return $value;
                } else {
                    return !$value;
                }

                break;

            default:
                if (!isset($this->_entityAttributeValues[$object->getId()])) {
                    $attr = $object->getResource()->getAttribute($attrCode);

                    if ($attr && $attr->getBackendType() == 'datetime' && !is_int($this->getValue())) {
                        $this->setValue(strtotime($this->getValue()));
                        $value = strtotime($object->getData($attrCode));

                        return $this->validateAttribute($value);
                    }

                    if ($attr && $attr->getFrontendInput() == 'multiselect') {
                        $value = $object->getData($attrCode);
                        $value = strlen($value) ? explode(',', $value) : [];

                        return $this->validateAttribute($value);
                    }

                    return parent::validate($object);
                } else {
                    $result = false; // any valid value will set it to TRUE
                    $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;
                    foreach ($this->_entityAttributeValues[$object->getId()] as $value) {
                        $attr = $object->getResource()->getAttribute($attrCode);
                        if ($attr && $attr->getBackendType() == 'datetime') {
                            $value = strtotime($value);
                        } elseif ($attr && $attr->getFrontendInput() == 'multiselect') {
                            $value = strlen($value) ? explode(',', $value) : [];
                        }

                        $object->setData($attrCode, $value);
                        $result |= parent::validate($object);

                        if ($result) {
                            break;
                        }
                    }

                    if ($oldAttrValue === null) {
                        $object->unsetData($attrCode);
                    } else {
                        $object->setData($attrCode, $oldAttrValue);
                    }

                    return (bool) $result;
                }
                break;
        }
    }

    /**
     * @return string
     */
    public function getJsFormObject()
    {
        return $this->getFormName().'rule_conditions_fieldset';
    }

    /**
     * Fix category_ids operator
     * @return void
     */
    private function fixCategoryOperator()
    {
        if ($this->getOperator() == '==') {
            $this->setOperator('()');
        }
        if ($this->getOperator() == '!=') {
            $this->setOperator('!()');
        }
    }
}
