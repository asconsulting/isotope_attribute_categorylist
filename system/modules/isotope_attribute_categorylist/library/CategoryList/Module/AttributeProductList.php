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

use Haste\Generator\RowClass;
use Haste\Http\Response\HtmlResponse;
use Haste\Input\Input;

use Isotope\Isotope;
use Isotope\Model\Attribute;
use Isotope\Model\AttributeOption;
use Isotope\Model\Product;
use Isotope\Model\ProductCache;
use Isotope\Model\ProductType;
use Isotope\Module\Module;
use Isotope\RequestCache\FilterQueryBuilder;
use Isotope\RequestCache\Sort;

use CategoryList\Model\Attribute\AttributeCategory;


/**
 * Isotope\Module\AttributeProductList
 */
class AttributeProductList extends \Isotope\Module\Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_iso_productlist';

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

            $objTemplate->wildcard = '### ISOTOPE ECOMMERCE: ATTRIBUTE PRODUCT LIST ###';

            $objTemplate->title = $this->headline;
            $objTemplate->id    = $this->id;
            $objTemplate->link  = $this->name;
            $objTemplate->href  = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        // Hide product list in reader mode if the respective setting is enabled
        if ($this->iso_hide_list && Input::getAutoItem('product', false, true) != '') {
            return '';
        }

        $this->iso_productcache  = StringUtil::deserialize($this->iso_productcache, true);

        // Disable the cache in frontend preview or debug mode
        if (BE_USER_LOGGED_IN === true || $GLOBALS['TL_CONFIG']['debugMode']) {
            $this->blnCacheProducts = false;
        }

        // Apply limit from filter module
        $this->perPage = Isotope::getRequestCache()
            ->getFirstLimitForModules($this->iso_filterModules, $this->perPage)
            ->asInt()
        ;

        return parent::generate();
    }

    /**
     * Compile product list.
     *
     * This function is specially designed so you can keep it in your child classes and only override findProducts().
     * You will automatically gain product caching (see class property), grid classes, pagination and more.
     */
    protected function compile()
    {
		global $objPage;
		
		$strTitle = '';
		$strDescription = '';
		
		/*
		$pageAlias = \Environment::get('request');
		if (substr($pageAlias, -5) == '.html') {
			$pageAlias = substr($pageAlias, 0, -5);
		}
		*/
		
		$arrUrl = parse_url(\Environment::get('request'));
		$arrPath = pathinfo($arrUrl['path']);
		$pageAlias = $arrPath['filename'];
		
		if (!$pageAlias) {
			return '';
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
			}
		}
				
		$strTitle .= " Products in " .$strAttributeLabel;
		$strTitle = trim($strTitle);
		
		$objPage->pageTitle   = $this->prepareMetaDescription($strTitle);
        $objPage->description = $this->prepareMetaDescription($strDescription);
		
        // return message if no filter is set
        if ($this->iso_emptyFilter && !\Input::get('isorc') && !\Input::get('keywords')) {
            $this->Template->message  = \Controller::replaceInsertTags($this->iso_noFilter);
            $this->Template->type     = 'noFilter';
            $this->Template->products = array();

            return;
        }

        $cacheKey    = $this->getCacheKey($attributeName, $attributeValue);
        $arrProducts = null;
        $arrCacheIds = null;

        // Try to load the products from cache
        if ($this->blnCacheProducts && ($objCache = ProductCache::findByUniqid($cacheKey)) !== null) {
            $arrCacheIds = $objCache->getProductIds();

            // Use the cache if keywords match. Otherwise we will use the product IDs as a "limit" for findProducts()
            if ($objCache->keywords == \Input::get('keywords')) {
                $arrCacheIds = $this->generatePagination($arrCacheIds);

                $objProducts = Product::findAvailableByIds($arrCacheIds, array(
                    'order' => \Database::getInstance()->findInSet(Product::getTable().'.id', $arrCacheIds)
                ));

                $arrProducts = (null === $objProducts) ? array() : $objProducts->getModels();

                // Cache is wrong, drop everything and run findProducts()
                if (count($arrProducts) != count($arrCacheIds)) {
                    $arrCacheIds = null;
                    $arrProducts = null;
                }
            }
        }
		
        if (!is_array($arrProducts)) {
            // Display "loading products" message and add cache flag
            if ($this->blnCacheProducts) {
                $blnCacheMessage = (bool) $this->iso_productcache[$cacheKey];

                if ($blnCacheMessage && !\Input::get('buildCache')) {
                    // Do not index or cache the page
                    $objPage->noSearch = 1;
                    $objPage->cache    = 0;

                    $this->Template          = new \Isotope\Template('mod_iso_productlist_caching');
                    $this->Template->message = $GLOBALS['TL_LANG']['MSC']['productcacheLoading'];

                    return;
                }

                // Start measuring how long it takes to load the products
                $start = microtime(true);

                // Load products
                $arrProducts = $this->findProducts($arrCacheIds, $attributeName, $attributeValueId);


                // Decide if we should show the "caching products" message the next time
                $end = microtime(true) - $start;
                $this->blnCacheProducts = $end > 1 ? true : false;

                $arrCacheMessage = $this->iso_productcache;
                if ($blnCacheMessage != $this->blnCacheProducts) {
                    $arrCacheMessage[$cacheKey] = $this->blnCacheProducts;

                    \Database::getInstance()
                        ->prepare("UPDATE tl_module SET iso_productcache=? WHERE id=?")
                        ->execute(serialize($arrCacheMessage), $this->id)
                    ;
                }

                // Do not write cache if table is locked. That's the case if another process is already writing cache
                if (ProductCache::isWritable()) {
                    \Database::getInstance()
                        ->lockTables(array(ProductCache::getTable() => 'WRITE', 'tl_iso_product' => 'READ'))
                    ;

                    $arrIds = array();
                    foreach ($arrProducts as $objProduct) {
                        $arrIds[] = $objProduct->id;
                    }

                    // Delete existing cache if necessary
                    ProductCache::deleteByUniqidOrExpired($cacheKey);

                    $objCache          = ProductCache::createForUniqid($cacheKey);
                    $objCache->expires = $this->getProductCacheExpiration();
                    $objCache->setProductIds($arrIds);
                    $objCache->save();

                    \Database::getInstance()->unlockTables();
                }
            } else {
                $arrProducts = $this->findProducts(null,  $attributeName, $attributeValueId);
            }

            if (!empty($arrProducts)) {
                $arrProducts = $this->generatePagination($arrProducts);
            }
        }

        // No products found
        if (!is_array($arrProducts) || empty($arrProducts)) {
            $this->compileEmptyMessage();

            return;
        }

        $arrBuffer         = array();
        $arrDefaultOptions = $this->getDefaultProductOptions();

        /** @var \Isotope\Model\Product\Standard $objProduct */
        foreach ($arrProducts as $objProduct) {
            /** @var ProductType $type */
			$type = $objProduct->getRelated('type');

            $arrConfig = array(
                'module'        => $this,
                'template'      => ($this->iso_list_layout ?: $type->list_template),
                'gallery'       => ($this->iso_gallery ?: $type->list_gallery),
                'buttons'       => $this->iso_buttons,
                'useQuantity'   => $this->iso_use_quantity,
                'jumpTo'        => $this->findJumpToPage($objProduct),
            );

            if (\Environment::get('isAjaxRequest')
                && \Input::post('AJAX_MODULE') == $this->id
                && \Input::post('AJAX_PRODUCT') == $objProduct->getProductId()
            ) {
                $objResponse = new HtmlResponse($objProduct->generate($arrConfig));
                $objResponse->send();
            }

            $objProduct->mergeRow($arrDefaultOptions);

            // Must be done after setting options to generate the variant config into the URL
            if ($this->iso_jump_first && Input::getAutoItem('product', false, true) == '') {
                \Controller::redirect($objProduct->generateUrl($arrConfig['jumpTo']));
            }

            $arrCSS = StringUtil::deserialize($objProduct->cssID, true);

            $arrBuffer[] = array(
                'cssID'     => ($arrCSS[0] != '') ? ' id="' . $arrCSS[0] . '"' : '',
                'class'     => trim('product ' . ($objProduct->isNew() ? 'new ' : '') . $arrCSS[1]),
                'html'      => $objProduct->generate($arrConfig),
                'product'   => $objProduct,
            );
        }

        // HOOK: to add any product field or attribute to mod_iso_productlist template
        if (isset($GLOBALS['ISO_HOOKS']['generateProductList'])
            && is_array($GLOBALS['ISO_HOOKS']['generateProductList'])
        ) {
            foreach ($GLOBALS['ISO_HOOKS']['generateProductList'] as $callback) {
                $objCallback = \System::importStatic($callback[0]);
                $arrBuffer   = $objCallback->{$callback[1]}($arrBuffer, $arrProducts, $this->Template, $this);
            }
        }

        RowClass::withKey('class')
            ->addCount('product_')
            ->addEvenOdd('product_')
            ->addFirstLast('product_')
            ->addGridRows($this->iso_cols)
            ->addGridCols($this->iso_cols)
            ->applyTo($arrBuffer)
        ;

        $this->Template->products = $arrBuffer;
    }

    /**
     * Find all products we need to list.
     *
     * @param array|null $arrCacheIds
     *
     * @return array
     */
    protected function findProducts($arrCacheIds = null, $strFieldName = false, $intAttributeValueId = false)
    {
		
        $arrColumns    = array();
        $arrCategories = $this->findCategories();
        $queryBuilder  = new FilterQueryBuilder(
            Isotope::getRequestCache()->getFiltersForModules($this->iso_filterModules)
        );

        $arrColumns[]  = "FIND_IN_SET('" .$intAttributeValueId ."', " .Product::getTable() ."." .$strFieldName .")";
		
        if (!empty($arrCacheIds) && is_array($arrCacheIds)) {
            $arrColumns[] = Product::getTable() . ".id IN (" . implode(',', $arrCacheIds) . ")";
        }

        // Apply new/old product filter
        if ('show_new' === $this->iso_newFilter) {
            $arrColumns[] = Product::getTable() . ".dateAdded>=" . Isotope::getConfig()->getNewProductLimit();
        } elseif ('show_old' === $this->iso_newFilter) {
            $arrColumns[] = Product::getTable() . ".dateAdded<" . Isotope::getConfig()->getNewProductLimit();
        }

        if ($this->iso_list_where != '') {
            $arrColumns[] = $this->iso_list_where;
        }

        if ($queryBuilder->hasSqlCondition()) {
            $arrColumns[] = $queryBuilder->getSqlWhere();
        }

        $arrSorting = Isotope::getRequestCache()->getSortingsForModules($this->iso_filterModules);

        if (empty($arrSorting) && $this->iso_listingSortField != '') {
            $direction = ('DESC' === $this->iso_listingSortDirection ? Sort::descending() : Sort::ascending());
            $arrSorting[$this->iso_listingSortField] = $direction;
        }	
		
        $objProducts = Product::findAvailableBy(
            $arrColumns,
			$queryBuilder->getSqlValues(),
			array(
                'filters' => $queryBuilder->getFilters(),
                'sorting' => $arrSorting
			)
        );
		
		$arrProducts = (null === $objProducts) ? array() : $objProducts->getModels();

		$arrTemp = array();
		$arrIds = array();
		foreach ($arrProducts as $objProduct) {
			if (!in_array($objProduct->id, $arrIds)) {
				$arrIds[] = $objProduct->id;
				$arrTemp[] = $objProduct;
			}
		}
		$arrProducts = $arrTemp;
		
		
        return $arrProducts;
    }

    /**
     * Compile template to show a message if there are no products
     *
     * @param bool $disableSearchIndex
     */
    protected function compileEmptyMessage($disableSearchIndex = true)
    {
        global $objPage;

        // Do not index or cache the page
        if ($disableSearchIndex) {
            $objPage->noSearch = 1;
            $objPage->cache    = 0;
        }

        $message = $this->iso_emptyMessage ? $this->iso_noProducts : $GLOBALS['TL_LANG']['MSC']['noProducts'];
		$message .= " [cat]";

        $this->Template->empty    = true;
        $this->Template->type     = 'empty';
        $this->Template->message  = $message;
        $this->Template->products = array();
    }

    /**
     * Generate the pagination
     *
     * @param array $arrItems
     *
     * @return array
     */
    protected function generatePagination($arrItems)
    {
        $offset = 0;
        $limit  = null;

        // Set the limit
        if ($this->numberOfItems > 0) {
            $limit = $this->numberOfItems;
        }

        $total = count($arrItems);

        // Split the results
        if ($this->perPage > 0 && (!isset($limit) || $limit > $this->perPage)) {
            // Adjust the overall limit
            if (isset($limit)) {
                $total = min($limit, $total);
            }

            // Get the current page
            $id   = 'page_iso' . $this->id;
            $page = \Input::get($id) ? : 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
                global $objPage;

                /** @var \PageError404 $objHandler */
                $objHandler = new $GLOBALS['TL_PTY']['error_404']();
                $objHandler->generate($objPage->id);
                exit;
            }

            // Set limit and offset
            $limit = $this->perPage;
            $offset += (max($page, 1) - 1) * $this->perPage;

            // Overall limit
            if ($offset + $limit > $total) {
                $limit = $total - $offset;
            }

            // Add the pagination menu
            $objPagination = new \Pagination($total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id);
            $this->Template->pagination = $objPagination->generate("\n  ");
        }

        if (isset($limit)) {
            $arrItems = array_slice($arrItems, $offset, $limit);
        }

        return $arrItems;
    }


    /**
     * Get filter & sorting configuration
     *
     * @param boolean
     *
     * @return array
     *
     * @deprecated Deprecated since Isotope 2.3, to be removed in 3.0.
     *             Use Isotope\RequestCache\FilterQueryBuilder instead.
     */
    protected function getFiltersAndSorting($blnNativeSQL = true)
    {
        $arrFilters = Isotope::getRequestCache()->getFiltersForModules($this->iso_filterModules);
        $arrSorting = Isotope::getRequestCache()->getSortingsForModules($this->iso_filterModules);

        if (empty($arrSorting) && $this->iso_listingSortField != '') {
            $direction = ('DESC' === $this->iso_listingSortDirection ? Sort::descending() : Sort::ascending());
            $arrSorting[$this->iso_listingSortField] = $direction;
        }

        if (!$blnNativeSQL) {
            return array($arrFilters, $arrSorting);
        }

        $queryBuilder = new FilterQueryBuilder($arrFilters);

        return array(
            $queryBuilder->getFilters(),
            $arrSorting,
            $queryBuilder->getSqlWhere(),
            $queryBuilder->getSqlValues()
        );
    }

    /**
     * Get a list of default options based on filter attributes
     * @return array
     */
    protected function getDefaultProductOptions()
    {
        $arrFields  = array_merge(Attribute::getVariantOptionFields(), Attribute::getCustomerDefinedFields());

        if (empty($arrFields)) {
            return array();
        }

        $arrOptions = array();
        $arrFilters = Isotope::getRequestCache()->getFiltersForModules($this->iso_filterModules);

        foreach ($arrFilters as $arrConfig) {
            if (in_array($arrConfig['attribute'], $arrFields)
                && ('=' === $arrConfig['operator'] || '==' === $arrConfig['operator'] || 'eq' === $arrConfig['operator'])
            ) {
                $arrOptions[$arrConfig['attribute']] = $arrConfig['value'];
            }
        }

        return $arrOptions;
    }

    /**
     * Generates a unique cache key for the product cache.
     * Child classes should likely overwrite this, see RelatedProducts class for an example.
     *
     * @return string A 32 char cache key (e.g. MD5)
     */
    protected function getCacheKey($strFieldName = false, $strAttributeValue = false)
    {
        $categories = $this->findCategories();

        // Sort categories so cache key is always the same
        sort($categories);

		$strCacheKey = md5(
			'fieldname=' .$strFieldName .':'
			. 'attributevalue=' .$strAttributeValue .':'
            . 'productlist=' . $this->id . ':'
            . 'where=' . $this->iso_list_where . ':'
            . 'isorc=' . (int) \Input::get('isorc') . ':'
            . implode(',', $categories)
        );
		
        return $strCacheKey; 
    }

    /**
     * Returns the timestamp when the product cache expires
     *
     * @return int
     */
    protected function getProductCacheExpiration()
    {
        $time = \Date::floorToMinute();

        // Find timestamp when the next product becomes available
        $expires = (int) \Database::getInstance()
            ->execute("SELECT MIN(start) AS expires FROM tl_iso_product WHERE start>'$time'")
            ->expires
        ;

        // Find
        if ('show_new' === $this->iso_newFilter || 'show_old' === $this->iso_newFilter) {
            $added = \Database::getInstance()
                ->execute("
                    SELECT MIN(dateAdded)
                    FROM tl_iso_product
                    WHERE dateAdded>" . Isotope::getConfig()->getNewProductLimit() . "
                ")
            ;

            if ($added < $expires) {
                $expires = $added;
            }
        }

        return $expires;
    }

	/**
     * Add meta header fields to the current page
     * @param   IsotopeProduct
     */
    protected function addMetaTags($strTitle, $strDescription)
    {
        global $objPage;

        $objPage->pageTitle   = $this->prepareMetaDescription($strTitle);
        $objPage->description = $this->prepareMetaDescription($strDescription);
    }
	
}
