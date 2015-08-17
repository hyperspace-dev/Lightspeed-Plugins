<div class="span9 argoworks-head">
	<h1><?php echo Yii::t('admin','Product Zoom Images.'); ?></h1>
	<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
    <div class="editinstructions clearfix">
        <div class="span6">
	        <?php echo Yii::t('admin','Please upload large images'); ?>
		
		</div>
		<div class="span6">
			<div class="row">
	            <p></p>
		        <p class="pull-right">
					
				</p>
	        </div>
		</div>
	</div>
	<div class="editinstructions clearfix">
		<h4>Product Name : <?php echo $product->Title; ?></h4>
		<h4>Product Code : <?php echo $product->Code; ?></h4>
	</div> 
	    <?php	
		Yii::import("application.extensions.zoomupload.models.XUploadForm");
		$modelup = new XUploadForm;
		$this->widget('application.extensions.zoomupload.XUpload', array(
			'url' => Yii::app()->createUrl("admin/zoomproduct/upload", array("pid" => Yii::app()->getRequest()->getQuery('pid'))),
			'model' => $modelup,
			'attribute' => 'file',
			'multiple' => true,
		));
		?>
</div>
<script type="text/javascript" src="<?php echo Yii::app()->baseurl.'/css/zoomproduct/main.js' ?>"></script>
<script>
$(document).ready(function(){
$('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: '<?php echo Yii::app()->createUrl("admin/zoomproduct/getimg?pid=".Yii::app()->getRequest()->getQuery('pid')); ?>',
            dataType: 'json'
        }).always(function () {
        }).done(function (result) {
			$.each(result,function(index, value){ 
				$('.table tbody.files').append(renderdownload(value));
			});
			
        });
		
});
function setprimary(obj){
	$.ajax({
		url: '<?php echo Yii::app()->createUrl("admin/zoomproduct/setprimary?pid=".Yii::app()->getRequest()->getQuery('pid'))."&id="; ?>'+obj.value,
		dataType: 'json'
	}).always(function () {
	}).done(function (result) {
		$.each(result,function(index, value){
		alert('sd');
		});
		
	});
}
</script>
<style>
.preview img {
    max-width: 100px;
}
#XUploadForm-form .row{margin-left:0px !important;}
</style>
