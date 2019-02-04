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
 * Table tl_iso_attribute_option
 */
$GLOBALS['TL_DCA']['tl_iso_attribute_option']['palettes']['option'] = str_replace('label;', 'label,optionAlias,optionDescription,optionImage;', $GLOBALS['TL_DCA']['tl_iso_attribute_option']['palettes']['option']);

$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['label']['load_callback'][] = array('\IsotopeAsc\Backend\AttributeOption\CategoryOption', 'loadLabel');

$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['optionImage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_attribute_option']['optionImage'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'mandatory'=>true, 'tl_class'=>'clr', 'extensions'=>Config::get('validImageTypes')),
	'sql'                     => "binary(16) NULL",
	'load_callback' 		  => array(array('\IsotopeAsc\Backend\AttributeOption\CategoryOption', 'loadOptionImage'))
);

$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['optionAlias'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_attribute_option']['optionAlias'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'alias', 'mandatory'=>true, 'tl_class'=>'w50', 'doNotCopy'=>true, 'maxlength'=>128),
	'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''",
	'load_callback' 		  => array(array('\IsotopeAsc\Backend\AttributeOption\CategoryOption', 'loadOptionAlias'))
);

$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['optionDescription'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_attribute_option']['optionDescription'],
	'exclude'                 => true,
	'inputType'               => 'textarea',
	'eval'                    => array('tl_class'=>'clr', 'rte'=>'tinyMCE', 'helpwizard'=>true), 
	'explanation'             => 'insertTags',
	'sql'                     => "mediumtext NULL",
	'load_callback' 		  => array(array('\IsotopeAsc\Backend\AttributeOption\CategoryOption', 'loadOptionDescription'))
);