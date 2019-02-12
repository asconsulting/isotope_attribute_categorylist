<?php

/**
 * Isotope Attribute Category List
 *
 * Copyright (C) 2019 Andrew Stevens Consulting
 *
 * @package    asconsulting/isotope_attribute_categorylist
 * @link       https://andrewstevens.consulting
 */
 


namespace IsotopeAsc\Model\Attribute;

use Isotope\Interfaces\IsotopeAttribute;
use Isotope\Interfaces\IsotopeAttributeWithOptions;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\Attribute;
use Isotope\Model\Attribute\AbstractAttributeWithOptions;


/**
 * Class IsotopeAsc\Model\Attribute\AttributeCategory
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
	
	static function findByPk($id) {
		parent::findByPk($id);
	}

}
