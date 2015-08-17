<?php
/**
 * @version    1.0.0
 * @since      2013-07-05
 * @author     Argoworks Team
 * @copyright  Copyright &copy; 2013 Argoworks, http://www.argoworks.com
 * @package    Argoworks
*/	
	$zip = new ZipArchive;
	if ($zip->open('source_installer.zip') === TRUE) {
		$zip->extractTo('./');
		$zip->close();
		$status= 'Your new plugin installer is installed. Wasn\'t that easy?<br/>';
	} else {
		$status= 'Opp! Have some problems occur on installing the plugin. Please try again.<br/>';
	}	
	echo $status; 
?>