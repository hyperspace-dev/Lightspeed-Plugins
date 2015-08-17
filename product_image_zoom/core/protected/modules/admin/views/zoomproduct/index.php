<div class="span9">
    <div class="argoworks-head">
        <h1 class="title">Product Zoom Images</h1>
		<h3>Summary</h3>
        <p>The Product Zoom plugin allows you to upload a large, high-resolution main image and alternate images to your product page.</p>
		<br/>
		<h3>Install Instruction</h3>
        <p>
			<br/>
			To finalize the installation of this plugin, you will need to edit your LightSpeed eCommerce theme.<br/>
			Open file "themes/your_template/views/product/index.php"<br/><br/>
			1) In this file you will see &lt;?php $this->renderPartial('/product/_photos', array('model'=>$model), true); ?&gt; change it to &lt;?php $this->renderPartial('application.views-cities.product._photosmagic', array('model'=>$model), true); ?&gt; <br/><br/>
			2) In this file you will see $this->widget('ext.<strong style="font-size:120%;color:#ff0000;">wsmenu</strong>.wsmatrixselector', array('form'=> $form,'model'=> $model));  change it to $this->widget('ext.<strong style="font-size:120%;color:#ff0000;">magiczmenu</strong>.wsmatrixselector', array('form'=> $form,'model'=> $model));
			
			<br/>	
			<br/>

			For installation and use instructions, <a href="http://www.argoworks.com/downloads/product-zoom-images?term=35" target="_blank">click here.</a>
		</p>
		<h3>Support:</h3>
		<p>
		If you have a plugin issue and need service, please contact Argoworks at <a target="_blank" href="http://www.argoworks.com/helpsupport">argoworks.com</a> for plugin support
		</p>
	</div>
</div><!--/span-->