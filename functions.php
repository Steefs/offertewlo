<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);
	wp_enqueue_style( 'wlo-style', get_stylesheet_directory_uri() .'/dist/css/style.css', array('hello-elementor-child-style'), filemtime(get_stylesheet_directory() .'/dist/css/style.css'), 'all' );
	wp_enqueue_script( 'wlo-js', get_stylesheet_directory_uri() .'/dist/js/scripts.min.js', array('jquery'), filemtime(get_stylesheet_directory() .'/dist/js/scripts.min.js'), 'true' );

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

require('functions/forms.php');
require('functions/theme.php');
require('functions/postcode-check.php');

require('functions/seo.php');
require('functions/acf.php');
require('functions/extra.php');

//[foobar]
function foobar_func( $atts ){
	ob_start();

	require_once('api.class.php');
	$token = 'aZUCIuLmH3UHLsT0s0vC9mmLuTc3ve'; //Your API token
	$api = new gripp_API($token);

	$fields = array(
		'template' => 184,
		'name' => 'test steefs',
		'company'	=> 141649
	);
	//$response = $api->offer_create($fields);
	/*
	$fields = array(
		"companyname" => "Steefs",
		"email" => "stefan@steefs.nl",
		"relationtype" => "PRIVATEPERSON",
		'lastname'	=> 'Remmerde',
		"invoicesendby" => "EMAIL", //verplicht
		"invoiceemail" => "stefan@steefs.nl", //verplicht
	);
	$responses = $api->company_create($fields);
	$response = $responses[0]['result'];
	$company_id = $response["recordid"];
	echo $company_id;
	*/
	print '<pre>';
	print_r($response);
	$return = ob_get_contents();
	ob_get_clean();
	return $return;
}
//add_shortcode( 'foobar', 'foobar_func' );

