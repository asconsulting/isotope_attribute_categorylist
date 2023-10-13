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
 * Load tl_iso_product language file for field legends
 */
\System::loadLanguageFile('tl_iso_product');


/**
 * Table tl_iso_attribute
 */
$GLOBALS['TL_DCA']['tl_iso_attribute']['config']['onsubmit_callback'][] = array('\CategoryList\Backend\AttributeOption\CategoryOption', 'saveAttributeCategory');

$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['attributeCategory'] = '{attribute_legend},name,field_name,type,legend,attributeListPage;{description_legend:hide},description;{options_legend},optionsTable;{config_legend},mandatory;{search_filters_legend},fe_filter,fe_sorting';

$GLOBALS['TL_DCA']['tl_iso_attribute']['fields']['attributeListPage'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_iso_attribute']['attributeListPage'],
    'exclude'                   => true,
    'inputType'                 => 'pageTree',
    'foreignKey'                => 'tl_page.title',
    'eval'                      => array('fieldType'=>'radio', 'mandatory'=>true, 'tl_class'=>'clr'),
    'explanation'               => 'jumpTo',
    'sql'                       => "int(10) unsigned NOT NULL default '0'",
    'relation'                  => array('type'=>'hasOne', 'load'=>'lazy'),
);