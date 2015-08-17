<div class="span9">
	<h3><?php echo Yii::t('admin','Product Categories.'); ?></h3>
	<?php echo CHtml::beginForm($this->createUrl('multicategory/save?pid='.$product_id),'post'); ?>
	<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
	<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
    <div class="editinstructions clearfix">
        <div class="span6">
	        <?php echo Yii::t('admin','Please click on the additional categories where you want the product to appear.'); ?>
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
		
		<strong>Product Code : <?php echo $product->Code; ?></strong>
	</div>	
	<div class="editinstructions clearfix">
		<span><button onclick="goBack();" name="yt0" type="button" class="btn ">Cancel</button></span>
		<span><button name="yt0" type="submit" class="btn btn-primary">Save</button></span>
	</div>
	<?php $categories = Category::GetTree(); ?>
	<div class="editinstructions hero-unit">
		<ul class="filetree" id="category-tree1">
			<?php  foreach($categories as $_category): ?>
			<li>
				<a class="no-decoration" href="#"><input <?php if(isAssigned($product_id,$_category['id']) || isDefaultAssigned($product_id,$_category['id'])): ?> checked="checked" <?php endif; ?> <?php if(isDefaultAssigned($product_id,$_category['id'])): ?>disabled ="disabled" <?php endif; ?> class="checkbox-tree" type="checkbox" name="categories[]" value="<?php echo $_category['id']; ?>"/><span><?php echo $_category['label']; ?></span></a>
				<?php renderChildren($product_id,$_category); ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<script type="text/javascript">
		/*<![CDATA[*/
		jQuery(function($) {
			jQuery('a[rel="tooltip"]').tooltip();
			jQuery('a[rel="popover"]').popover();
			jQuery("#category-tree1").treeview({'animated':'fast','collapsed':false});
		});
		/*]]>*/
	</script>	
	<?php echo CHtml::endForm(); ?>
	<div style="display:none">
	<?php $category = Category::GetTree(); ?>
	<?php $category[0]['expanded'] = 'open'; ?>
	<?php $this->widget('CTreeView',array(
			'id'=>'sitemap-category-tree',
			'data'=> Category::GetTree(),
			'animated'=>'fast',
			'collapsed'=>true,
			'htmlOptions'=>array(
				'class'=>'filetree'
			)
		)); 
	?>			
	</div>
	<script>
		function goBack(){
		  window.history.back()
		}
	</script>	
	<style type="text/css">
		a.no-decoration{ text-decoration:none !important;color:#000;cursor:default;}
		.treeview ul{background:none !important;}
		.checkbox-tree{border:1px solid red;margin:0px 5px 0px 0px !important;}
		#category-tree1 ul li a{}
		.treeview .hitarea{
			position: absolute;
			top:8px;
		}
		.treeview li{position: relative !important; line-height:20px !important;}
	</style>
</div>
<?php
	function isDefaultAssigned($_productid,$categoryid){
		$criteria = new CDbCriteria();
		$criteria->addCondition('product_id='.$_productid.' AND category_id='.$categoryid.'');
		$is_exsit = ProductCategoryAssn::model()->count($criteria);
		
		if($is_exsit>0){
			return true;
		}else{
			return false;
		}
	}
	function isAssigned($_productid,$categoryid){
		$criteria = new CDbCriteria();
		$criteria->addCondition('product_id='.$_productid.' AND category_id='.$categoryid.' AND is_default = 0');
		$is_exsit = Multicategory::model()->count($criteria);
		if($is_exsit>0){
			return true;
		}else{
			return false;
		}
	}	
	function renderChildren($product_id,$arrCategory) {
		$objCategory = NULL;
		if(!$arrCategory['hasChildren']) {
			return ;
		}
		$arrChildren = $arrCategory['children'];
?>
		<ul>
<?php 	foreach($arrChildren as $_category): ?>
			<li>
				<a class="no-decoration" href="#"><input <?php if(isDefaultAssigned($product_id,$_category['id'])): ?>disabled ="disabled" <?php endif; ?> class="checkbox-tree" <?php if(isAssigned($product_id,$_category['id']) || isDefaultAssigned($product_id,$_category['id'])): ?> checked="checked" <?php endif; ?> type="checkbox" name="categories[]" value="<?php echo $_category['id']; ?>"/><span><?php echo $_category['label']; ?></span><?php if(isDefaultAssigned($product_id,$_category['id'])): ?><span style="color:#ff0000">(From POS)</span><?php endif; ?></a> 
                <?php renderChildren($product_id,$_category); ?>
            </li>
<?php 	endforeach; ?>
		</ul>
<?php
	}
?>