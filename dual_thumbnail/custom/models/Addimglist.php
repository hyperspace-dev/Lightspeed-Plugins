<?php
/**
 * @version    1.0.0
 * @since      2013-07-08
 * @author     Argoworks
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    Dualthumb
*/


class Addimglist extends CActiveRecord
{
	public function tableName()
    {
        return 'xlsws_mod_addimglist';
    }
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public static function CheckSetDb(){
		// Check isset database by try-catch. If don't isset database, code will error then return false
		try{
			Addimglist::model()->findAll();
		} 
		catch(exception $e)
		{
		   return false;
		}	
	}
	public static function SetDb(){
		try{
			// Create table 'xlsws_mod_zoomproduct'
			Yii::app()->db->createCommand()->createTable('xlsws_mod_addimglist', array('id'=>'pk', 'name'=>'string NOT NULL', 'code'=>'string NOT NULL','url'=>'string NOT NULL','size'=>'integer NOT NULL','level'=>'integer NOT NULL','type'=>'string NULL',));
			$arrPattern=array('title','key_name','key_value','helper_text','configuration_type_id','sort_order','modified','created','options','template_specific','param','required',);		
			$arrShowProductName=array('Show product name','ar_show_product_name','1','what is help',82,1,'2013-06-06 12:52:39','2013-05-06 00:00:00','BOOL',0,1,'NULL',);		
			$arrShowProductPrice=array('Show product price','ar_show_product_price','1','what is help',82,1,'2013-06-06 12:52:39','2013-05-06 00:00:00','BOOL',0,1,'NULL',);		
			$arrShowProductFamily=array('Show product family','ar_show_product_family','1','what is help',82,1,'2013-06-06 12:52:39','2013-05-06 00:00:00','BOOL',0,1,'NULL',);		
			$arrUseInfoEffect=array('Use info effect','ar_use_info_effect','1','what is help',82,1,'2013-06-06 12:52:39','2013-05-06 00:00:00','BOOL',0,1,'NULL',);		
			$arrCShowProductName=array_combine($arrPattern,$arrShowProductName);
			$arrCShowProductPrice=array_combine($arrPattern,$arrShowProductPrice);
			$arrCShowProductFamily=array_combine($arrPattern,$arrShowProductFamily);
			$arrCUseInfoEffect=array_combine($arrPattern,$arrUseInfoEffect);
			// Insert to Db
			$insert = Yii::app()->db->createCommand()->insert('xlsws_configuration',$arrCShowProductName);
			$insert = Yii::app()->db->createCommand()->insert('xlsws_configuration',$arrCShowProductPrice);
			$insert = Yii::app()->db->createCommand()->insert('xlsws_configuration',$arrCShowProductFamily);
			$insert = Yii::app()->db->createCommand()->insert('xlsws_configuration',$arrCUseInfoEffect);
		}catch(exception $e)
			{
				Yii::app()->user->setFlash('error',Yii::t('admin','Create database error. Database can existed.'));
			   return false;
			}
	}
	public function Checkversion($router,$version){
		$criteria=new CDbCriteria();
		$criteria->addCondition('router="'.$router.'"');
		$models= Argoworks::model() -> findAll($criteria);
		foreach($models as $model){
			if(str_replace('.','',$version)>str_replace('.','',$model->version)){
				//Code install version
				return false;
			}		
		}
		return true;
	}
/*-------------------------------------Module-------------------------------------------*/

	public function isAssigned($_productid,$categoryid){
		$criteria = new CDbCriteria();
		$criteria->addCondition('code='.$_productid.' AND category_id='.$categoryid.'');
		$is_exsit = ProductCategoryAssn::model()->count($criteria);
		if($is_exsit>0){
			return true;
		}else{
			return false;
		}
	}
}
?>