<?php



/*

Plugin Name: WP Power OCR - Free

Plugin URI: 

Version: 1.05

Description: Extract easily text form your documents and images directly into Wordpress!

Author: InfoD74

Author URI: https://www.info-d-74.com/en/shop/

Network: false

Text Domain: wp-power-ocr-free

Domain Path: 

*/



register_activation_hook( __FILE__, 'wp_power_ocr_install' );

register_uninstall_hook(__FILE__, 'wp_power_ocr_desinstall');



function wp_power_ocr_install()

{

	//ajoute les options de config

	add_option( 'wpo_language', 'english' );

	add_option( 'wpo_username' );

	add_option( 'wpo_licence_code' );

}



function wp_power_ocr_desinstall()

{

	delete_option( 'wpo_language' );

	delete_option( 'wpo_username' );

	delete_option( 'wpo_licence_code' );

}



add_action( 'admin_menu', 'register_wp_power_ocr_menu' );

function register_wp_power_ocr_menu() {



	add_menu_page('WP Power OCR', 'WP Power OCR', 'edit_pages', 'wp_power_ocr', 'wp_power_ocr', plugins_url( 'images/icon.png', __FILE__ ), 27);

	add_submenu_page( 'wp_power_ocr', 'Settings', 'Settings', 'edit_pages', 'wp_power_ocr_settings', 'wp_power_ocr_settings');



}



add_action('admin_print_styles', 'wp_power_ocr_css' );

function wp_power_ocr_css() {



    wp_enqueue_style( 'WPPowerOCRStylesheet', plugins_url('css/admin.css', __FILE__) );

    wp_enqueue_style( 'jquery-ui-theme', '//code.jquery.com/ui/1.11.4/themes/ui-lightness/jquery-ui.css');



}



add_action( 'admin_enqueue_scripts', 'load_script_wp_power_ocr' );

function load_script_wp_power_ocr() {



	wp_enqueue_media();

	wp_enqueue_script( 'jquery');

    wp_enqueue_script( 'jquery-ui-core');

    wp_enqueue_script( 'jquery-ui-accordion');

    wp_enqueue_script( 'jquery-form');



}



function wp_power_ocr()

{

	global $wpdb;



	//vérifie si les identifiants de l'API ont été saisi

	$username = get_option('wpo_username');

	$licence_code = get_option('wpo_licence_code');



	if(empty($username) || empty($licence_code))

		wp_power_ocr_settings();

	else

		include(plugin_dir_path( __FILE__ ) . 'views/importer.php');

}



add_action( 'wp_ajax_launch_ocr', 'ajax_launch_ocr' );



function ajax_launch_ocr()

{

	if(current_user_can('edit_pages'))

	{

		check_ajax_referer( 'import_wpo' );



		//file by file

		if(!empty($_POST['images'][0]))

		{

			//récupère la langue du text et la clé d'API

			$language = get_option('wpo_language');

			$username = get_option('wpo_username');

			$licence_code = get_option('wpo_licence_code');



			//traitement image

			foreach($_POST['images'] as $key => $file_url)

			{

				if($file_url != '')

				{

					echo '<div class="image">';

					if(strstr($file_url, '.jpg') || strstr($file_url, '.png') || strstr($file_url, '.gif') || strstr($file_url, '.bmp'))

						echo '<img src="'.esc_url($file_url).'" /><br />';

					else

						echo '<a href="'.esc_url($file_url).'" target="_blank">'.esc_url($file_url).'</a><br />';



					//envoi l'image à l'API pour reconnaissance texte

					if(is_numeric($_POST['nb_pages'][$key]))

						$data = ocr_file($username, $licence_code, $file_url, $language);

					else

						$data = ocr_file($username, $licence_code, $file_url, $language);



					if($data['status'] == 'success') //text trouvé

					{

						echo '<blockquote>'.esc_html($data['text']).'</blockquote>';

					}

					else

						echo '<p>Error : '.esc_html($data['message']).'</p>';//.print_r($data, true);



					echo '</div>';

				}

			}		

		}

	}



	wp_die();



}



function wp_power_ocr_settings()

{

	if(sizeof($_POST) > 0)

	{

		update_option('wpo_language', $_POST['language']);

		update_option('wpo_username', $_POST['username']);

		update_option('wpo_licence_code', $_POST['licence_code']);

	}



	include(plugin_dir_path( __FILE__ ) . 'views/settings.php');

}



function ocr_file($username, $licence_code, $file, $language='english', $absolute=true)

{

	



        $url = 'http://www.ocrwebservice.com/restservices/processDocument?gettext=true&language='.$language; 



        //passe l'URL de l'image en absolu si besoin

		if($absolute)

		{

			$relative_path = str_replace(get_site_url().'/', '', $file);

			$file = realpath(get_home_path().$relative_path);

		}

        //$filePath = 'E:\Web\wamp\www\ocr\tarifs_internationaux_EI.pdf';

  

        $fp = fopen($file, 'r');

        $session = curl_init();



        curl_setopt($session, CURLOPT_URL, $url);

        curl_setopt($session, CURLOPT_USERPWD, "$username:$licence_code");



        curl_setopt($session, CURLOPT_UPLOAD, true);

        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($session, CURLOPT_TIMEOUT, 5000);

        curl_setopt($session, CURLOPT_HEADER, false);





        // For SSL using

        //curl_setopt($session, CURLOPT_SSL_VERIFYPEER, true);



        // Specify Response format to JSON or XML (application/json or application/xml)

        curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

 

        curl_setopt($session, CURLOPT_INFILE, $fp);

        curl_setopt($session, CURLOPT_INFILESIZE, filesize($file));



        $result = curl_exec($session);



  		$httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

        curl_close($session);

        fclose($fp);

	

        if($httpCode == 401) 

		{

           // Please provide valid username and license code

           return array('status' => 'error', 'message' => 'Error while accessing API (HTTPCode: '.$httpCode.'). Check your login and API-key in <a href="'.admin_url('admin.php?page=wp_power_ocr_settings').'">settings</a>');

        }



        // Output response

		$data = json_decode($result);



		if(!empty($data->ErrorMessage))

			return array('status' => 'error', 'message' => $data->ErrorMessage);



        // Task description

		//echo 'TaskDescription:'.$data->TaskDescription."\r\n";



        // Available pages 

		//echo 'AvailablePages:'.$data->AvailablePages."\r\n";



        // Extracted text

        $ocr_text = '';

        foreach($data->OCRText as $data)

        {

        	foreach ($data as $text) {

        		$ocr_text .= $text;

        	}

        }



        return array('status' => 'success', 'text' => $ocr_text);

}



?>