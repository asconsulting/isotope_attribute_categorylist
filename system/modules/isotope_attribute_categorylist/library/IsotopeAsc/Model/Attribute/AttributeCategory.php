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
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\Attribute;
use Isotope\Model\Attribute\AbstractAttributeWithOptions;


/**
 * Class IsotopeAsc\Model\Attribute\AttributeCategory
 */
class AttributeCategory extends AbstractAttributeWithOptions implements IsotopeAttribute
{

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

}
