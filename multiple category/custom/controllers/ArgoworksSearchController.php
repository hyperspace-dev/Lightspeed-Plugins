<?php

Yii::import("application.controllers.SearchController");
class ArgoworksSearchController extends SearchController
{
	/**
	 * Used for general category browsing (which is really a search by category or family)
	 * The URL manager passes the category request_url which we look up here
	 */
	public function actionBrowse() {

		$strC = Yii::app()->getRequest()->getQuery('cat');
		$strB = Yii::app()->getRequest()->getQuery('brand');
		$strS = Yii::app()->getRequest()->getQuery('class_name');

		$strInv = '';

		//If we haven't passed any criteria, we just query the database
		$criteria = new CDbCriteria();
		$criteria->alias = 'Product';

		if (!empty($strC))
		{
			$objCategory = Category::LoadByRequestUrl($strC);
			if($objCategory)
			{
				$criteria->join = 'LEFT JOIN xlsws_product_category_assn as ProductAssn ON ProductAssn.product_id=Product.id';
				$intIdArray = array($objCategory->id);
				$intIdArray = array_merge($intIdArray, $objCategory->GetBranchPath());
				
				
				$criteria_new = new CDbCriteria();	
				$criteria_new->addInCondition('category_id', $intIdArray);	
				$criteria_new->addCondition('is_default = 0');
						
				$numberOfRow = Multicategory::model()->count($criteria_new);	
				$rows = Multicategory::model()->findAll($criteria_new);
				$productIds = array();
				foreach($rows as $_row){
					$productIds[] =  $_row->product_id;
				}
				if($numberOfRow>0){
					$criteria->addCondition('ProductAssn.category_id IN ('.implode(",", $intIdArray).') OR Product.id IN ('.implode(",",$productIds).')');
				}else{
					$criteria->addInCondition('ProductAssn.category_id', $intIdArray);
				}

				$this->pageTitle = $objCategory->PageTitle;
				$this->pageDescription = $objCategory->PageDescription;
				$this->pageImageUrl = $objCategory->CategoryImage;
				$this->breadcrumbs = $objCategory->Breadcrumbs;
				$this->pageHeader = Yii::t('category', $objCategory->label);

				$this->subcategories = $objCategory->getSubcategoryTree($this->MenuTree);

				if ($objCategory->custom_page)
				{
					$this->custom_page_content = $objCategory->customPage->page;
				}

				$this->canonicalUrl = $objCategory->getCanonicalUrl();
			}
		}

		if (!empty($strB))
		{
			$objFamily = Family::LoadByRequestUrl($strB);
			if($objFamily)
			{
				$criteria->addCondition('family_id = :id');
				$criteria->params = array (':id' => $objFamily->id);
				$this->pageTitle = $objFamily->PageTitle;
				$this->pageHeader = $objFamily->family;
				$this->canonicalUrl = $objFamily->getCanonicalUrl();
			}
		}

		if (!empty($strS))
		{
			$objClasses = Classes::LoadByRequestUrl($strS);
			if($objClasses)
			{
				$criteria->addCondition('class_id = :id');
				$criteria->params = array (':id' => $objClasses->id);
				$this->pageHeader = $objClasses->class_name;
				$this->canonicalUrl = $objClasses->getCanonicalUrl();
			}
		}

		if (_xls_get_conf('INVENTORY_OUT_ALLOW_ADD') == Product::InventoryMakeDisappear)
			$criteria->addCondition('(inventory_avail>0 OR inventoried=0)');

		if (!_xls_get_conf('CHILD_SEARCH') || empty($strQ))
			$criteria->addCondition('Product.parent IS NULL');

		if (Product::HasFeatured() && empty($strS) && empty($strB) && empty($strC))
		{
			$criteria->addCondition('featured=1');
			$this->pageHeader = 'Featured Products';
		}

		$criteria->addCondition('web=1');
		$criteria->addCondition('current=1');
		$criteria->order = 'Product.'._xls_get_sort_order();

		$productsGrid = new ProductGrid($criteria);

		$this->returnUrl = $this->canonicalUrl;
		$this->pageImageUrl = "";

		if ($strB == '*')
		{
			$familiesCriteria = new CDbCriteria();
			$familiesCriteria->order = 'family';
			if (CPropertyValue::ensureBoolean(Yii::app()->params['DISPLAY_EMPTY_CATEGORY']) === false)
			{
				$familiesCriteria->addCondition('child_count > 0');
			}

			$families = Family::model()->findAll($familiesCriteria);
			$this->render('brands', array('model' => $families));
		}
		else
		{
			if(isset($_GET['viewall']) && $_GET['viewall']==1){
				Yii::app()->params['PRODUCTS_PER_PAGE'] = $productsGrid->getNumberOfRecords();
			}				
	        $this->render(
		        'grid',
		        array(
		            'model' => $productsGrid->getProductGrid(),
		            'item_count' => $productsGrid->getNumberOfRecords(),
		            'page_size' => Yii::app()->params['PRODUCTS_PER_PAGE'],
		            'items_count' => $productsGrid->getNumberOfRecords(),
		            'pages' => $productsGrid->getPages(),
	            )
	        );
		}
	}
}