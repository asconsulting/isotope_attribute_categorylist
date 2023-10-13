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
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'CategoryList\Frontend\AttributeCategory' 				=> 'system/modules/isotope_attribute_categorylist/library/CategoryList/Frontend/AttributeCategory.php',
	'CategoryList\Backend\AttributeOption\CategoryOption' 	=> 'system/modules/isotope_attribute_categorylist/library/CategoryList/Backend/AttributeOption/CategoryOption.php',
	'CategoryList\Model\Attribute' 							=> 'system/modules/isotope_attribute_categorylist/library/CategoryList/Model/Attribute.php',
	'CategoryList\Model\Attribute\AttributeCategory' 		=> 'system/modules/isotope_attribute_categorylist/library/CategoryList/Model/Attribute/AttributeCategory.php',
	'CategoryList\Module\AttributeCategoryList' 			=> 'system/modules/isotope_attribute_categorylist/library/CategoryList/Module/AttributeCategoryList.php',
    'CategoryList\Module\AttributeCategoryDetails' 			=> 'system/modules/isotope_attribute_categorylist/library/CategoryList/Module/AttributeCategoryDetails.php',
	'CategoryList\Module\AttributeProductList' 				=> 'system/modules/isotope_attribute_categorylist/library/CategoryList/Module/AttributeProductList.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'mod_iso_attributecategorylist'			=> 'system/modules/isotope_attribute_categorylist/templates/modules',
	'mod_iso_attributecategorydetails'		=> 'system/modules/isotope_attribute_categorylist/templates/modules',
));
