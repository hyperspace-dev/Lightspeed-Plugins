<?php
/**
 * @version    1.0.0
 * @since      2013-07-08
 * @author     Argoworks team
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    Zoomproduct
*/
class ZoomproductController extends AdminBaseController{
/*-------------------------------------Global-------------------------------------------*/
	const SETTING= 111;
	const VER= 113;
	public $InfoModule = array(
			'name'=>'Product Zoom Images',
			'version'=>'1.0.8',
			'router'=>'zoomproduct',
			'description'=>'This module allow administrator can upload large size version image product to use for zoom feature on product view detail. ',
			'author'=>'Argoworks',
			'author_url'=>'http://www.argoworks.com',
			'plugin_url'=>'http://www.argoworks.com/downloads/category/lightspeed-ecommerce-3-0-plugins'		
		);
	public function actions()
	{
		return array(
			'edit'=>'admin.edit',
			'upload' => array('class' => 'application.extensions.zoomupload.actions.XUploadAction', 
								'path' => Yii::getPathOfAlias('webroot') . "/images/zoomproduct", 
								"publicPath" => Yii::app()->baseurl . "/images/zoomproduct",),
		);
	}
	public function accessRules()
	{
		return array(
				array('allow',
						'actions'=>array('index','edit','save','list','import','view','getimg','setprimary','install','config','installed','upload'),
						'roles'=>array('admin'),
				),
		);
	}
	/* Check isset db when first run. */
	public function beforeAction($action)
	{
		date_default_timezone_set(_xls_get_conf('TIMEZONE'));
		if(Zoomproduct::CheckSetDb()===false){
			Zoomproduct::model()->SetDb();
		}else{
			Zoomproduct::model()->Checkversion($this->InfoModule['router'],$this->InfoModule['version']);
		}

		$SelfmenuItems =
			array(
					array('label'=>'Manage Products', 'url'=>array('zoomproduct/list'), 'linkOptions'=>array('class'=>'level-1')),
					array('label'=>'Import Product CSV Zoom Images', 'url'=>array('zoomproduct/import'), 'linkOptions'=>array('class'=>'level-1')),
					array('label'=>'Module Settings', 'url'=>array('zoomproduct/edit?id=111'), 'linkOptions'=>array('class'=>'level-1')),
			);
		$this->menuItems =$this->menuItems =Argoworks::model()->createMenuArgo($SelfmenuItems,$this->InfoModule['router']);
		//run parent init() after setting menu so highlighting works
		$css_file = Yii::app()->getRequest()->getBaseUrl().'/css/zoomproduct/style.css';
		Yii::app()->clientScript->registerCssFile($css_file); 		
		return parent::beforeAction($action);

	}
/*-------------------------------------Module-------------------------------------------*/
	public function actionIndex(){
		
		$this->render("index");
	}
	public function actionList(){
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
		$criteria->addCondition('current=1 AND web=1');
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
		$this->render('list',array(
			'model'=> $model, // must be the same as $item_count
			'item_count'=>$item_count,
			'page_size'=>$_default_item_perpage,
			'items_count'=>$item_count,
			'pages'=>$pages,
		));		
	}
	public function actionImport(){
		$result = '';
		ini_set("auto_detect_line_endings", 1); 
		if (isset($_POST['yt0']))
		{
			if($_FILES['csv_file']["tmp_name"]){
				$handle = fopen($_FILES['csv_file']["tmp_name"], 'r');
				$result .= "<span style='width:5% ; float: left;'>Line</span><span style='width:10% ; float: left;'>Product Code</span><span style='width:10% ; float: left;'>Status</span>";
				$result .= '<br>';
				$count=0;
			
				while($data= fgetcsv($handle,100,","))
				{
					$count++;
					$action=true; // Allow or not allow continue handle action.
					list($code,$name,$level)=$data;
					$result .="<span style='width:5% ; float: left;'>".$count."</span>";

					$product= Yii::app()->db->createCommand()->from('xlsws_product')->where('code=:code',array(':code'=>$code))->queryRow();
					$action = false;
					if($product){
						$action = true;
						Yii::app()->db->createCommand("DELETE FROM `xlsws_mod_zoomproduct` WHERE `name`='".$name."' AND `xlsws_mod_zoomproduct`.`product_id` = ".$product['id'])->execute();
						$result .="<span style='width:10% ; float: left;'>Product is found</span>";
					}
					
					//------------Process 3: Check isset img in folder import-------------------
					if($action){
						$pathImg=Yii::getPathOfAlias('webroot').$_POST['dirimg'].$name;
						if (file_exists($pathImg)) { // Check isset images
							if (!file_exists(Yii::getPathOfAlias('webroot').'/images/zoomproduct/')) {
								@mkdir(Yii::getPathOfAlias('webroot').'/images/zoomproduct/');
							}
							$copy = @copy($pathImg, Yii::getPathOfAlias('webroot').'/images/zoomproduct/'.$name);
							if($copy){
								$result .="<span style='width:10% ; float: left;'>OK</span>";
							}else{
								$result .="<span style='width:10% ; color:red; float: left;'>Can't copy image file</span>";
								$action=false; // Stop all action
							}
						}else{
							$result .= "<span style='width:10% ; color:red; float: left;'>Image File doesn't not exists</span>";
							$action=false; // Stop all action
						}
					}else{
							$result .="<span style='width:10% ; color:red; float: left;'>Handle stopped</span>";
					}
					//------------Process 4: Check finish insert to database-------------------
					if($action){
						$insert = Yii::app()->db->createCommand()->insert('xlsws_mod_zoomproduct',array('product_id'=>$product['id'],'name'=>$name,'url'=>'/zoomproduct/'.$name,'size'=>0,'type'=>0,'level'=>$level));
						if($insert){ // Check insert.
							$result .="<span style='width:10% ; float: left;'>Complete</span>";
						}else{
							$result .="<span style='width:10% ; color:red; float: left;'>Error</span>";
						}
					}else{
							$result .="<span style='width:10% ; color:red; float: left;'>Not Imported</span>";
							$action=false; 
					}
					//------------The End: Show result-------------------
					 $result .='<br>';
					
				}
				
				fclose($handle);
				$this->render('import',array('result'=>$result));
			}
		}else{		
			$this->render('import',array('result'=>$result));
		}
	}
	public function actionView(){
		$pid = Yii::app()->getRequest()->getQuery('pid');
		$product = Product::model()->findByPk($pid);
		$rowImg = Yii::app()->db->createCommand()->from('xlsws_mod_zoomproduct')->where('product_id=:product_id', array(':product_id'=>$pid))->queryRow();
		if($rowImg){
			$imgsrc=Zoomproduct::model()->resizeImage($rowImg['url'],_xls_get_conf('DETAIL_IMAGE_WIDTH'),_xls_get_conf('DETAIL_IMAGE_HEIGHT'));
		}
		$this->render('view',array('product_id'=>$pid,'product'=>$product));
	}
	public function actionGetimg(){
		$criteria = new CDbCriteria();
		$criteria->addCondition('product_id='.Yii::app()->getRequest()->getQuery('pid'));
		$model = Zoomproduct::model()->findAll($criteria);
		$repon=array();
		foreach($model as $item){
			$repon[]=array('id'=>$item->id,'name'=>$item->name,'nameimg'=>str_replace(' ','%20',$item->name),'level'=>$item->level,'size'=>$item->size,'url'=>Yii::app()->baseurl.'/images'.str_replace(' ','%20',$item->url),'del_url'=>Yii::app()->baseurl.'/admin/zoomproduct/upload?_method=delete&file='.str_replace(' ','+',$item->name).'&pid='.$item->product_id,);
		}
		print_r(json_encode($repon));
	}
	public function actionSetprimary(){
		$update = Yii::app()->db->createCommand()->update('xlsws_mod_zoomproduct',array('level'=>0),'product_id='.Yii::app()->getRequest()->getQuery('pid')); // Update all to default, not primary
		$update = Yii::app()->db->createCommand()->update('xlsws_mod_zoomproduct',array('level'=>1),'id='.Yii::app()->getRequest()->getQuery('id')); // Update primary
	}
	public function actionEdit()
	{
		$id = Yii::app()->getRequest()->getQuery('id');

		$model = Configuration::model()->findAllByAttributes(array('configuration_type_id'=>$id),array('order'=>'sort_order'));

		if(isset($_POST['Configuration']))
		{
			$valid=true;
			foreach($model as $i=>$item)
			{
				if(isset($_POST['Configuration'][$i]))
					$item->attributes=$_POST['Configuration'][$i];
				$valid=$item->validate() && $valid;
				if (!$valid)
				{
					$err = $item->getErrors();
					Yii::app()->user->setFlash('error',$item->title." -- ".print_r($err['key_value'][0],true));
					break;
				}
			}
			if($valid)  {
				foreach($model as $i=>$item)
				{
					$item->attributes=$_POST['Configuration'][$i];
					if ($item->options=="PASSWORD") $item->key_value=_xls_encrypt($item->key_value);
					if (!$item->save())
						Yii::app()->user->setFlash('error',print_r($item->getErrors(),true));
					else
						$item->postConfigurationChange();

					if($item->key_name=='EMAIL_TEST' && $item->key_value==1)
						$this->sendEmailTest();

				}
				Yii::app()->user->setFlash('success',Yii::t('admin','Configuration updated on {time}.',array('{time}'=>date("d F, Y  h:i:sa"))));



			}
		}


		foreach ($model as $i=>$item)
		{
			if ($item->key_name=="EMAIL_TEST") $item->key_value=0;
			if ($item->options=="BOOL") $this->registerOnOff($item->id,"Configuration_{$i}_key_value",$item->key_value);
			if ($item->options=="PASSWORD") $model[$i]->key_value=_xls_decrypt($model[$i]->key_value);
			$model[$i]->title = Yii::t('admin',$item->title,
				array(
					'{color}'=>_xls_regionalize('color'),
					'{check}'=>_xls_regionalize('check'),
				));
			$model[$i]->helper_text = Yii::t('admin',$item->helper_text,
				array(
					'{color}'=>_xls_regionalize('color'),
					'{check}'=>_xls_regionalize('check'),
				));
		}


		$this->render('edit', array('model'=>$model));


	}
}

//Class DB
