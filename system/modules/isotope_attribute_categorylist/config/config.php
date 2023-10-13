<?php

/**
 * Isotope Attribute Category List
 *
 * Copyright (C) 2023 Andrew Stevens Consulting
 *
 * @package    asconsulting/isotope_attribute_categorylist
 * @link       https://andrewstevens.consulting
 */



/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['miscellaneous']['iso_attributecategory_list']		 	= 'CategoryList\Module\AttributeCategoryList'; 
$GLOBALS['FE_MOD']['miscellaneous']['iso_attributecategory_details']		= 'CategoryList\Module\AttributeCategoryDetails'; 
$GLOBALS['FE_MOD']['miscellaneous']['iso_attributecategory_productlist']	= 'CategoryList\Module\AttributeProductList'; 


/**
 * Attributes
 */
\Isotope\Model\Attribute::registerModelType('attributeCategory', 'CategoryList\Model\Attribute\AttributeCategory');
\Isotope\Model\Attribute::registerModelType('attribute', 'CategoryList\Model\Attribute');


/**
 * Backend form fields
 */
$GLOBALS['BE_FFL']['attributeCategory'] = 'CheckBoxWizard';


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = array('CategoryList\Frontend\AttributeCategory', 'loadListPageFromUrl');


/**
 * Models
 */ 
$GLOBALS['TL_MODELS'][\Isotope\Model\Attribute::getTable()] = 'CategoryList\Model\Attribute';