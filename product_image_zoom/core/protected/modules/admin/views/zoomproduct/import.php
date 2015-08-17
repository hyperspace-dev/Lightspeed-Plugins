<div class="span9">
	<div class="argoworks-head">
		<h1><?php echo Yii::t('admin','Import Product CSV Zoom Images'); ?></h1>
	</div>
	<?php if($result !=''): ?>
	<div class="row-fluid">
		<div class="span12">
			<?php echo $result; ?>
		</div>
	</div>
	<?php else: ?>
    <?php echo CHtml::beginForm('import', 'post', array('enctype'=>'multipart/form-data')); ?>
			<div class="row-fluid">
				<div class="span5"><?php echo CHtml::label(Yii::t('admin','Choose your .csv file (Max size: {max}):',array('{max}'=>ini_get('upload_max_filesize'))), 'csv_file'); ?></div>
				<div class="span5"><?php echo CHtml::fileField('csv_file', '', array('id'=>'csv_file')); ?></div>
			</div>
			<div class="row-fluid">
				<div class="span5"><span>Folder Import<span style="font-size:11px;color:#ff0000">&nbsp;(import images need to upload into this folder)</span></span></div>
				<div class="span5"><input type="text" name="dirimg" id="dirimg" value="/images/import/" /></div>
			</div>
			<div class="row-fluid">
				<div class="span12"><p>&nbsp;</p></div>
			</div>
			<div class="row-fluid">
				<h4>Structure of csv import file. </h4>
				<pre>
				<span style="float: left; width: 10%;">Product Code</span><span style="float: left; width: 15%;">Image</span><span style="float: left; width: 10%;">Is Main Image</span>
				
				<br>
				<span style="float: left; width: 50%;">Example</span>
				<span style="float: left; width: 50%; color: red;">product-code1,sample_image1.jpg,1</span>
				<span style="float: left; width: 50%; color: red;">product-code1,sample_image2.jpg,0</span>
				<span style="float: left; width: 50%; color: red;">product-code2,sample_image2.png,0</span>
				</pre>
			</div>
			<p class="pull-right">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'label'=>'Upload',
					'type'=>'primary',
					'size'=>'large',
				)); ?>
			</p>
	<?php echo CHtml::endForm(); ?>
	<?php endif; ?>
</div>