<script>



		function set_browse_library(selector)

		{

			jQuery(selector).click(function(e) {

		    	var _this = this;

		        e.preventDefault();

		        var image = wp.media({ 

		            title: 'Upload Image',

		            // mutiple: true if you want to upload multiple files at once

		            multiple: false

		        }).open()

		        .on('select', function(e){

		            // This will return the selected image from the Media Uploader, the result is an object

		            var uploaded_image = image.state().get('selection').first();

		            // We convert uploaded_image to a JSON object to make accessing it easier

		            // Output to the console uploaded_image

		            var file_url = uploaded_image.toJSON().url;

		            // Let's assign the url value to the input field

		            jQuery(_this).parent().find('input[name="'+jQuery(_this).attr('rel')+'"]').val(file_url);

		        });

		    });

		}



		jQuery(document).ready(function(){

			

		    //formulaire nouveau slider

		    jQuery('#wpo_importer .add').click(function(){



		    	var new_line = jQuery('#wpo_importer div.line_image:first-child').clone();

		    	jQuery(new_line).find('input[name="images[]"]').val('');

		    	jQuery(new_line).appendTo('.file_by_file .files');

		    	set_browse_library('#wpo_importer div.line_image:last-child .browse');



		    	return false;



		    });



		    //choix d'une image dans la librairie Wordpress

		    set_browse_library('#wpo_importer .browse');

		    

		    //lancement process ocr

			jQuery('#wpo_importer').submit(function(){



				jQuery.ajax({

				  type: 'POST',

				  url: ajaxurl,

				  data: jQuery('#wpo_importer').serialize(),

				  beforeSend: function()

				  {

				  	jQuery('#wpo_importer .progress').show();

				  },

				  success: function(data){

				    jQuery('#wpo_importer .progress').hide();

					jQuery('#wp_power_ocr_result').html(data);

					jQuery('html, body').animate({

					     scrollTop: jQuery("#wp_power_ocr_result").offset().top

					}, 500);

				  }

				});



				return false;



			});

 



		});



</script>



<h2>WP Power OCR</h2>



<form id="wpo_importer" action="<?php echo esc_url(admin_url('admin-ajax.php')) ?>" method="post">



	<input type="hidden" name="action" value="launch_ocr" />



	<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr(wp_create_nonce( "import_wpo" )); ?>" />



	<h3>Import file by file (PDF, JPEG/JPG, PNG, GIF, TIF/TIFF, BMP and PCX are supported)</h3>

	<div class="file_by_file">

		<div class="files">

			<div class="line_image">

				<label>File: </label> 

				<input type="text" name="images[]" /> 

				<button class="browse" rel="images[]">Browse...</button>

				<br />

			</div>

		</div>

		<button class="add"><img src="<?php echo esc_url(plugins_url('images/add.png', dirname(__FILE__))) ?>" /> Add a file</button>

		<input type="submit" value="Launch OCR !" />

	</div>	



	<div class="progress">

		<img src="<?php echo esc_url(plugins_url('images/progress_bar.gif', dirname(__FILE__))) ?>" />

		<div class="wait">Please wait, your files are uploaded to OCR API...</div>

	</div>



</form>



<div id="wp_power_ocr_result">

</div>



<div>

	<h3>Need more options ? Look at full version of <a href="https://www.info-d-74.com/en/produit/wp-power-ocr-plugin-wordpress-2/" target="_blank">WP Power OCR</a>! <a href="https://www.facebook.com/infod74/" target="_blank"><img src="<?php echo esc_url(plugins_url( 'images/fb.png', dirname(__FILE__))) ?>" alt="" /></a></h3>

	<a href="http://www.info-d-74.com/produit/wp-power-ocr-plugin-wordpress/" target="_blank"><img src="<?php echo esc_url(plugins_url('images/pro.png', dirname(__FILE__))) ?>" /></a>

</div>