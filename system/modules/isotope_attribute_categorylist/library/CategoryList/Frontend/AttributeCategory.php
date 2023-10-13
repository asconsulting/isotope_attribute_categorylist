<?php

/**
 * Isotope Attribute Category List
 *
 * Copyright (C) 2023 Andrew Stevens Consulting
 *
 * @package    asconsulting/isotope_attribute_categorylist
 * @link       https://andrewstevens.consulting
 */
 


namespace CategoryList\Frontend;

use Isotope\Frontend;
use Isotope\Interfaces\IsotopeAttributeWithOptions;
use Isotope\Interfaces\IsotopePrice;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Model\Attribute;
use Isotope\Model\AttributeOption;
use Isotope\Model\Product;
use Isotope\Model\Product\Standard;
use Isotope\Model\ProductCollection\Cart;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\ProductCollectionSurcharge;


/**
 * Class Isotope\AttributeCategory
 */
class AttributeCategory extends Frontend {
	
	public function loadListPageFromUrl($arrFragments)
    {

		if ($objPage = \PageModel::findPublishedByIdOrAlias($arrFragments[0])) {
			return $arrFragments;
		}
		
		$objAttribute = \Database::getInstance()->execute("SELECT field_name, attributeListPage FROM tl_iso_attribute WHERE type LIKE 'attributeCategory' ORDER BY CHAR_LENGTH(field_name) DESC");
			
		while($objAttribute->next()) { 
			if (substr($arrFragments[0], 0, (strlen($objAttribute->field_name) + 1)) == ($objAttribute->field_name ."_")) {
				$objNewPage = \PageModel::findPublishedByIdOrAlias($objAttribute->attributeListPage);
				if ($objNewPage) {
					return array($objNewPage->alias);
				}
			}
		}
        return $arrFragments;
    }
}