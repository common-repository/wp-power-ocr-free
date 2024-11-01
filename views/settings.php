<?php



	$languages = array(

		'English' => 'english', 

		'Afrikaans' => 'afrikaans',

		'Albanian' => 'albanian',

		'Basque' => 'basque',

		'Brazilian' => 'brazilian',

		'Bulgarian' => 'bulgarian',

		'Byelorussian' => 'byelorussian',

		'Catalan' => 'catalan',

		'Chinese Simplified' => 'chinesesimplified',

		'Chinese Traditional' => 'chinesetraditional',

		'Croatian' => 'croatian',

		'Czech' => 'czech', 

		'Danish' => 'danish',

		'Dutch' => 'dutch',

		'Esperanto' => 'esperanto',

		'Estonian' => 'estonian',

		'Finnish' => 'finnish', 

		'French' => 'french', 

		'Galician' => 'galician',

		'German' => 'german',

		'Greek' => 'greek',

		'Hungarian' => 'hungarian',

		'Icelandic' => 'icelandic',

		'Indonesian' => 'indonesian',

		'Italian' => 'italian',

		'Japanese' => 'japanese',

		'Korean' => 'korean',

		'Latin' => 'latin',

		'Latvian' => 'latvian',

		'Lithuanian' => 'lithuanian',

		'Macedonian' => 'macedonian',

		'Malay' => 'malay',

		'Moldavian' => 'moldavian',

		'Norwegian' => 'norwegian',

		'Polish' => 'polish',

		'Portuguese' => 'portuguese',

		'Romanian' => 'romanian',

		'Russian' => 'russian',

		'Serbian' => 'serbian',

		'Slovak' => 'slovak',

		'Slovenian' => 'slovenian',

		'Spanish' => 'spanish',

		'Swedish' => 'swedish',

		'Tagalog' => 'tagalog',

		'Turkish' => 'turkish',

		'Ukrainian' => 'Ukrainian'

	);



	$language = get_option('wpo_language');

	$username = get_option('wpo_username');

	$licence_code = get_option('wpo_licence_code');



?>



<h2>WP Power OCR</h2>



<form method="post" id="wpo_settings" action="<?php echo esc_url(admin_url('admin.php?page=wp_power_ocr_settings')) ?>">



	<label>Language of text to extract:</label> 

	<select name="language">

	<?php



		foreach ($languages as $key => $value) {

			if($value == $language)

				echo '<option value="'.esc_attr($value).'" selected="selected">'.esc_html($key).'</option>';

			else

				echo '<option value="'.esc_attr($value).'">'.esc_html($key).'</option>';

		}





	?>

	</select><br />



	<h3>API settings</h3>



	<p><i>Request a free trial licence code here (25 pages / day during 1 month) or buy a plan here: <a href="http://www.ocrwebservice.com/api/pricing" target="_blank">http://www.ocrwebservice.com/api/pricing</a></i></p>



	<label>Username:</label> 

	<input type="text" name="username" value="<?php echo esc_attr($username) ?>" />

	<br />

	<label>Licence code:</label> 

	<input type="text" name="licence_code" value="<?php echo esc_attr($licence_code) ?>" />

	<br />



	<input type="submit" value="Save settings" />



</form>



<?php



	if(!empty($username) && !empty($licence_code))

		echo '<div id="start_wpo">Settings ok. <a href="'.esc_url(admin_url('admin.php?page=wp_power_ocr')).'">Click here to start using WP Power OCR</a>';



?>