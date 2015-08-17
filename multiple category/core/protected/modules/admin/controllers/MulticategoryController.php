<?php
/**
 * @author     Argoworks team
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    Multiple Categories
*/
class MulticategoryController extends AdminBaseController{
	const SETTING= 111;
	const VER= 113;
	public $InfoModule = array(
			'name'=>'Multiple Categories',
			'version'=>'2.0',
			'router'=>'multicategory',
			'description'=>'The Multiple Categories Module gives you the option to add products from LightSpeed into multiple categories on your web store through our easy to use web based interface.',
			'author'=>'Argoworks',
			'author_url'=>'http://www.argoworks.com',
			'plugin_url'=>'http://www.argoworks.com/downloads/multiple-categories-module'		
		);
	public function actions()
	{
		return array(
			'edit'=>'admin.edit',
		);
	}
	public function accessRules()
	{
		return array(
				array('allow',
						'actions'=>array('index','save','view','install','config','installed','upload','synctable'),
						'roles'=>array('admin'),
				),
		);
	}
	
	protected function upgradeDataBase(){
		$res1 = Yii::app()->db->createCommand("SHOW COLUMNS FROM xlsws_mod_multicategory WHERE Field='is_default'")->execute();
		if(!$res1)
			Yii::app()->db->createCommand("ALTER TABLE `xlsws_mod_multicategory` ADD `is_default` INT( 11 ) NOT NULL DEFAULT '0'")->execute();
			
		$res2 = Yii::app()->db->createCommand("SHOW COLUMNS FROM xlsws_mod_multicategory WHERE Field='is_assigned'")->execute();
		if(!$res2)
			Yii::app()->db->createCommand("ALTER TABLE `xlsws_mod_multicategory` ADD `is_assigned` INT( 11 ) NOT NULL DEFAULT '1'")->execute();			
		
	}
	
	/* Check isset db when first run. */
	public function beforeAction($action)
	{
		date_default_timezone_set(_xls_get_conf('TIMEZONE'));
		if(Multicategory::CheckSetDb()===false){
			Multicategory::model()->SetDb();
		}else{
			Multicategory::model()->Checkversion($this->InfoModule['router'],$this->InfoModule['version']);
		}
		$this->upgradeDataBase();
		$SelfmenuItems =array();
		$this->menuItems =$this->menuItems =Argoworks::model()->createMenuArgo($SelfmenuItems,$this->InfoModule['router']);
		return parent::beforeAction($action);

	}
/*-------------------------------------Module-------------------------------------------*/
	public function actionIndex(){
		$_default_item_perpage = 10;
		if(isset($_GET['limit']) && (int)$_GET['limit'] >0){
			Yii::app()->session['current'] = $_GET['limit'];
			$_default_item_perpage = Yii::app()->session['current'];
		}else{
			if(isset(Yii::app()->session['current'])){
				$_default_item_perpage = Yii::app()->session['current'];
			}else{
				Yii::app()->session['current'] = $_default_item_perpage;
			}
		}

		$criteria = new CDbCriteria();
		$criteria->addCondition('current=1 AND web=1 AND parent IS NULL');
		$q = Yii::app()->getRequest()->getQuery('q');
		if($q){
			$criteria->addCondition('`title` LIKE "%'.$q.'%" OR `code` LIKE "%'.$q.'%" OR `description_short` LIKE "%'.$q.'%"');
		}
		
		$criteria->order = _xls_get_sort_order();
		$item_count = Product::model()->count($criteria);
		$pages = new CPagination($item_count);
		$pages->setPageSize($_default_item_perpage);
		$pages->applyLimit($criteria);
		$model = Product::model()->findAll($criteria);
		$this->render('index',array(
			'model'=> $model, // must be the same as $item_count
			'item_count'=>$item_count,
			'page_size'=>$_default_item_perpage,
			'items_count'=>$item_count,
			'pages'=>$pages,
		));		
		
	}

	public function isAssigned($_productid,$categoryid){
		$criteria = new CDbCriteria();
		$criteria->addCondition('product_id='.$_productid.' AND category_id='.$categoryid.'');
		$is_exsit = Multicategory::model()->count($criteria);
		
		if($is_exsit>0){
			return true;
		}else{
			return false;
		}
	}
	
	public function isAssignedByPOS($_productid,$categoryid){
		$criteria = new CDbCriteria();
		$criteria->addCondition('product_id='.$_productid.' AND category_id='.$categoryid.'');
		$is_exsit = ProductCategoryAssn::model()->count($criteria);
		
		if($is_exsit>0){
			return true;
		}else{
			return false;
		}
	}	
	
	public function getProductCategoryIds($productid){
		$criteria = new CDbCriteria();
		$criteria->addCondition('product_id='.$productid);
		$is_exsit = Multicategory::model()->count($criteria);	
		$categoryIds = array();
		if($is_exsit>0){
			$categories = Multicategory::model()->findAll($criteria);
			foreach($categories as $cat){
				$categoryIds[] = $cat->category_id;
			}
		}
		return $categoryIds;
	}
	


	public function actionView(){
		$pid = Yii::app()->getRequest()->getQuery('pid');
		$product = Product::model()->findByPk($pid);
		$this->render('view',array('product_id'=>$pid,'product'=>$product));
	}
	
	public function actionSave(){
		$selected_categories = Yii::app()->getRequest()->getParam('categories');
		$_productid = Yii::app()->getRequest()->getParam('pid');
		$page = Yii::app()->getRequest()->getParam('page');
		if($_productid !=""){
			$delete = Yii::app()->db->createCommand()->delete('xlsws_mod_multicategory','`product_id`='.$_productid); 
		}

		if(count($selected_categories)>0){
			foreach($selected_categories as $key=>$_category){
				
				$criteria_new = new CDbCriteria();
				$criteria_new->addCondition('product_id='.$_productid.' AND category_id='.$_category.'');
				$is_exsit = Multicategory::model()->count($criteria_new);	
				$is_assigned_from_pos = $this->isAssignedByPOS($_productid,$_category);
				if(!$is_exsit && !$is_assigned_from_pos){
					$insert = Yii::app()->db->createCommand()->insert('xlsws_mod_multicategory',array('product_id'=>$_productid,'category_id'=>$_category));
				}
			}
		}

		$this->redirect($this->createUrl('multicategory/index',array('page'=>$page)));
		
	}
	
}
