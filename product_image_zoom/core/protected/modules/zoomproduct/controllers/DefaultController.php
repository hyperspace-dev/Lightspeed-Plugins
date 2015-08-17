<?php
/**
 * @version    1.0.0
 * @since      2013-07-08
 * @author     Argoworks team
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    ...
*/
?>
<?php
class DefaultController extends CController
{
	
	public static function initParams()
	{
		defined('DEFAULT_THEME') or define('DEFAULT_THEME', 'brooklyn2014');

		$params = CHtml::listData(Configuration::model()->findAll(), 'key_name', 'key_value');

		foreach ($params as $key => $value)
		{
			Yii::app()->params->add($key, $value);
		}

		if(isset(Yii::app()->params['THEME']))
		{
			Yii::app()->theme = Yii::app()->params['THEME'];
		} else {
			Yii::app()->theme = DEFAULT_THEME;
		}

		if(isset(Yii::app()->params['LANG_CODE']))
		{
			Yii::app()->language = Yii::app()->params['LANG_CODE'];
		} else {
			Yii::app()->language = "en";
		}

		Yii::app()->params->add('listPerPage', Yii::app()->params['PRODUCTS_PER_PAGE']);

		//Based on logging setting, set log level dynamically and possibly turn on debug mode
		switch (Yii::app()->params['DEBUG_LOGGING'])
		{
			case 'info':
				$logLevel = "error,warning,info";
				break;
			case 'trace':
				$logLevel = "error,warning,info,trace";
				defined('YII_DEBUG') or define('YII_DEBUG', true);
				defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
				break;
			case 'error':
			default:
				$logLevel = "error,warning";
				break;
		}

		foreach(Yii::app()->getComponent('log')->routes as $route)
		{
			$route->levels = $logLevel;
		}

		Yii::app()->setViewPath(Yii::getPathOfAlias('application')."/views-cities");

		Yii::app()->name = Yii::app()->params['STORE_NAME'];

		if(Yii::app()->params['LIGHTSPEED_CLOUD'] == '-1')
		{
			//We should never see this, this means our cloud cache file is bad
			$strHostfile = realpath(dirname(__FILE__)).'/../../../config/cloud/'.$_SERVER['HTTP_HOST'].".php";
			@unlink($strHostfile);
			Yii::app()->request->redirect(Yii::app()->createUrl('site/index'));
		}

	}
	/**
	 * Load anything we need globally, such as items we're going to use in our main.php template.
	 * If you create init() in any other controller, you need to run parent::init() too or this
	 * will be skipped. If you run your own init() and don't call this, you must call Controller::initParams(); 
	 * or nothing will work.
	 */
	public function init()
	{
		self::initParams();

		if(isset($_GET['nosni']))
		{
			Yii::app()->user->setFlash('warning', Yii::t('global', 'NOTE: Your older operating system does not support certain security features this site uses. You have been redirected to {link} for your session which will ensure your information is properly protected.', array('{link}' => "<b>".Yii::app()->params['LIGHTSPEED_HOSTING_LIGHTSPEED_URL']."</b>")));
		}

		$filename = Yii::getPathOfAlias('webroot.themes').DIRECTORY_SEPARATOR.DEFAULT_THEME;
		if(!file_exists($filename) && _xls_get_conf('LIGHTSPEED_MT', 0) == '0')
		{
			if(!downloadTheme(DEFAULT_THEME))
			{
				die("missing ".DEFAULT_THEME);
			}
			else
			{
				$this->redirect(Yii::app()->createUrl("site/index"));
			}
		}

		if(!Yii::app()->theme)
		{
			if(_xls_get_conf('THEME'))
			{
				//We can't find our theme for some reason, switch back to default
				_xls_set_conf('THEME', DEFAULT_THEME);
				_xls_set_conf('CHILD_THEME', 'light');
				Yii::log(
					"Couldn't find our theme, switched back to " . DEFAULT_THEME . " for emergency",
					'error',
					'application.' . __CLASS__ . "." . __FUNCTION__
				);
				$this->redirect(Yii::app()->createUrl('site/index'));
			}
			else
			{
				die("you have no theme set");
			}
		}

		if (isset($_GET['theme']) && isset($_GET['themekey']))
		{
			$strTheme = CHtml::encode($_GET['theme']);
			$strThemeKey = CHtml::encode($_GET['themekey']);

			if ($this->verifyPreviewThemeKey($strTheme, $strThemeKey))
			{
					Yii::app()->theme = $strTheme;
					$this->registerPreviewThemeScript($strTheme, $strThemeKey);
			}
			else
			{
				Yii::log(
					"Invalid theme preview link for" . $strTheme  . ". Navigate to Admin Panel to generate a new link.",
					'error',
					'application.' . __CLASS__ . "." . __FUNCTION__
				);
			}
		}

		$strViewset = Yii::app()->theme->info->viewset;
		if(!empty($strViewset))
		{
			Yii::app()->setViewPath(Yii::getPathOfAlias('application')."/views-".$strViewset);
		}
	}
	
	
	public function actionIndex(){

	}
	public function actionGetmatrixproduct()
	{
		$this->init();
		if(Yii::app()->request->isAjaxRequest) {

			$id = Yii::app()->getRequest()->getParam('id');
			$strSize= Yii::app()->getRequest()->getParam('product_size');
			$strColor= Yii::app()->getRequest()->getParam('product_color');

			$objProduct = Product::LoadChildProduct($id, $strSize, $strColor);


			if ($objProduct instanceof Product)
			{
				$arrReturn['status'] = 'success';
				$arrReturn['id'] = $objProduct->id;
				$arrReturn['FormattedPrice'] = $objProduct->Price;
				$arrReturn['FormattedRegularPrice'] = $objProduct->SlashedPrice;
				$arrReturn['image_id'] = CHtml::image(Images::GetLink($objProduct->image_id, ImagesType::pdetail));
				$arrReturn['code'] = $objProduct->code;
				$arrReturn['title'] = $objProduct->Title;
				$arrReturn['InventoryDisplay'] = $objProduct->InventoryDisplay;

				if ($objProduct->WebLongDescription)
					$arrReturn['description_long'] = $objProduct->WebLongDescription;
				else
					$arrReturn['description_long'] = $objProduct->parent0->WebLongDescription;

				if ($objProduct->description_short)
					$arrReturn['description_short'] = $objProduct->WebShortDescription;
				else
					$arrReturn['description_short'] = $objProduct->parent0->WebShortDescription;
			}
			else
			{
				// options are missing so return the master product

				$objProduct = Product::model()->findByPk($id);

				$arrReturn['FormattedPrice'] = $objProduct->Price;
				$arrReturn['code'] = $objProduct->code;
				$arrReturn['title'] = $objProduct->Title;
				$arrReturn['InventoryDisplay'] = $objProduct->InventoryDisplay;
				if ($objProduct->WebLongDescription)
					$arrReturn['description_long'] = $objProduct->WebLongDescription;
				if ($objProduct->description_short)
					$arrReturn['description_short'] = $objProduct->WebShortDescription;

			}
		
			Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			
			$arrReturn['photos'] = $this->renderPartial('application.views-cities3.product._photosmagic', array('model'=>$objProduct,'FkParentID'=>$id), true,false);
			echo json_encode($arrReturn);
		}

	}
}