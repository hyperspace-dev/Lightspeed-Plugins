<div class="span9">
	<div class="argoworks-head">
	 <h1><?php echo Yii::t('admin','Import Dual Thumbnails'); ?></h1>
	</div>
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
				<span style="float: left; width: 10%;">Product Code</span><span style="float: left; width: 15%;">Image</span><span style="float: left; width: 10%;">Size</span><span style="float: left; width: 10%;">Level</span><span style="float: left; width: 10%;">Type</span>
				<span style="float: left; width: 10%;">Matrix</span><span style="float: left; width: 15%;">img_example.jpg</span><span style="float: left; width: 10%;">1000(example)</span><span style="float: left; width: 10%;">1(default)</span><span style="float: left; width: 10%;">0(default)</span>
				<br>
				<span style="float: left; width: 50%;">Example</span>
				<span style="float: left; width: 50%; color: red;">Matrix,mens-longsleeve-hoodie-zip-lined-406px-512px.jpg,22720,1,0</span>
				<span style="float: left; width: 50%; color: red;">Matrix-Blk-1,sennheiser-audiophile-hd-800-headphones.png,99685,0,0</span>
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
</div>