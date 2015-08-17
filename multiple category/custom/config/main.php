<?php
// Rename this file to main.php to use here. Items below will be merged into the primary config/main.php
$configCustom = array();
foreach (glob(dirname(__FILE__)."/config_*.php") as $filename) {
	$configFilename = require($filename);
	if(!empty($configFilename) && is_array($configFilename))
		$configCustom = CMap::mergeArray($configFilename,$configCustom);
}

return $configCustom;