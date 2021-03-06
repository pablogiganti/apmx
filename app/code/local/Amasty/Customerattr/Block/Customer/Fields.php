<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Customerattr
*/ 
class Amasty_Customerattr_Block_Customer_Fields extends Mage_Core_Block_Template
{
    protected $_entityTypeId;
    
    protected $_formElements = null;
    
    public static $renderedElements = array();
    
    protected $_fieldsToRender = array();
    
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amcustomerattr/customer_fields.phtml');
        
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType('customer')->getTypeId();
    }
    
    /**
     * Get Elements relation.
     * Returns:
     * option_id | parent_code | dependent_code
     * 
     */
    public function getElementsRelation()
    {
    	if (!Mage::registry('amcustomerattr_attributes_relation')) {
    		$relation =  Mage::getModel('amcustomerattr/relation')->getElementsRelation();
    		Mage::register('amcustomerattr_attributes_relation', $relation);
    	}
    	return Mage::registry('amcustomerattr_attributes_relation');
    }
    
    protected function _getElementsToRender()
    {
    	$elements = $this->getData('fields');
    	$newElements = array();
    	foreach ($elements as $key => $code) {
    		if (!Amasty_Customerattr_Block_Customer_Fields::elementAlreadyRendered($code)) {
	    		$newElements[] = $code;
    		}
    	}
    	$this->_fieldsToRender = $newElements;
    	return $newElements;
    }
    
    public static function elementAlreadyRendered($code)
    {
    	$elements = self::$renderedElements;
    	 
    	if (!in_array($code, $elements)) {
    		self::$renderedElements[] = $code;
    		return false;
    	} else {
    		return true;
    	}
    	
    }
    
    
    /**
     * Enter description here ...
     * @return Ambigous <mixed, NULL, multitype:>
     */
    public function getAttributes()
    {
    	if (!Mage::registry('amcustomerattr_attributes')) {

    		$collection = Mage::getModel('customer/attribute')->getCollection();
	        $collection->addFieldToFilter('is_user_defined', 1);
	        
	        if (Mage::getSingleton('customer/session')->isLoggedIn() && !Mage::getStoreConfig('amcustomerattr/general/allow_change_group')) {
	        	$collection->addFieldToFilter('type_internal', array('neq'=> 'selectgroup'));
			}
			
	        if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Orderattr/active'))
	        {
	            $alias = 'main_table';
	            if (false !== strpos($collection->getSelect()->__toString(), $alias))
	            {
	                $collection->addFieldToFilter('main_table.is_visible_on_front', 1);
	            } else 
	            {
	                $collection->addFieldToFilter('e.is_visible_on_front', 1);
	            }
	        } else 
	        {
	            $collection->addFieldToFilter('is_visible_on_front', 1);
	        }
	        
	        if ($this->_isCheckout())
	        {
	            // Show on Billing During Checkout
	            $collection->addFieldToFilter('used_in_product_listing', 1);
	        }
	        
	        $collection->addFieldToFilter('entity_type_id', $this->_entityTypeId);
	        
	        if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Orderattr/active'))
	        {
	            $alias = 'main_table';
	            if (false !== strpos($collection->getSelect()->__toString(), $alias))
	            {
	                $collection->getSelect()->order('main_table.sorting_order');
	            } else 
	            {
	                $collection->getSelect()->order('e.sorting_order');
	            }
	        } else 
	        {
	            $collection->getSelect()->order('sorting_order');
	        }
	        
	        if ($this->_isRegistration())
	        {
	            $collection->addFieldToFilter('on_registration', 1);
	        }
	        
	        $attributes = $collection->load();
	        
    		Mage::register('amcustomerattr_attributes', $attributes);
    	}
    	return Mage::registry('amcustomerattr_attributes');
    }
    
    public function getFormElements()
    {
        if (!is_null($this->_formElements)) {
            return $this->_formElements;
        }
        
        $attributes = $this->getAttributes();
        
        $attributesToRender = $this->_getElementsToRender();
        
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('amcustomerattr' . rand(0, 100) , array('class' => 'amcustomerattr'));
        
        /**
        * Loading current customer, it we are on edit page
        */
        $customer = Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getCustomer() : null;
        
        $isAnyAttributeApplies = false;
        
        $customerAttributes = Mage::getSingleton('customer/session')->getAmcustomerattr();
        
        foreach ($attributes as $attribute)
        {
        	if (!empty($attributesToRender) && !in_array($attribute->getAttributeCode(), $attributesToRender)) {

        		continue;
        	}
            $currentStore = ($customer ? $customer->getStoreId() : Mage::app()->getStore()->getId());
            $storeIds = explode(',', $attribute->getData('store_ids'));
            
            $defaultValue = $attribute->getDefaultValue();

            if (!in_array($currentStore, $storeIds) && !in_array(0, $storeIds))
            {
                continue;
            }
            
            $isAnyAttributeApplies = true;
            
            if ($inputType = $attribute->getFrontend()->getInputType())
            {
                if ($attribute->getTypeInternal())
                {
                	if ('statictext' == $attribute->getTypeInternal()){
                       $inputType = 'note'; 
                    } else if ('selectgroup' == $attribute->getTypeInternal()){
                       $inputType = 'select'; 
                    } else {
                        $inputType = $attribute->getTypeInternal();
                    }
                }
                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $fieldName = $this->_isCheckout() 
                                    ? 'billing[amcustomerattr][' . $attribute->getAttributeCode(). ']'
                                    : 'amcustomerattr[' . $attribute->getAttributeCode() . ']';
                                    
                // default_value
                $attributeValue = '';
                if ($customer)
                {
                    $attributeValue = $customer->getData($attribute->getAttributeCode());
                } elseif ($attribute->getData('default_value'))
                {
                    $attributeValue = $attribute->getData('default_value');
                }
                
                // if for example there was page reload with error, we putting attribute back from session
                if (isset($customerAttributes[$attribute->getAttributeCode()]))
                {
                    $attributeValue = $customerAttributes[$attribute->getAttributeCode()];
                }
                
                // applying translations
                $translations = $attribute->getStoreLabels();
                if (isset($translations[Mage::app()->getStore()->getId()]))
                {
                    $attributeLabel = $translations[Mage::app()->getStore()->getId()];
                } else 
                {
                    $attributeLabel = $attribute->getFrontend()->getLabel();
                }
                
                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
                    array(
                        'name'      => $fieldName,
                        'label'     => $attributeLabel,
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                    	'disabled'  => $attribute->getIsReadOnly(),
                        'note'      => $attribute->getNote(),
                        'value'     => $attributeValue,
                    	'text'		=> $attributeValue
                    )
                )
                ->setEntityAttribute($attribute);
                
                
                $element->setText($attributeValue);
                
                $element->setAfterElementHtml('<div style="padding: 4px;"></div>');

                if ($inputType == 'select' || $inputType == 'selectimg' || $inputType == 'multiselect' || $inputType == 'multiselectimg') {
                    
                    // getting values translations
                    $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                        ->setAttributeFilter($attribute->getId())
                        ->setStoreFilter(Mage::app()->getStore()->getId(), false)
                        ->load();
                    foreach ($valuesCollection as $item) {
                        $values[$item->getId()] = $item->getValue();
                    }
                    
                    // applying translations
                    $options = $attribute->getSource()->getAllOptions(true, true);
                    foreach ($options as $i => $option)
                    {
                        if (isset($values[$option['value']]))
                        {
                            $options[$i]['label'] = $values[$option['value']];
                        }
                        if ($defaultValue == $option['value'])
                        {
                            $options[$i]['default'] = true;
                        }
                    }
                    $element->setValues($options);
                } elseif ($inputType == 'date') {
                	$dateImage = $this->getSkinUrl('images/grid-cal.gif');
                	if ($attribute->getIsReadOnly()) {
                		$dateImage = '';
                	}
                	
                    $element->setImage($dateImage);
                    
                    $element->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                }
            }
        }
        if ($isAnyAttributeApplies)
        {
            $this->_formElements = $form->getElements();
        } else 
        {
            $this->_formElements = array();
        }
        return $this->_formElements;
    }
    
    protected function _toHtml()
    {
        if (!$this->getFormElements()) {
            return '';
        }
        $html = parent::_toHtml();
        $html = str_replace('</label>', '</label><br />', $html);
        return $html;
    }
    
    protected function _isRegistration()
    {
        if ('create' == Mage::app()->getRequest()->getActionName())
        {
            return true;
        }
        return false;
    }
    
    protected function _isCheckout()
    {
        return ('checkout' == Mage::app()->getRequest()->getModuleName());
    }
    
    public function isShowHeader()
    {
        if ($this->_isCheckout())
        {
            return false;
        }
        return true;
    }
}