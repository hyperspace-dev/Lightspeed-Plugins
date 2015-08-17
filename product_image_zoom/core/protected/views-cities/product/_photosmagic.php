
<?php if(_xls_get_conf('enable_module_zoom')==1): ?>
<?php 
function clean_alt_string($string) {
   return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}
?>
<link href="<?php echo Yii::app()->baseurl; ?>/css/zoomproduct/magiczoomplus/magiczoomplus.css" rel="stylesheet" type="text/css" media="screen"/>
<script src="<?php echo Yii::app()->baseurl; ?>/css/zoomproduct/magiczoomplus/magiczoomplus.js" type="text/javascript"></script>
<?php
	if(!isset($FkParentID))$FkParentID = null;
	$primaryImage = Zoomproduct::getprimaryImage($model,$FkParentID);
	$smallImages = Zoomproduct::getsmallImage($model,$FkParentID);
?>
<div class="row-fluid">
	<div class="span12 zoom-wapper">
		<a href="<?php echo $primaryImage['image'];?>" class="MagicZoomPlus" id="Zoomer" title="<?php echo clean_alt_string($model->Title); ?>"><img alt="<?php echo $model->Title; ?>" src="<?php echo $primaryImage['image_large'];?>"/></a>
		<?php if($smallImages && count($smallImages)>1): ?>
		<div class="small_imgs">
			<?php if($smallImages): ?>
				<?php foreach($smallImages as $image): ?>
					<a href="<?php echo $image['image'];?>" rel="zoom-id:Zoomer" title="<?php echo clean_alt_string($model->Title); ?>" rev="<?php echo $image['image_large'];?>"><img src="<?php echo $image['image_thumb'];?>"  alt="<?php echo $model->Title; ?>" /></a>
				<?php endforeach; ?>
			<?php endif ?>
		</div>
		<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
	MagicZoomPlus.options = {
	'show-title': <?php echo _xls_get_conf('show_title_zoom')==1 ? 'true' : 'false'; ?>,
	'hint': <?php echo _xls_get_conf('show_zoom_hint')==1 ? 'true' : 'false'; ?>,
	'zoom-width': '<?php echo _xls_get_conf("area_zoom_width"); ?>',
	'zoom-height': '<?php echo _xls_get_conf("area_zoom_height"); ?>',
	'disable-zoom':<?php echo _xls_get_conf('show_zoom_area')==0 ? 'true' : 'false'; ?>,
	'disable-expand':<?php echo _xls_get_conf('show_zoom_expand')==0 ? 'true' : 'false'; ?>
	};
</script>
<style type="text/css">
.webstore #photos{
	visibility: visible !important;	
}
#photos img {
  max-width: 100%;
}
#Zoomer > img {
    width: <?php echo Zoomproduct::getConfig('DETAIL_IMAGE_WIDTH'); ?>px ;
    height: auto ;
	max-width:100%;
}
#photos .small_imgs img {
    width: <?php echo Zoomproduct::getConfig('PREVIEW_IMAGE_WIDTH'); ?>px ;
    height: auto ;
	float:left;
	margin-right:10px;
	margin-bottom:10px;
	max-width:100%;	
}
</style>

<?php else: ?>
<div class="row-fluid">
	<?php $this->widget('ext.starplugins.cloudzoom',array(
		'images'=>$model->ProductPhotos,
		'instructions'=>'<legend>'.Yii::t('global','Hover over image to zoom').'</legend>',
		'css_target'=>'targetarea span11',
		'css_thumbs'=>'thumbs span11',
		'zoomClass'=>'cloudzoom',
		'zoomSizeMode'=>'lens',
		'zoomPosition'=>Yii::app()->params['IMAGE_ZOOM']=='flyout' ? '3' : 'inside',
		'zoomOffsetX'=>Yii::app()->params['IMAGE_ZOOM']=='flyout' ? 10 : 0,
		'zoomFlyOut'=>Yii::app()->params['IMAGE_ZOOM']=='flyout' ? 'true' : 'false',
	));
	?>
</div>
<?php endif; ?>