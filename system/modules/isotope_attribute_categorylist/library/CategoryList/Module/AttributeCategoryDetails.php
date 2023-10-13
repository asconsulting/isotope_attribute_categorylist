<?php

/**
 * Isotope Attribute Category List
 *
 * Copyright (C) 2023 Andrew Stevens Consulting
 *
 * @package    asconsulting/isotope_attribute_categorylist
 * @link       https://andrewstevens.consulting
 */
 


namespace CategoryList\Module;

use Contao\StringUtil;

use Isotope\Module\Module;
use Isotope\Model\Attribute;
use Isotope\Model\AttributeOption;

use CategoryList\Model\Attribute\AttributeCategory;


/**
 * Isotope\Module\AttributeCategoryList
 */
class AttributeCategoryDetails extends \Isotope\Module\Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_iso_attributecategorydetails';

    /**
     * Cache products. Can be disable in a child class, e.g. a "random products list"
     * @var boolean
     *
     * @deprecated Deprecated since version 2.3, to be removed in 3.0.
     *             Implement getCacheKey() to always cache result.
     */
    protected $blnCacheProducts = true;

    /**
     * @inheritdoc
     */
    public function __construct($objModule, $strColumn = 'main')
    {
        parent::__construct($objModule, $strColumn);

        $this->iso_filterModules = StringUtil::deserialize($this->iso_filterModules, true);
        $this->iso_productcache  = StringUtil::deserialize($this->iso_productcache, true);

        if (!is_array($this->iso_filterModules)) {
            $this->iso_filterModules = array();
        }

        if (!is_array($this->iso_productcache)) {
            $this->iso_productcache = array();
        }
    }

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if ('BE' === TL_MODE) {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ISOTOPE ECOMMERCE: ATTRIBUTE CATEGORY DETAILS ###';

            $objTemplate->title = $this->headline;
            $objTemplate->id    = $this->id;
            $objTemplate->link  = $this->name;
            $objTemplate->href  = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function compile()
    {
		global $objPage;
		
		$strTitle = '';
		$strDescription = '';
		
		$pageAlias = \Environment::get('request');
		if (substr($pageAlias, -5) == '.html') {
			$pageAlias = substr($pageAlias, 0, -5);
		}
				
		$objAttribute = \Database::getInstance()->execute("SELECT id, name, field_name, attributeListPage FROM tl_iso_attribute WHERE type LIKE 'attributeCategory' ORDER BY CHAR_LENGTH(field_name) DESC");	
		while($objAttribute->next()) { 
			if (substr($pageAlias, 0, (strlen($objAttribute->field_name) + 1)) == ($objAttribute->field_name ."_")) {
				$attributeName = $objAttribute->field_name;
				$strAttributeLabel = $objAttribute->name;
				$attributeId = $objAttribute->id;
				break 1;
			}
		}
		/*
		$objAttribute = Attribute::findOneBy('id', $attributeId);

		if (!$objAttribute || $objAttribute->type != 'attributeCategory') {
			return;
		}
	
		$objOptions = AttributeOption::findByAttribute($objAttribute);
		*/
		
		$objAttribute = AttributeCategory::findByAttribute($attributeId);
		if (!$objAttribute || $objAttribute->type != 'attributeCategory') {
			return;
		}

		$objResult = \Database::getInstance()->prepare('SELECT id FROM ' .\Isotope\Model\AttributeOption::getTable() .' WHERE pid=?')->execute($objAttribute->id);
		$arrIds = array();
		if ($objResult) {
			while($objResult->next()) {
				$arrIds[] = $objResult->id;
			}
		}
		
		$objOptions = AttributeOption::findPublishedByIds($arrIds);		
		
		$attributeValue = substr($pageAlias, (strlen($attributeName) + 1));
		
		while($objOptions->next()) {
			if ($objOptions->optionAlias == $attributeValue) {
				
				$attributeValueId = $objOptions->id;
				$strDescription = $objOptions->optionDescription;
				$strTitle .= $objOptions->label;
			
				$objImage = \FilesModel::findByUuid(\StringUtil::binToUuid($objOptions->optionImage));
				if ($objImage) {
					$strImage = ($objImage ? $objImage->path : FALSE);
				}
			}
		}

		$strTitle .= " Products in " .$strAttributeLabel;
		$strTitle = trim($strTitle);

        $this->Template->attribute_field_name = $objAttribute->field_name;
        $this->Template->attribute_label = $objAttribute->name;
		$this->Template->attribute_option_title = $strTitle;
		$this->Template->attribute_option_value = $attributeValue;
		$this->Template->attribute_option_description = $strDescription;
		$this->Template->attribute_option_image = $strImage;		
		
    }

}
