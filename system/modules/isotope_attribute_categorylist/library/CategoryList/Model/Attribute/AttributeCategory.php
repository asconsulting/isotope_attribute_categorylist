<?php

/**
 * Isotope Attribute Category List
 *
 * Copyright (C) 2023 Andrew Stevens Consulting
 *
 * @package    asconsulting/isotope_attribute_categorylist
 * @link       https://andrewstevens.consulting
 */
 


namespace CategoryList\Model\Attribute;

use Isotope\Interfaces\IsotopeAttribute;
use Isotope\Interfaces\IsotopeAttributeWithOptions;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\Attribute;
use Isotope\Model\Attribute\AbstractAttributeWithOptions;


/**
 * Class CategoryList\Model\Attribute\AttributeCategory
 */
class AttributeCategory extends AbstractAttributeWithOptions implements IsotopeAttribute, IsotopeAttributeWithOptions
{

    public function __construct(\Database\Result $objResult = null)
    {
        // This class should not be registered
        // Set type or ModelType would throw an exception
        $this->arrData['type'] = 'attributeCategory';

        parent::__construct($objResult);
    }

    /**
     * Adjust the options wizard for this attribute
     * @return  array
     */
    public function prepareOptionsWizard($objWidget, $arrColumns)
    {
        unset($arrColumns['group']);
        return $arrColumns;
    }

    /**
     * Set SQL field for this attribute
     * @param   array
     */
    public function saveToDCA(array &$arrData)
    {
        parent::saveToDCA($arrData);
        $arrData['fields'][$this->field_name]['inputType'] = "checkboxWizard";
        $arrData['fields'][$this->field_name]['sql'] = "mediumtext NULL";
    }
	
	static function findByAttribute($id) {
		$arrOptions['column'][] = "type='attributeCategory'";
		$arrOptions['column'][] = "id=" .intval($id);
		
		//$objAttribute = AttributeCategory::findValid($arrOptions);
		return static::find($arrOptions);
	}

}
