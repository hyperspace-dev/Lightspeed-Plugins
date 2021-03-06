<?php
/**
 * @version    1.0.0
 * @since      2013-07-08
 * @author     Argoworks team
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    Zoomproduct
*/
class Zoomproduct extends CActiveRecord
{
/*-------------------------------------Global-------------------------------------------*/
	public function tableName()
    {
        return 'xlsws_mod_zoomproduct';
    }
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public static function CheckSetDb(){
		// Check isset database by try-catch. If don't isset database, code will error then return false
		try{
			Zoomproduct::model()->findAll();
		} 
		catch(exception $e)
		{
		   return false;
		}	
	}
	public static function SetDb(){
		try{
			$sql_install = "CREATE TABLE IF NOT EXISTS `xlsws_mod_zoomproduct` (
							  `id` int(11) NOT NULL AUTO_INCREMENT,
							  `name` varchar(255) NOT NULL,
							  `product_id` int(11) NOT NULL,
							  `url` varchar(255) NOT NULL,
							  `size` int(11) NOT NULL,
							  `level` int(11) NOT NULL,
							  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
							  `modified` datetime DEFAULT NULL,
							  `type` varchar(255) DEFAULT NULL,
							  PRIMARY KEY (`id`)
							) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			Yii::app()->db->createCommand($sql_install)->execute();	
	
			_xls_insert_conf('enable_module_zoom' , 'Enable Module', '1', 'Allow you enable or disable module.', '111', 'BOOL', 1,0);
			_xls_insert_conf('show_title_zoom' , 'Show title zoom image', '1', 'css/zoomproduct/css/image/help-zoom-title.png', '111', 'BOOL', 2,0);
			_xls_insert_conf('show_zoom_area' , 'Enable Zoom', '1', 'Allow show zoom area', '111','BOOL', 3,0);
			_xls_insert_conf('area_zoom_width' , 'Zoom width', '300', 'css/zoomproduct/css/image/help-width-zoom-area.png', '111', '', 4,0);
			_xls_insert_conf('area_zoom_height' , 'Zoom height', '300', 'css/zoomproduct/css/image/help-height-zoom-area.png', '111', '', 5,0);
			_xls_insert_conf('show_zoom_hint' , 'Show zoom hint', '1', 'css/zoomproduct/css/image/help-zoom-hint.png', '111', 'BOOL', 6,0);
			_xls_insert_conf('show_zoom_expand' , 'Enable Expand', '1', 'css/zoomproduct/css/image/help-zoom-expand.png', '111', 'BOOL', 7,0);
			_xls_insert_conf('get_images_parent' , 'Use parent image for child product', '1', 'Use parent image for child product.', '111', 'BOOL', 8,0);
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
				Zoomproduct::model()->Updateversion($router,$version);
			}		
		}
		Zoomproduct::model()->Updateversion($router,$version); 
		return true;
	}
	public static function Updateversion($router,$version){
		// Set config title
		$config = Yii::app()->db->createCommand()->from('xlsws_configuration')->where('key_name=:key_name', array(':key_name'=>'enable_module'))->queryRow();
		if(!$config) _xls_insert_conf('enable_module_zoom' , 'Enable Module', '1', 'Allow you enable or disable module.', '111', 'BOOL', 1,0);	
		// Set config title
		$config = Yii::app()->db->createCommand()->from('xlsws_configuration')->where('key_name=:key_name', array(':key_name'=>'show_title_zoom'))->queryRow();
		if(!$config) _xls_insert_conf('show_title_zoom' , 'Show title zoom image', '1', 'css/zoomproduct/css/image/help-zoom-title.png', '111', 'BOOL', 2,0);
		// Set config zoom area
		$config = Yii::app()->db->createCommand()->from('xlsws_configuration')->where('key_name=:key_name', array(':key_name'=>'show_zoom_area'))->queryRow();
		if(!$config) _xls_insert_conf('show_zoom_area' , 'Enable Zoom', '1', 'Allow zoom when hover on primary image.', '111', 'BOOL', 3,0);
		// Set config zoom width
		$config = Yii::app()->db->createCommand()->from('xlsws_configuration')->where('key_name=:key_name', array(':key_name'=>'area_zoom_width'))->queryRow();
		if(!$config) _xls_insert_conf('area_zoom_width' , 'Zoom Width', '300', 'css/zoomproduct/css/image/help-width-zoom-area.png', '111', '', 4,0);
		// Set config zoom height
		$config = Yii::app()->db->createCommand()->from('xlsws_configuration')->where('key_name=:key_name', array(':key_name'=>'show_zoom_area'))->queryRow();
		if(!$config) _xls_insert_conf('area_zoom_height' , 'Zoom height', '1', 'css/zoomproduct/css/image/help-height-zoom-area.png', '111', '', 5,0);		
		
		// Set config zoom hint
		$config = Yii::app()->db->createCommand()->from('xlsws_configuration')->where('key_name=:key_name', array(':key_name'=>'show_zoom_hint'))->queryRow();
		if(!$config) _xls_insert_conf('show_zoom_hint' , 'Show zoom hint', '1', 'css/zoomproduct/css/image/help-zoom-hint.png', '111', 'BOOL', 6,0);
		// Set config zoom hint
		$config = Yii::app()->db->createCommand()->from('xlsws_configuration')->where('key_name=:key_name', array(':key_name'=>'show_zoom_expand'))->queryRow();
		if(!$config) _xls_insert_conf('show_zoom_expand' , 'Enable Expand', '1', 'css/zoomproduct/css/image/help-zoom-expand.png', '111', 'BOOL', 7,0);
		// Set config get parent image
		$config = Yii::app()->db->createCommand()->from('xlsws_configuration')->where('key_name=:key_name', array(':key_name'=>'get_images_parent'))->queryRow();
		if(!$config) _xls_insert_conf('get_images_parent' , 'Use parent image for child product.', '1', 'Use parent image for child product.', '111', 'BOOL', 8,0);
		$insert = Yii::app()->db->createCommand()->update('xlsws_argoworks',array('version'=>$version),'router="'.$router.'"');
	}
	
