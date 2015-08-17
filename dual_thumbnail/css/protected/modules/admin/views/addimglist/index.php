<div class="span9">
    <div class="argoworks-head">
        <h1 class="title">Dual Thumbnail Plugin</h1>
        
		<h3>Summary</h3>
        <p>
		The Dual Thumbnail Plugin allows you to load a secondary image for the product images that appear on web category pages.  When you hover over the default product image with your mouse, it displays an alternate image.
		</p>
		<h3>Install Instruction</h3>
		<p>
			<br>
			To finalize the installation of this plugin, you will need to make a small edit to your LightSpeed eCommerce theme.<br>
			1) Open file "themes/&lt;template_name&gt;/views/search/grid.php"<br>
			2) In this file you will see add this to the top of file &lt;?php Dualthumbnail::renderCssJsResource(); ?&gt;
			3) In this file find this line CHtml::link(CHtml::image($objProduct->ListingImage), $objProduct->Link) and replace it with Dualthumbnail::render($objProduct)<br/>
			
			<br>	
			<br>

			For installation and use instructions, <a target="_blank" href="http://support.argoworks.com/entries/25050141-Easy-Category-Banner-Plu">click here.</a>
		</p>		
		<p>
		For installation and use instructions, <a target="_blank" href="http://support.argoworks.com/entries/27645586-Dual-Thumbnail-Extra-Image-on-Hover-Plugin">click here</a>.
		</p>
		<h3>Support:</h3>
		<p>
		If you have a plugin issue and need service, please contact Argoworks at <a target="_blank" href="http://www.argoworks.com/helpsupport">argoworks.com</a> for plugin support
		</p>
	</div>
</div><!--/span-->