<?php
/**
 * @version    1.0.0
 * @since      2013-07-08
 * @author     Argoworks
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    dualthumbnail 
*/
class AddimglistController extends AdminBaseController{
	const SETTING= 151;
	const VER= 153;
	public $InfoModule = array(
			'name'=>'Dual Thumbnail Image',
			'version'=>'1.0.0',
			'router'=>'addimglist',
			'description'=>'This plugin allow administrator of site can upload an second image to use it as second thumbnail image on product listing page.',
			'author'=>'Argoworks',
			'author_url'=>'http://www.argoworks.com',
			'plugin_url'=>'http://www.argoworks.com/downloads/category/lightspeed-ecommerce-3-0-plugins'		
		);
	public function actions()
	{
		return array(
			'edit'=>'admin.edit',
			'upload' => array('class' => 'application.extensions.addimgupload.actions.XUploadAction', 
								'path' => Yii::getPathOfAlias('webroot') . "/images/addimglist", 
								"publicPath" => Yii::app()->baseurl . "/images/addimglist",)
		);
	}
	public function accessRules()
	{
		return array(
				array('allow',
						'actions'=>array('index','save','list','import','view','getimg','setprimary','install','config','installed','upload'),
						'roles'=>array('admin'),
				),
		);
	}
	public function beforeAction($action)
	{
		date_default_timezone_set(_xls_get_conf('TIMEZONE'));
		if(Addimglist::CheckSetDb()===false){
			Addimglist::model()->SetDb();
		}else{
			Addimglist::model()->Checkversion($this->InfoModule['router'],$this->InfoModule['version']);
		}
		if(!Argoworks::model()->checkLicense($this->InfoModule['router'])){
			$this->redirect($this->createUrl('argoworks/license?router='.$this->InfoModule['router'])); // Check license . If fail will ridirect to activate
		}
		$SelfmenuItems =
			array(
					array('label'=>'Thumbnail Products', 'url'=>array('addimglist/list'), 'linkOptions'=>array('class'=>'level-1')),
					array('label'=>'Import Dual Thumbnail', 'url'=>array('addimglist/import'), 'linkOptions'=>array('class'=>'level-1')),
				
			);
		$this->menuItems =$this->menuItems =Argoworks::model()->createMenuArgo($SelfmenuItems,$this->InfoModule['router']);
		return parent::beforeAction($action);

	}
	public function actionIndex(){
		$this->render('index');
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
		$this->render('list',array(
			'model'=> $model, 
			'item_count'=>$item_count,
			'page_size'=>$_default_item_perpage,
			'items_count'=>$item_count,
			'pages'=>$pages,
		));		
	}
	public function actionImport(){
		if (isset($_POST['yt0']))
		{
			if($_FILES['csv_file']["tmp_name"]){
				$handle = fopen($_FILES['csv_file']["tmp_name"], 'r');
				$result="<span style='width:5% ; float: left;'>Line</span><span style='width:15% ; float: left;'>Product Code</span><span style='width:10% ; float: left;'>Status Product</span><span style='width:10% ; float: left;'>Insert Images</span><span style='width:10% ; float: left;'>Insert Database</span>";
				echo $result.'<br>';
				$count=0;
				echo '<html><head><script src="http://code.jquery.com/jquery-1.9.1.js"></script></head><body>'; //Insert script
				while($data= fgetcsv($handle,100,","))
				{
					$result="";
					$count++;
					$action=true; // Allow or not allow continue handle action.
					list($code,$name,$size,$level,$type)=$data;
					$result="<span style='width:5% ; float: left;'>".$count."</span>";
					//------------Process 1: Check isset product available img-------------------
					$idproduct=Yii::app()->db->createCommand()->from('xlsws_mod_addimglist')->where('code=:code',array(':code'=>$code))->queryRow();
					if(!$idproduct){ // Check isset product in xlsws_mod_addimglist.
						$result.="<span style='width:15% ; float: left;'>".$code."</span>";
					}else{
						$result.="<span style='width:15% ; color:red; float: left;'>".$code."(Duplicate)</span>";
						$action=false; // Stop all action
					}
					//------------Process 2: Check isset product database-------------------
					if($action){
						$product=Yii::app()->db->createCommand()->from('xlsws_product')->where('code=:code',array(':code'=>$code))->queryRow();
						if($product){ // Check isset product in database.
							$result.="<span style='width:10% ; float: left;'>Found</span>";
						}else{
							$result.="<span style='width:10% ; color:red; float: left;'>Not Found</span>";
							$action=false; // Stop all action
						}
					}else{
							$result.="<span style='width:10% ; color:red; float: left;'>Handle stopped</span>";
					}
					//------------Process 3: Check isset img in folder import-------------------
					if($action){
						$pathImg=Yii::getPathOfAlias('webroot').$_POST['dirimg'].$name;
						if (file_exists($pathImg)) { // Check isset images
							if (!file_exists(Yii::getPathOfAlias('webroot').'/images/addimglist/')) {
								mkdir(Yii::getPathOfAlias('webroot').'/images/addimglist/');
							}
							$copy=copy($pathImg, Yii::getPathOfAlias('webroot').'/images/addimglist/'.$name);
							if($copy){
								$result.="<span style='width:10% ; float: left;'>OK</span>";
							}else{
								$result.="<span style='width:10% ; color:red; float: left;'>Copy Error</span>";
								$action=false; // Stop all action
							}
						}else{
							$result.="<span style='width:10% ; color:red; float: left;'>File not exists</span>";
							$action=false; // Stop all action
						}
					}else{
							$result.="<span style='width:10% ; color:red; float: left;'>Handle stopped</span>";
					}
					//------------Process 4: Check finish insert to database-------------------
					if($action){
						$insert = Yii::app()->db->createCommand()->insert('xlsws_mod_addimglist',array('code'=>$code,'name'=>$name,'url'=>'/addimglist/'.$name,'size'=>$size,'type'=>$type,'level'=>$level));
						if($insert){ // Check insert.
							$result.="<span style='width:10% ; float: left;'>Complete</span>";
						}else{
							$result.="<span style='width:10% ; color:red; float: left;'>Error</span>";
						}
					}else{
							$result.="<span style='width:10% ; color:red; float: left;'>Handle stopped</span>";
							$action=false; // Stop all action
					}
					//------------The End: Show result-------------------
					echo $result.'<br>';
					echo '<script> $(document).scrollTop($(document).height()); </script>'; // Scroll to bottom.
				}
				
				fclose($handle);
			}
		}else{		
			$this->render('import');
		}
	}
	public function actionView(){
		$code = Yii::app()->getRequest()->getQuery('code');
		$product = Product::model()->findByAttributes(array('code'=>$code));
		$this->render('view',array('code'=>$code,'product'=>$product));
	}
	public function actionGetimg(){
		$criteria = new CDbCriteria();
		$criteria->addCondition('code="'.str_replace('%20',' ',Yii::app()->getRequest()->getQuery('code')).'"');
		$model = Addimglist::model()->findAll($criteria);
		$repon=array();
		foreach($model as $item){
			$repon[]=array('id'=>$item->id,'name'=>$item->name,'nameimg'=>str_replace(' ','%20',$item->name),'level'=>$item->level,'size'=>$item->size,'url'=>Yii::app()->baseurl.'/images'.str_replace(' ','%20',$item->url),'del_url'=>Yii::app()->baseurl.'/admin/addimglist/upload?_method=delete&file='.str_replace(' ','+',$item->name).'&code='.str_replace(' ','+',$item->code).'&module=addimglist',);
		}
		print_r(json_encode($repon));
	}
	public function actionSetprimary(){
		$update = Yii::app()->db->createCommand()->update('xlsws_mod_addimglist',array('level'=>0),'code='.Yii::app()->getRequest()->getQuery('code')); // Update all to default, not primary
		$update = Yii::app()->db->createCommand()->update('xlsws_mod_addimglist',array('level'=>1),'id='.Yii::app()->getRequest()->getQuery('id')); // Update primary
	}

}