/*-------------------------------------Module-------------------------------------------*/

	public static function getprimaryImage($model,$productParentId=null)
	{ 
		$criteria=new CDbCriteria();
		$criteria->addCondition('product_id='.$model->id);
		$objZoom=Zoomproduct::model()->findAll($criteria);
		$countZ=Zoomproduct::model()->count($criteria);
		if($productParentId !=null && $countZ==0 && $model->id!= $productParentId && _xls_get_conf('get_images_parent')){
			$criteria=new CDbCriteria();
			$criteria->addCondition('product_id='.$productParentId);
			$objZoom=Zoomproduct::model()->findAll($criteria);
			$countZ=Zoomproduct::model()->count($criteria);
		}
		$srcprimaryImage='';
		$isLoadedByModuleZoom = false;
		if($countZ>0){
			foreach($objZoom as $itemp){
				if($srcprimaryImage =="")
					$srcprimaryImage = $itemp->url;
				if($itemp->level=='1'){
					$srcprimaryImage=$itemp->url;	
					$isLoadedByModuleZoom = true;
				}
			}
		}else{
			$images = $model->ProductPhotos;
			return $images[0];
		}
		if($srcprimaryImage!=''){
			$primaryImage=self::buildArrImage($srcprimaryImage,$isLoadedByModuleZoom);
			return $primaryImage;
		}else{
			return false;
		}
	}
	public static function getsmallImage($model,$paId=null){
		$criteria=new CDbCriteria();
		$criteria->addCondition('product_id='.$model->id);
		$objZoom=Zoomproduct::model()->findAll($criteria);
		$countZ=Zoomproduct::model()->count($criteria);
		if($paId!=null && $countZ==0 && $model->id != $paId && _xls_get_conf('get_images_parent')){
			$criteria=new CDbCriteria();
			$criteria->addCondition('product_id='.$paId);
			$objZoom=Zoomproduct::model()->findAll($criteria);
			$countZ=Zoomproduct::model()->count($criteria);
		}
		$isLoadedByModuleZoom = false;
		$arrSmallImage=array();
		if($countZ>0){
			foreach($objZoom as $itemp){
				$isLoadedByModuleZoom = true;
				$arrSmallImage[]=self::buildArrImage($itemp->url,$isLoadedByModuleZoom);
			}
		}else{
			$images = $model->ProductPhotos;
			return $images;
		}
		if(count($arrSmallImage)>0){
			return $arrSmallImage;
		}else{
			return false;
		}
	}
	public static function buildArrImage($url,$isLoadedByModuleZoom){
		if($isLoadedByModuleZoom){
			$file_path = Yii::app()->baseurl.'/images/'.$url;
			if(!file_exists(Images::GetImagePath($url))){
				$primaryImage['image_large'] = "http://res.cloudinary.com/lightspeed-retail/image/upload/c_fit,h_".Zoomproduct::getConfig('DETAIL_IMAGE_HEIGHT').",w_".Zoomproduct::getConfig('DETAIL_IMAGE_WIDTH')."/v1389476545/no_product.png";
				$primaryImage['image'] = "http://res.cloudinary.com/lightspeed-retail/image/upload/c_fit,h_".Zoomproduct::getConfig('DETAIL_IMAGE_HEIGHT').",w_".Zoomproduct::getConfig('DETAIL_IMAGE_WIDTH')."/v1389476545/no_product.png";
				$primaryImage['image_thumb'] = "http://res.cloudinary.com/lightspeed-retail/image/upload/c_fit,h_".Zoomproduct::getConfig('DETAIL_IMAGE_HEIGHT').",w_".Zoomproduct::getConfig('DETAIL_IMAGE_WIDTH')."/v1389476545/no_product.png";
				return $primaryImage;
			}
			$primaryImage['image'] = Images::GetImageUri($url,true);
		}else{
			$primaryImage['image'] = self::resizeImage($url,Zoomproduct::getConfig('DETAIL_IMAGE_WIDTH')*1.5,Zoomproduct::getConfig('DETAIL_IMAGE_HEIGHT')*1.5);
		}
		$primaryImage['image_large'] = self::resizeImage($url,Zoomproduct::getConfig('DETAIL_IMAGE_WIDTH'),Zoomproduct::getConfig('DETAIL_IMAGE_HEIGHT'));
		$primaryImage['image_thumb'] = self::resizeImage($url,Zoomproduct::getConfig('PREVIEW_IMAGE_WIDTH'),Zoomproduct::getConfig('PREVIEW_IMAGE_HEIGHT'));
		return $primaryImage;
	}
	
	public static function resizeImage($imagePath,$intNewWidth,$intNewHeight)
	{
		if(strpos($imagePath,'http') !==false){
			return $imagePath;
		}
		//Get our original file from LightSpeed
		$strOriginalFile = $imagePath;
		$strTempThumbnail = Images::GetImageName($strOriginalFile, $intNewWidth, $intNewHeight,'temp');
		$strNewThumbnail = Images::GetImageName($strOriginalFile, $intNewWidth, $intNewHeight);
		if(file_exists(Images::GetImagePath($strNewThumbnail))){
			return Images::GetImageUri($strNewThumbnail,true);
		}
		$strOriginalFileWithPath = Images::GetImagePath($strOriginalFile);
		$strTempThumbnailWithPath = Images::GetImagePath($strTempThumbnail);
		$strNewThumbnailWithPath = Images::GetImagePath($strNewThumbnail);


		$image = Yii::app()->image->load($strOriginalFileWithPath);
		$image->resize($intNewWidth,$intNewHeight)->quality(_xls_get_conf('IMAGE_QUALITY','75'))->sharpen(_xls_get_conf('IMAGE_SHARPEN','20'));



		if (Images::IsWritablePath($strNewThumbnail)) //Double-check folder permissions
		{
			if (_xls_get_conf('IMAGE_FORMAT','jpg') == 'jpg')
			{   $strSaveFunc = 'imagejpeg';
				$strLoadFunc = "imagecreatefromjpeg";
			} else {
				$strSaveFunc = 'imagepng';
				$strLoadFunc = "imagecreatefrompng";
			}

			$image->save($strTempThumbnailWithPath,false);

			try{
				$src = $strLoadFunc($strTempThumbnailWithPath);
				
				//We've saved the resize, so let's load it and resave it centered
				$dst_file = $strNewThumbnailWithPath;
				$dst = imagecreatetruecolor($intNewWidth, $intNewHeight);
				$colorFill = imagecolorallocate($dst, 255,255,255);
				imagefill($dst, 0, 0, $colorFill);
				if (_xls_get_conf('IMAGE_FORMAT','jpg') == 'png')
					imagecolortransparent($dst, $colorFill);

				$arrOrigSize = getimagesize($strOriginalFileWithPath);
				$arrSize = Images::CalculateNewSize($arrOrigSize[0],$arrOrigSize[1], $intNewWidth,$intNewHeight);
				$intStartX = $intNewWidth/2 - ($arrSize[0]/2);
				imagecopymerge($dst, $src, $intStartX, 0, 0, 0, $arrSize[0], $arrSize[1], 100);



				$strSaveFunc($dst, $dst_file);
				@unlink($strTempThumbnailWithPath);
			}catch(Exceiption $e){
			
			}
			return Images::GetImageUri($strNewThumbnail,true);
			
		} else {
			Yii::log("Directory permissions error attempting to save ".$strNewThumbnail, 'error', 'application.'.__CLASS__.".".__FUNCTION__);
			return false;
		}	
	
	}
	public static function getConfig($cfg){
		if(Yii::app()->theme->info->$cfg!='') return Yii::app()->theme->info->$cfg;
		return _xls_get_conf($cfg);
	}
}