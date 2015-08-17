<?php
/**
 * @version    1.0.0
 * @since      2013-07-08
 * @author     Argoworks
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    Multicategory
*/
?>
<?php

class Multicategory extends CActiveRecord
{
	public function tableName()
    {
        return 'xlsws_mod_multicategory';
    }
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public static function CheckSetDb(){
		try{
			Multicategory::model()->findAll();
		} 
		catch(exception $e)
		{
		   return false;
		}	
	}
	public static function SetDb(){
		try{
			Yii::app()->db->createCommand()->createTable('xlsws_mod_multicategory', array('product_id'=>'integer NOT NULL', 'category_id'=>'integer NOT NULL',));
		}catch(exception $e)
			{
				Yii::app()->user->setFlash('error',Yii::t('admin','Create database error. Database can existed.'));
			   return false;
			}
	}
	public static function upgradeDataBase(){
		$res1 = Yii::app()->db->createCommand("SHOW COLUMNS FROM xlsws_mod_multicategory WHERE Field='is_default'")->execute();
		if(!$res1)
			Yii::app()->db->createCommand("ALTER TABLE `xlsws_mod_multicategory` ADD `is_default` INT( 11 ) NOT NULL DEFAULT '0'")->execute();
			
		$res2 = Yii::app()->db->createCommand("SHOW COLUMNS FROM xlsws_mod_multicategory WHERE Field='is_assigned'")->execute();
		if(!$res2)
			Yii::app()->db->createCommand("ALTER TABLE `xlsws_mod_multicategory` ADD `is_assigned` INT( 11 ) NOT NULL DEFAULT '1'")->execute();			
		
	}	
	public function Checkversion($router,$version){
		$criteria=new CDbCriteria();
		$criteria->addCondition('router="'.$router.'"');
		$models= Argoworks::model() -> findAll($criteria);
		foreach($models as $model){
			if(str_replace('.','',$version)>str_replace('.','',$model->version)){
				return false;
			}		
		}
		return true;
	}
	
	
}


