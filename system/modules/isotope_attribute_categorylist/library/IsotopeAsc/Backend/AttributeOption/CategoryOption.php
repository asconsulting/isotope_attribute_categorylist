<?php

/**
 * Isotope Attribute Category List
 *
 * Copyright (C) 2019 Andrew Stevens Consulting
 *
 * @package    asconsulting/isotope_attribute_categorylist
 * @link       https://andrewstevens.consulting
 */
 


namespace IsotopeAsc\Backend\AttributeOption;

use Isotope\Model\Attribute;
use Isotope\Model\AttributeOption;


/**
 * Class Isotope\Backend\AttributeOption\CategoryOption
 */
class CategoryOption extends \Backend
{
	
	public function loadLabel($varValue, $dc) {
		$objAttribute = Attribute::findOneBy('id', $dc->activeRecord->pid);
		if ($objAttribute->type == 'attributeCategory') { 
			$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['label']['eval']['tl_class'] = 'clr w50';
		}
		return $varValue;
	}
	
	public function loadOptionImage ($varValue, $dc) {
		$objAttribute = Attribute::findOneBy('id', $dc->activeRecord->pid);
		if ($objAttribute->type != 'attributeCategory') { 
			$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['optionImage']['inputType'] = FALSE;
			return '';
		}
		return $varValue;
	}
	
	public function loadOptionAlias ($varValue, $dc) {
		$objAttribute = Attribute::findOneBy('id', $dc->activeRecord->pid);
		if ($objAttribute->type != 'attributeCategory') { 
			$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['optionAlias']['inputType'] = FALSE;
			return '';
		}
		return $varValue;
	}
	
	public function loadOptionDescription ($varValue, $dc) {
		$objAttribute = Attribute::findOneBy('id', $dc->activeRecord->pid);
		if ($objAttribute->type != 'attributeCategory') { 
			$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['optionDescription']['inputType'] = FALSE;
			return '';
		}
		return $varValue;
	}	
	
	public function saveAttributeCategory($dc) {
		if ($dc->activeRecord->type == 'attributeCategory') {
			\Database::getInstance()->prepare("UPDATE tl_iso_attribute SET optionsSource = 'table', multiple = 1 WHERE id=?")->execute($dc->activeRecord->id);
		}
	}
	
}
