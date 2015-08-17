<?php
/**
 * @version    1.0.0
 * @since      2013-07-05
 * @author     Pham Ba Quyet-Argoworks
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    ...
*/
?>
<?php
class ZoomproductModule extends CWebModule
{
	public function init()
	{
	$this->setImport(array(
		'zoomproduct.models.*',
		'zoomproduct.components.*',
	));
	//$this->layout='application.views.layouts.column1';
	}
}