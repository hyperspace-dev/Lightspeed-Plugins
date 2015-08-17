<?php 
	class Dualthumbnail{
		public $_product;
		public function setProduct($objProduct){
			$this->_product = $objProduct;
		}
		public static function render($objProduct){
			$objDualthumb = Yii::app()->db->createCommand()
									  ->from('xlsws_mod_addimglist')
									  ->where('code=:code', array(':code'=>$objProduct->code))
									  ->queryRow(); 
			if($objDualthumb){
				if(isset($objDualthumb['url']) && $objDualthumb['url'] !=''){
					return CHtml::link(
						CHtml::image($objProduct->ListingImage,$objProduct->Title,array('class'=>'normal')).
						CHtml::image(Yii::app()->baseurl.'/images'.$objDualthumb['url'],$objProduct->Title,array('class'=>'hover')),
						$objProduct->Link); 
					
				}else{
					return CHtml::link(CHtml::image($objProduct->ListingImage,$objProduct->Title,array('class'=>'normal')), $objProduct->Link);
				}
			}else{
				return CHtml::link(CHtml::image($objProduct->ListingImage,$objProduct->Title,array('class'=>'normal')), $objProduct->Link);
			}
		}
		public static function renderCssJsResource(){
?>
		<style type="text/css">
			.product_cell .product_cell_graphic a {
			  position: relative;
			}
			.product_cell a{width:100%;height:100%;float:left;}
			.product_cell img.hover {
			  max-height: <?php echo _xls_get_conf('LISTING_IMAGE_HEIGHT') ?>px;
			  top: 0;
			  opacity: 0;
			  position: absolute;
			  width: 100%;
			  height:100%;
			  max-width: <?php echo _xls_get_conf('LISTING_IMAGE_WIDTH') ?>px;
			}
		</style>
		<script type="text/javascript" >
		jQuery(document).ready(function(){
			$('.product_cell_graphic a').hover(function(){
				if(!$(this).children('.hover').length>0){
					return;
				}
				$(this).children('.normal').animate({
					opacity: 0,
				}, 200, function() {
				});
					$(this).children('.hover').animate({
				opacity: 1,
				}, 200, function() {
				});
			},function(){
				$(this).children('.normal').animate({
					opacity: 1,
				}, 200, function() {
				});
					$(this).children('.hover').animate({
				opacity: 0,
				}, 200, function() {
				});
			});
		});
		</script>
<?php		
		}
	
	}
?>