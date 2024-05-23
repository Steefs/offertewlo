<?php
function pc_populate_postcode(){
			$postcode = $_POST['postcode'];
			$huisnummer = $_POST['huisnummer'];
	
			if (empty($postcode) || empty($huisnummer)) {
				die(json_encode([]));
			}
			
			$form_id = $_POST['form_id'];
			$postcode = preg_replace('/\s+/', '',$_POST['postcode']);
			$apikey = 'RIVUh3iqng5NwDv7IGJcn19FEX05ovMN7fPo3Juj';
			$huisnummer = $_POST['huisnummer'];
			$returnarray = pc_populate_postcode_check($postcode, $huisnummer, $apikey);
			$message = $returnarray;
			die($message);

}
add_action( 'wp_ajax_pc_populate_postcode', 'pc_populate_postcode' );
add_action( 'wp_ajax_nopriv_pc_populate_postcode', 'pc_populate_postcode' );

function pc_populate_postcode_check($postcode, $huisnummer, $apikey){
	$returnarray = array();
	
	// De headers worden altijd meegestuurd als array
	$headers = array();
	$headers[] = 'X-Api-Key: '.$apikey;
	
	// De URL naar de API call
	$url = 'https://api.postcodeapi.nu/v3/lookup/'.$postcode.'/'.$huisnummer;
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	
	// De ruwe JSON response
	$response = curl_exec($curl);
	
	curl_close($curl);
	return $response;
	
}

// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
add_action('wp_head','wdk_ajaxurl');
function wdk_ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}