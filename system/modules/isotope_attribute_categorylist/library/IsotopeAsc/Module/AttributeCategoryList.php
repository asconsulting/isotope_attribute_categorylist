<?php

/**
 * Isotope Attribute Category List
 *
 * Copyright (C) 2019 Andrew Stevens Consulting
 *
 * @package    asconsulting/isotope_attribute_categorylist
 * @link       https://andrewstevens.consulting
 */
 


namespace IsotopeAsc\Module;

use Haste\Generator\RowClass;
use Haste\Http\Response\HtmlResponse;
use Haste\Input\Input;
use Isotope\Isotope;
use Isotope\Model\Attribute;
use Isotope\Model\AttributeOption;
use Isotope\Model\Product;
use Isotope\Model\ProductCache;
use Isotope\Model\ProductType;
use Isotope\Module;
use Isotope\RequestCache\FilterQueryBuilder;
use Isotope\RequestCache\Sort;


/**
 * Isotope\Module\AttributeCategoryList
 */
class AttributeCategoryList extends Contao\Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_iso_attributecategorylist';

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
		die("Module Construct");
       // parent::__construct($objModule, $strColumn);

        //$this->iso_filterModules = deserialize($this->iso_filterModules);
       // $this->iso_productcache  = deserialize($this->iso_productcache);

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

            $objTemplate->wildcard = '### ISOTOPE ECOMMERCE: ATTRIBUTE CATEGORY LIST ###';

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
		die("Module Compile - AttributeCategoryList");
/*
	   $objAttribute = Attribute::findOneBy('id', $this->categoryAttribute);

		if (!$objAttribute || $objAttribute->type != 'attributeCategory') {
			return;
		}
	
		$objOptions = AttributeOption::findByAttribute($objAttribute);
		
		$arrOptions = array();
		while ($objOptions->next()) {
			$objProducts = \Database::getInstance()->prepare("SELECT COUNT(id) AS product_count FROM tl_iso_product WHERE FIND_IN_SET(?, " .$objAttribute->field_name .")")->execute($objOptions->id);
			
			$objImage = \FilesModel::findByUuid(\StringUtil::binToUuid($objOptions->optionImage));
			if ($this->showEmptyCategories == 'yes' || $objProducts->product_count > 0) {
				$arrOptions[] = array('id' => $objOptions->id, 'label' => $objOptions->label, 'image' => ($objImage ? $objImage->path : FALSE), 'alias' => $objAttribute->field_name ."_" .$objOptions->optionAlias, 'description' => $objOptions->optionDescription, 'product_count' => $objProducts->product_count);
			}
		}
		
        $this->Template->categories = $arrOptions;
*/
    }

}
