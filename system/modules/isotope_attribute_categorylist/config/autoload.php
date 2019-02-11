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
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'IsotopeAsc\Frontend\AttributeCategory' 			=> 'system/modules/isotope_attribute_categorylist/library/IsotopeAsc/Frontend/AttributeCategory.php',
	'IsotopeAsc\Backend\AttributeOption\CategoryOption' => 'system/modules/isotope_attribute_categorylist/library/IsotopeAsc/Backend/AttributeOption/CategoryOption.php',
	'IsotopeAsc\Model\Attribute' 						=> 'system/modules/isotope_attribute_categorylist/library/IsotopeAsc/Model/Attribute.php',
	'IsotopeAsc\Model\Attribute\AttributeCategory' 		=> 'system/modules/isotope_attribute_categorylist/library/IsotopeAsc/Model/Attribute/AttributeCategory.php',
	'IsotopeAsc\Module\AttributeCategoryList' 			=> 'system/modules/isotope_attribute_categorylist/library/IsotopeAsc/Module/AttributeCategoryList.php',
    'IsotopeAsc\Module\AttributeCategoryDetails' 		=> 'system/modules/isotope_attribute_categorylist/library/IsotopeAsc/Module/AttributeCategoryDetails.php',
	'IsotopeAsc\Module\AttributeProductList' 			=> 'system/modules/isotope_attribute_categorylist/library/IsotopeAsc/Module/AttributeProductList.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'mod_iso_attributecategorylist'			=> 'system/modules/isotope_attribute_categorylist/templates/modules',
	'mod_iso_attributecategorydetails'		=> 'system/modules/isotope_attribute_categorylist/templates/modules',
));
