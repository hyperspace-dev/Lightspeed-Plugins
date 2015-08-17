<div class="span9">
	<div class="argoworks-head">
		<h1><?php echo Yii::t('admin','Upload Dual Thumbnail'); ?></h1>
	</div>
	<input type="hidden" name="product_id" value="<?php echo $product->id; ?>" />
    <div class="editinstructions clearfix">
        <div class="span6">
	        <?php echo Yii::t('admin','Upload the second image using the choose file button. Select your image and click save.'); ?>
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
		<h4>Product Name : <?php echo $product->title; ?></h4>
		<h4>Product Code : <?php echo $code; ?></h4>
	</div> 
	    <?php	
		Yii::import("application.extensions.addimgupload.models.XUploadForm");
		$modelup = new XUploadForm;
		$this->widget('application.extensions.addimgupload.XUpload', array(
			'url' => Yii::app()->createUrl("admin/addimglist/upload", array("code" => Yii::app()->getRequest()->getQuery('code'))),
			'model' => $modelup,
			'attribute' => 'file',
			'multiple' => false,
		));
		?>
</div>
<script>
$(document).ready(function(){
$('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: '<?php echo Yii::app()->createUrl("admin/addimglist/getimg?code=".Yii::app()->getRequest()->getQuery('code')); ?>',
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
		url: '<?php echo Yii::app()->createUrl("admin/addimglist/setprimary?code=".Yii::app()->getRequest()->getQuery('code'))."&id="; ?>'+obj.value,
		dataType: 'json'
	}).always(function () {
	}).done(function (result) {
		$.each(result,function(index, value){
		alert('sd');
		});
		
	});
}
function renderdownload(value){
	var check="";
	if(value['level']==1){
		check="checked=checked"
	}
	var content="<tr class='template-download fade in'><td class='preview'><a href="+value['url']+" title="+value['nameimg']+" rel='gallery' download="+value['nameimg']+"><img src="+value['url']+"></a></td><td class='name'><a href="+value['url']+" title="+value['nameimg']+" rel='gallery' download="+value['nameimg']+">"+value['name']+"</a></td><td class='size'><span>"+value['size']+"</span></td><td colspan='2'></td><td class='delete'><button class='btn btn-danger' data-type='POST' data-url="+value['del_url']+"><i class='icon-trash icon-white'></i><span>Delete</span></button><input type='checkbox' name='delete' value='1'></td><td class='delete'></td></tr>";
	return content;
}
</script>
<style>
.preview img {
    max-width: 100px;
}
#XUploadForm-form .row{margin-left:0px !important;padding-left:0px !important;}
</style>
