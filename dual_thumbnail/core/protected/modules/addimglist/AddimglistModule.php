<?php
/**
 * @version    1.0.0
 * @since      2013-07-05
 * @author     Argoworks
 * @descriptions ...
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    ...
*/
class AddimglistModule extends CWebModule
{
	public function init()
	{
		$this->setImport(array(
			'addimglist.models.*',
			'addimglist.components.*',
		));
	}
}