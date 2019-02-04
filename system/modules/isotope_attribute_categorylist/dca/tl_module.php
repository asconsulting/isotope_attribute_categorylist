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
 * Palettes
 */ 
$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_attributecategory_list'] = '{title_legend},name,headline,type;{config_legend},categoryAttribute,showEmptyCategories;{redirect_legend},iso_jump_first;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_attributecategory_details'] = '{title_legend},name,headline,type;{config_legend},categoryAttribute;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_attributecategory_productlist'] = '{title_legend},name,headline,type;{config_legend},categoryAttribute,numberOfItems,perPage,iso_category_scope,iso_list_where,iso_newFilter,iso_filterModules,iso_listingSortField,iso_listingSortDirection;{redirect_legend},iso_addProductJumpTo,iso_jump_first;{reference_legend:hide},defineRoot;{template_legend:hide},customTpl,iso_list_layout,iso_gallery,iso_cols,iso_use_quantity,iso_hide_list,iso_includeMessages,iso_emptyMessage,iso_emptyFilter,iso_buttons;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['categoryAttribute'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['categoryAttribute'],
	'inputType'               => 'select',
	'foreignKey'              => 'tl_iso_attribute.name',
	'eval'                    => array('doNotCopy'=>true, 'chosen'=>true, 'mandatory'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'",
	'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['showEmptyCategories'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showEmptyCategories'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('yes', 'no'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('includeBlankOption'=>false, 'tl_class'=>'w50'),
	'sql'                     => "varchar(8) NOT NULL default ''"
);
