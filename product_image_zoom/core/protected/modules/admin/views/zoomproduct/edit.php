<div class="span9">
    <div class="hero-unit">
        <h3><?php echo $this->editSectionName; ?></h3>
        <div class="editinstructions"><?php echo $this->editSectionInstructions; ?></div>
		    <?php echo CHtml::beginForm(); ?>
			    <?php foreach($model as $i=>$item): ?>
				<div class="row">
					
                    <div class="span5 class_<?php echo $i; ?>"><label><?php echo $item->title; ?>:</label></div>
                    <div class="span5"><?php
	                    switch($item->options)
	                    {
		                    case null:
		                    case "NULL":
			                case "INT":
		                    case "PINT":
			                    echo CHtml::activeTextField($item,"[$i]key_value",array('title'=>'Hover to help image'));
			                    break;

		                    case "PASSWORD":
			                    echo CHtml::activePasswordField($item,"[$i]key_value",array('title'=>'Hover to help image'));
			                    break;

		                    case "BOOL":
			                    echo '<div class="onoff" id="'.$item->id.'" title="Hover to help image"></div>'; //Create On/Off
			                    echo CHtml::activeHiddenField($item,"[$i]key_value");
			                    break;

		                    default:
			                    echo CHtml::activeDropDownList($item,"[$i]key_value",
				                    Configuration::getAdminDropdownOptions($item->options),
				                    array('title'=>'Hover to help image'));
	                    }
	                    ?></div>
                    <div class="span1 wrap-help">
	                    <?php if(!empty($item->helper_text)): ?><img src="<?= $this->assetUrl?>/img/help.png" title=""/><div class="help-hover">
							<?php if(strpos($item->helper_text,'css/zoomproduct/css')>=0): ?>
							<img src="<?php echo Yii::app()->baseUrl.'/'.$item->helper_text ?>" />
							<?php else: ?>
							<?php echo $item->helper_text; ?>
							<?php endif; ?>
						</div><?php endif; ?>
                    </div>
				</div>
			    <?php endforeach; ?>
            <p class="pull-right">
		        <?php $this->widget('bootstrap.widgets.TbButton', array(
			        'buttonType'=>'submit',
			        'label'=>'Save',
			        'type'=>'primary',
			        'size'=>'large',
		        )); ?>
	        </p>
		    <?php echo CHtml::endForm(); ?>
    </div>
</div>
<style>
.wrap-help {
  position: relative;
}
.help-hover {
  display: none;
  font-size: 12px;
  line-height: normal;
  position: absolute;
  right: 50px;
  text-align: right;
  top: 25px;
  width: 300px;
  z-index: 999;
}
.help-hover > img {
  float: right;
  max-height: 300px;
  max-width: 450px;
}
.wrap-help:hover .help-hover {
  display: block;
}
</style>