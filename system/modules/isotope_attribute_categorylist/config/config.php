<?php

/**
 * Isotope Attribute Category List
 *
 * Copyright (C) 2019 Andrew Stevens Consulting
 *
 * @package    asconsulting/isotope_attribute_categorylist
 * @link       https://andrewstevens.consulting
 */



/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['miscellaneous']['iso_attributecategory_list']		 	= 'IsotopeAsc\Module\AttributeCategoryList'; 
$GLOBALS['FE_MOD']['miscellaneous']['iso_attributecategory_details']		= 'IsotopeAsc\Module\AttributeCategoryDetails'; 
$GLOBALS['FE_MOD']['miscellaneous']['iso_attributecategory_productlist']	= 'IsotopeAsc\Module\AttributeProductList'; 


/**
 * Attributes
 */
\Isotope\Model\Attribute::registerModelType('attributeCategory', 'IsotopeAsc\Model\Attribute\AttributeCategory');
\Isotope\Model\Attribute::registerModelType('attribute', 'IsotopeAsc\Model\Attribute');


/**
 * Backend form fields
 */
$GLOBALS['BE_FFL']['attributeCategory'] = 'CheckBoxWizard';


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = array('IsotopeAsc\Frontend\AttributeCategory', 'loadListPageFromUrl');


/**
 * Models
 */ 
$GLOBALS['TL_MODELS'][\Isotope\Model\Attribute::getTable()] = 'IsotopeAsc\Model\Attribute';