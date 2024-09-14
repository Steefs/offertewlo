<?php
function steefs_aanvraagiteminfo_html( $postitem ){
	ob_start();
	
			echo '<div  class=" gfield--type-section"><h3 class="gsection_title">' . $postitem->post_title . '</h3></div><div class="choiceproduct">';
			if ( has_post_thumbnail($postitem->ID) ) {
				echo get_the_post_thumbnail($postitem->ID, 'medium');
			}
			echo '</div>';
			$return = ob_get_contents();
			ob_get_clean();
			return $return;

}
function steefs_aanvraagiteminfo_func( $atts ){
	ob_start();
	if(isset($_GET['items'])):
		$postitem = steefs_get_post_by_item($item = false);

		if($postitem):

			echo steefs_aanvraagiteminfo_html( $postitem );

		endif;
	endif;
	$return = ob_get_contents();
	ob_get_clean();
	return $return;
}
add_shortcode( 'aanvraagiteminfo', 'steefs_aanvraagiteminfo_func' );

//add_action( 'gform_after_submission_1', 'steefs_set_post_to_api', 10, 2 );
function steefs_set_post_to_api( $entry, $form ) {
	$entryfields = steefs_get_entryfields($form);
	$itemid = rgar( $entry, $entryfields['itemid'] );
	$arrid = rgar( $entry, $entryfields['arrid'] );
	$business = rgar( $entry, $entryfields['business'] );
	$gelegenheid_photobooth = rgar( $entry, $entryfields['gelegenheid_photobooth'] );
	$gelegenheid = false;
	$weddinggelegenheid = false;
	if($gelegenheid_photobooth == 'Bedrijfsfeest'):
		$business = 1;
		$gelegenheid = 'Photobooth';
		$tag = 4;
	elseif($gelegenheid_photobooth == 'Feest'):
		$gelegenheid = 'Photobooth';
		$tag = 4;
	elseif($gelegenheid_photobooth == 'Bruiloft'):
		$gelegenheid = 'Photobooth';
		$tag = 4;
		$weddinggelegenheid = 1;
	elseif($gelegenheid_photobooth == 'Bruiloft en trouwvervoer'):
		$gelegenheid = 'Trouwvervoer en Photobooth';
		$tag = 4;
		$weddinggelegenheid = 1;
	endif;
	$email = rgar( $entry, $entryfields['email'] );
	$company_id = false;
	if($itemid):
		$postitem = steefs_get_post_by_item($itemid);
		if(get_post_type($postitem) == 'arrangement'):
			$postitem = get_post( rgar( $entry, $entryfields['keuze_oldtimer'] ) );
		endif;
	else:
		$postitem = get_post( rgar( $entry, $entryfields['keuze_oldtimer'] ) );
	endif;
	
	if($postitem):
		require_once('api.class.php');
		$token = 'aZUCIuLmH3UHLsT0s0vC9mmLuTc3ve'; //Your API token
		$api = new gripp_API($token);
		$template = get_field('templateid', 'option');
		$naam_website = get_field('naam_website', 'option');
		if($arrid):
			$arrangementitem = steefs_get_post_by_item($arrid);
		else:
			$arrangementitem = false;
		endif;
		if($arrangementitem):
			
			if(get_field('templateid', $arrangementitem->ID)):
				$template = get_field('templateid', $arrangementitem->ID);
			endif;
			if($business == 1):
				if(get_field('templateid_zakelijk', $arrangementitem->ID)):
					$template = get_field('templateid_zakelijk', $arrangementitem->ID);
				endif;
			endif;
			if($weddinggelegenheid == 1):
				if(get_field('templateid_wedding', $arrangementitem->ID)):
					$template = get_field('templateid_wedding', $arrangementitem->ID);
				endif;
			endif;
		endif;
		$companyid = steefs_get_company($email);
		if($companyid == false):
			$phone = phonize(rgar( $entry, $entryfields['telefoonnummer'] ), rgar( $entry, $entryfields['land'] ));

			if($business == 1):
				$fields = array(
					'identity' => 1,
					'invoicesendby' => 'EMAIL',
					'invoiceemail' => $email,
					'email' => $email,
					'phone' => $phone,
					'relationtype' => 'COMPANY',
					'companyname' => rgar( $entry, $entryfields['bedrijfsnaam'] ),
					'companyroles' => array('LEAD'),
					'active' => true,
					'customfield_avgakkoord' => true,
					'invoicesendto' => 'VISITINGADDRESS',
					'invoiceaddress_companyname' => rgar( $entry, $entryfields['bedrijfsnaam'] ),
					'invoiceaddress_attn' => 'Administratie',
					'visitingaddress_street' => rgar( $entry, $entryfields['straatnaam'] ),
					'visitingaddress_streetnumber' => rgar( $entry, $entryfields['huisnummer'] ),
					'visitingaddress_zipcode' => rgar( $entry, $entryfields['postcode'] ),
					'visitingaddress_city' => rgar( $entry, $entryfields['plaatsnaam'] ),
					'visitingaddress_country' => rgar( $entry, $entryfields['land'] ),
					'customfield_benaderenper' => 'E-mail',
					);
			else:
				$fields = array(
					'companyname' => '',
					'email' => $email,
					'phone' => $phone,
					'relationtype' => 'PRIVATEPERSON',
					'companyroles' => ['LEAD'],
					'active' => true,
					'firstname'=> rgar( $entry, '3.3' ),
					'lastname'	=> rgar( $entry, '3.6' ),
					'postaddress'	=> 'VISITINGADDRESS',
					'customfield_voornaampartner' => rgar( $entry, '9.3' ),
					'invoiceaddress_street' => rgar( $entry, $entryfields['straatnaam'] ),
					'invoiceaddress_streetnumber' => rgar( $entry, $entryfields['huisnummer'] ),
					'invoiceaddress_zipcode' => rgar( $entry, $entryfields['postcode'] ),
					'invoiceaddress_city' => rgar( $entry, $entryfields['plaatsnaam'] ),
					'invoiceaddress_country' => rgar( $entry, $entryfields['land'] ),
					'postaddress_street' => rgar( $entry, $entryfields['straatnaam'] ),
					'postaddress_streetnumber' => rgar( $entry, $entryfields['huisnummer'] ),
					'postaddress_zipcode' => rgar( $entry, $entryfields['postcode'] ),
					'postaddress_city' => rgar( $entry, $entryfields['plaatsnaam'] ),
					'postaddress_country' => rgar( $entry, $entryfields['land'] ),
					'visitingaddress_street' => rgar( $entry, $entryfields['straatnaam'] ),
					'visitingaddress_streetnumber' => rgar( $entry, $entryfields['huisnummer'] ),
					'visitingaddress_zipcode' => rgar( $entry, $entryfields['postcode'] ),
					'visitingaddress_city' => rgar( $entry, $entryfields['plaatsnaam'] ),
					'visitingaddress_country' => rgar( $entry, $entryfields['land'] ),
					'invoicesendby' => 'EMAIL', //verplicht
					'invoiceemail' => $email, //verplicht
				);
			endif;
			$responses = $api->company_create($fields);
			$response = $responses[0]['result'];
			if($response):
				$companyid = $response["recordid"];
			endif;
			if($companyid):
				if($business == 1):

					$phone = phonize(rgar( $entry, $entryfields['telefoonnummer'] ), rgar( $entry, $entryfields['land'] ));
					$fieldsContact = array(
						"company" => $companyid,
						"showoncompanycard" => true,
						"active" => true,
						"email" => $email,
						'phone' => $phone,
						"function" => "Contactpersoon",
						"initials" => "",
						"firstname" => rgar( $entry, '3.3' ),
						"lastname" =>  rgar( $entry, '3.6' ),
						);
						
						$fieldsContactresponse = $api->contact_create($fieldsContact);
				endif;
			endif;
		endif;
		if($companyid && $template):
			$dag = false;
			$result = GFAPI::update_entry_field( rgar( $entry,'id' ),  $entryfields['companyid'], $companyid );
			$wedding = rgar( $entry, $entryfields['wedding'] );
			if($weddinggelegenheid == 1):
				$wedding = 1;
			endif;
			if(rgar( $entry, $entryfields['huurdatum'] )):
				$huurdatum = date_create_from_format('Y-m-d', rgar( $entry, $entryfields['huurdatum'] ));
				$huurdatumvan = $huurdatum->format('d-m-Y');
				$huurdatumvanen = $huurdatum->format('Y-m-d');
			 	if ($huurdatum->format('N') < 4):
					$dag = "ma-wo";
				elseif ($huurdatum->format('N') == 5):
					$dag = "vr";
				else:
					$dag = "do-za-zo";
				endif;
				if(!$wedding):
					if ($huurdatum->format('N') < 5):
						$dag = "ma-do";
					else:
						$dag = "vr-zo";
					endif;
				endif;
			else:
				$huurdatumvan = '';
			endif;
			if(rgar( $entry, $entryfields['huurdatumtot'] )):
				$einddatum = date_create_from_format('Y-m-d', rgar( $entry, $entryfields['huurdatumtot'] ));
				$huurdatumtot = $einddatum->format('Y-m-d');
			else:
				if($huurdatumvanen):
					$huurdatumtot = $huurdatumvanen;
				else:
					$huurdatumtot = '';
				endif;
			endif;
			$extra_opties = steefs_entry_checkbox( $form, $entryfields['extra_opties'], $entry );
			$extra_opties_n = steefs_entry_checkbox( $form, $entryfields['extra_opties_n'], $entry );

			$extra_oldtimers = steefs_entry_checkbox( $form, $entryfields['extra_autos'], $entry );
			
			
			$tagwebsite = (int)get_field('tag_website', 'options');

			$oldtimername = $postitem->post_title;
			if(rgar( $entry, $entryfields['starttijdverhuur'] )):
				$starttijdverhuur = rgar( $entry, $entryfields['starttijdverhuur'] );
			else:
				$starttijdverhuur = get_field('starttijd','options');
				if($arrangementitem):
					if(get_field('starttijd', $arrangementitem->ID)):
						$starttijdverhuur = get_field('starttijd', $arrangementitem->ID);
					endif;
				endif;
			endif;
			if(rgar( $entry, $entryfields['eindtijdverhuur'] )):
				$eindtijdverhuur = rgar( $entry, $entryfields['eindtijdverhuur'] );
			else:
				$eindtijdverhuur = get_field('eindtijd','options');
				if($arrangementitem):
					if(get_field('eindtijd', $arrangementitem->ID)):
						$eindtijdverhuur = get_field('eindtijd', $arrangementitem->ID);
					endif;
				endif;
			endif;
			if($wedding):
				$oldtimer = get_field('gripp_car', $postitem->ID);
				if(!$gelegenheid ):
					$gelegenheid = 'Trouwvervoer';
				endif;
				if($arrangementitem):
					if(get_field('gelegenheid', $arrangementitem->ID)):
						$gelegenheid = get_field('gelegenheid', $arrangementitem->ID);
					endif;
				endif;
				$tag = 6;
				if(get_field('templateid', $postitem->ID)):
					$template = get_field('templateid', $postitem->ID);
				endif;
			else:
				$oldtimername =  get_field('gripp_car_nowedding', $postitem->ID);
				$oldtimer = get_field('gripp_car_nowedding', $postitem->ID);
				if($arrangementitem):
					if(get_field('gelegenheid', $arrangementitem->ID)):
						$gelegenheid = get_field('gelegenheid', $arrangementitem->ID);
					else:
						$gelegenheid = get_the_title($arrangementitem->ID);
					endif;
					if(get_field('tag_id', $arrangementitem->ID)):
						$tag = get_field('tag_id', $arrangementitem->ID);
					else:
						$tag = 8;
					endif;
				elseif($business):
					$gelegenheid = 'Zakelijk vervoer';
					$tag = 5;
				elseif(!$gelegenheid):
					$gelegenheid = 'Dagje toeren';
					$tag = 8;
				endif;
			endif;
			
			$startlocatie = rgar( $entry, $entryfields['startlocatie'] );
			$eindlocatie = rgar( $entry, $entryfields['eindlocatie'] );

			if(empty(rgar( $entry, $entryfields['startlocatie'] ))):
				$startlocatie = "Hoge Giessen 9A, Hoogblokland";
			else:
				$startlocatie = rgar( $entry, $entryfields['startlocatie'] );
			endif;

			if(empty(rgar( $entry, $entryfields['eindlocatie'] ))):
				$eindlocatie = "Hoge Giessen 9A, Hoogblokland";
			else:
				$eindlocatie = rgar( $entry, $entryfields['eindlocatie'] );
			endif;
			$fields = array(
				'template' => (int)$template,
				'name' => $oldtimername . ', ' .  $huurdatumvan,
				'company' => $companyid,
				'signingenabled' => true,
				'customfield_huurdatum' => $huurdatumvanen,
				'customfield_huurdatumtot' => $huurdatumtot,
				'customfield_starttijdverhuur' => $starttijdverhuur,
				'customfield_eindtijdverhuur' => $eindtijdverhuur,
				'customfield_startlocatie' => $startlocatie,
				'customfield_eindlocatie' => $eindlocatie,
				'customfield_aantalpersonen' => rgar( $entry, $entryfields['personen'] ),
				'customfield_opmerkingendoorklant' => rgar( $entry, $entryfields['opmerkingen'] ),
				'customfield_offerteaanvraag' => $naam_website . ' offerte via website',
				'customfield_cadeaubonnummer' => rgar( $entry, $entryfields['cadeaubonnummer'] ),
				'customfield_website' => $naam_website,
				'customfield_oldtimer' => $oldtimer,
				'customfield_gelegenheid' => $gelegenheid,
				'customfield_extraoldtimer' => rgar( $entry, $entryfields['extra_oldtimer_1'] ),
				'customfield_extraoldtimer2' => rgar( $entry, $entryfields['extra_oldtimer_2'] ),
				'customfield_clientid' => rgar( $entry, $entryfields['clientid'] ),
				'description' => '',
				'tags' =>  [$tagwebsite, $tag]
			);
			if($extra_opties):
			foreach($extra_opties as $extra_optie):
				$fields['customfield_' . $extra_optie] = 1;
			endforeach;
			endif;
			if($extra_opties_n):
				foreach($extra_opties_n as $extra_optie):
					$fields['customfield_' . $extra_optie] = 1;
				endforeach;
			endif;
			if($dag):
				$grippnaam = get_field('gripp_naam', $postitem->ID);
				$oldtimerValue2 = false;
				if($arrangementitem):
					if(get_field('gripp_naam', $arrangementitem->ID)):
						$oldtimerValue = get_field('gripp_naam', $arrangementitem->ID);
					else:
						$oldtimerValue = $grippnaam ." dagtarief " . $dag;
					endif;
				else:
					$oldtimerValue = $grippnaam ." dagtarief " . $dag;
				endif;
			
				if($gelegenheid_photobooth == 'Bedrijfsfeest'):
					$oldtimerValue = 'Photobooth Bedrijfsfeest';
				elseif($gelegenheid_photobooth == 'Feest'):
					$oldtimerValue = 'Photobooth Feest';
				elseif($gelegenheid_photobooth == 'Bruiloft'):
					$oldtimerValue = 'Photobooth Bruiloft';
				elseif($gelegenheid_photobooth == 'Bruiloft en trouwvervoer'):
					$oldtimerValue = 'Photobooth in combinatie met Trouwvervoer';
				endif;

				if($oldtimerValue == 'Photobooth in combinatie met Trouwvervoer'):
					$oldtimerValue2 = $grippnaam ." dagtarief " . $dag;
				endif;
				
				$productfilters = array(
					array(
						"field" => "product.name",
						"operator" => "like",
						"value" => $oldtimerValue
					),
					array(
						"field" => "product.tags",
						"operator" => "equals",
						"value" => $tag
					)
				);
				$productresponse = $api->product_getone($productfilters);
				$productresponseresult = $productresponse[0]['result'];
				$offerlines = array();
				if($productresponseresult['rows']):
					$productnummer = $productresponseresult['rows'][0]['number'];
					$product_id = $productresponseresult['rows'][0]['id'];
					$sellingprice = $productresponseresult['rows'][0]['sellingprice'];
					$buyingprice = $productresponseresult['rows'][0]['buyingprice'];
					$beschrijving = $productresponseresult['rows'][0]['description'];
					$offerlines[] = array(
							"product" => $product_id,
							"amount" => 1,
							"invoicebasis" => "FIXED",
							"sellingprice" => $sellingprice,
							"discount" => 0,
							"buyingprice" => $buyingprice,
							"description" => $beschrijving,
					);
				endif;

				if($oldtimerValue2):
					$productfilters = array(
						array(
							"field" => "product.name",
							"operator" => "like",
							"value" => $oldtimerValue2
						),
						array(
							"field" => "product.tags",
							"operator" => "equals",
							"value" => $tag
						)
					);
					$productresponse = $api->product_getone($productfilters);
					$productresponseresult = $productresponse[0]['result'];
					if($productresponseresult['rows']):
						$productnummer = $productresponseresult['rows'][0]['number'];
						$product_id = $productresponseresult['rows'][0]['id'];
						$sellingprice = $productresponseresult['rows'][0]['sellingprice'];
						$buyingprice = $productresponseresult['rows'][0]['buyingprice'];
						$beschrijving = $productresponseresult['rows'][0]['description'];
						$offerlines[] = array(
							"product" => $product_id,
							"amount" => 1,
							"invoicebasis" => "FIXED",
							"sellingprice" => $sellingprice,
							"discount" => 0,
							"buyingprice" => $buyingprice,
							"description" => $beschrijving,
						);
					endif;
				endif;

				if($offerlines):
					$fields['offerlines'] = $offerlines;
				endif;
			endif;
			$response = $api->offer_create($fields);
			if($response):
				ob_start();
				print_r($response);
				$returnread = ob_get_contents();
				ob_get_clean();
				$result = GFAPI::update_entry_field( rgar( $entry,'id' ),  $entryfields['responsegripp'], $returnread );
				if (array_key_exists('recordid',  $response[0]['result'])):
					$result = GFAPI::update_entry_field( rgar( $entry,'id' ),  $entryfields['offerteid'], $response[0]['result']['recordid'] );
				endif;
			endif;
		endif;
	endif;

}

//get entrie ids by label
function steefs_get_entryfields($form){
	$entryfields = array();
	  foreach ( $form['fields'] as &$field ) {
			  if($field->adminLabel):
				  $entryfields[$field->adminLabel] = $field->id;
			  else:
				  $label = lcfirst($field->label);
				  $label = strtolower($label);
				  $entryfields[$label] = $field->id;
			  endif;
	  }
	return $entryfields;
  }
  
  function steefs_get_company($email){
	if($email):
       require_once('api.class.php');
		$token = 'aZUCIuLmH3UHLsT0s0vC9mmLuTc3ve'; //Your API token
		$api = new gripp_API($token);

		$filters = array(
			array(
				"field" => "company.email",
				"operator" => "like",    	
				"value" => $email
			)
		);
		$responses = $api->company_getone($filters);
		$response = $responses[0]['result'];
		if($response['rows']):
			$companyid = $response['rows'][0]['id'];
			return $companyid;
		else:
			return false;
		endif;
	endif;
	return false;
}

add_filter( 'gform_pre_render', 'yid_populate_trans_label' );
add_filter( 'gform_pre_validation', 'yid_populate_trans_label' );
function yid_populate_trans_label( $form ) {
	if ( rgar( $form, 'id' ) !== 1 ) {
        return $form;

    }

	$entryfields = steefs_get_entryfields($form);
	$args = array(
		'post_type'        => 'auto',
		'post_status'        => 'publish',
		'posts_per_page'     => '-1'
	);
	$postitem = steefs_get_post_by_item();
	
    $current_page = GFFormDisplay::get_current_page( $form['id'] );
	$startpage = 1;
	$business = false;
	$keuze_arrangement = false;
	$wedding = false;
	$curarrangement = false;
	if($postitem or isset($_GET['wedding']) or isset($_GET['business'])):
		$startpage = 2;
		GFFormDisplay::$submission[$form['id']]["page_number"] = 2;
	endif;
	if($current_page == 2 && $startpage == 1):
		foreach ( $form['fields'] as &$field ) { 
			if ( $field->id == $entryfields['keuze_arrangement']):
				$keuze_arrangement = rgpost('input_' . $field->id );
			endif; 
		}
	endif;
	if(is_numeric($keuze_arrangement)):
		$postitem  = get_post($keuze_arrangement);
	elseif($keuze_arrangement == 'wedding'):
		$wedding = 1;
	elseif($keuze_arrangement == 'business'):
		$business = 1;
	endif;
	if(isset($_GET['wedding']) and $wedding == false):
		$wedding   = isset( $_GET['wedding'] ) ? sanitize_text_field( wp_unslash( $_GET['wedding'] ) ) : '';
	endif;
	if($postitem ):
		if(get_post_type($postitem) == 'arrangement'):
			$curarrangement = $postitem->ID;
			if(get_field('wedding', $postitem->ID)):
				$wedding = 1;
			endif;
			
			if(!get_field('add_to_all', $postitem->ID)):
			
				$meta_query = [];

				// Append our meta query
				$meta_query[] = [
					'key' => 'arrangementen',
					'value' =>  $postitem->ID,
					'compare' => 'LIKE',
				];

				$args['meta_query'] = $meta_query;
			endif;
			
		endif;
	endif;
	//Reading posts for "Business" category;
	$posts = get_posts( $args );
 
	//Creating item array.
	$items = array();
	 $options = array();
	 $optionsextra = array();
	 $optionsarrangement = array();
	 $optionsarrangement[] = array( 'value' => '', 'text' => 'Dagje toeren' );
	 $optionsarrangement[] = array( 'value' => 'business', 'text' => 'Zakelijk event' );
	 $optionsarrangement[] = array( 'value' => 'wedding', 'text' => 'Trouwvervoer' );

	 $options[] = array( 'value' => '', 'text' => 'Geen extra oldtimer' );

	$fields = $form['fields'];
	$input_id = 1;
	$input_idarr = 1;

	$extra_oldtimer_1 = $entryfields['extra_oldtimer_1'];
	$extra_oldtimer_2 = $entryfields['extra_oldtimer_2'];
	$keuze_oldtimer = $entryfields['keuze_oldtimer'];
	if (array_key_exists('keuze_arrangement', $entryfields)):
		$keuze_arrangementid = $entryfields['keuze_arrangement'];
	else:
		$keuze_arrangementid = 0;
	endif;
	

			$argsarr = array(
				'post_type'        => 'arrangement',
				'post_status'        => 'publish',
				'posts_per_page'     => '-1'
			);

			$meta_queryargsarr = [];

			// Append our meta query
			$meta_queryargsarr[] = [
				'key' => 'hide_on_form',
				'compare' => 'NOT EXISTS',
			];
			$meta_queryargsarr[] = [
				'key' => 'hide_on_form',
				'value' => 0,
				'compare' => '=',
			];
			$meta_queryargsarr['relation'] = 'OR';


			$argsarr['meta_query'] = $meta_queryargsarr;
		
			$postsarr = get_posts( $argsarr );
			foreach ( $postsarr as $postsarr ) {
				if ( $input_idarr % 10 == 0 ) {
					$input_idarr++;
				}
				if($curarrangement == $postsarr->ID):
					$selected = true;
				else:
					$selected = false;
				endif;
				$optionsarrangement[] = array( 'value' =>$postsarr->ID, 'text' => $postsarr->post_title, 'isSelected' =>  $selected);
		
				$input_idarr++;
		
			}
	//Adding post titles to the items array
	foreach ( $posts as $post ) {
		if ( $input_id % 10 == 0 ) {
			$input_id++;
		}
		$options[] = array( 'value' =>$post->post_title, 'text' => $post->post_title );
		$optionsextra[] = array( 'value' =>$post->ID, 'text' => $post->post_title );

		$input_id++;

	}
	
	foreach ( $form['fields'] as &$field ) {

		if ( $field->id == $entryfields['html']) {
			$field->content = "
			<script>
			gform.addFilter( 'gform_datepicker_options_pre_init', function( optionsObj, formId, fieldId ) {
				if ( formId == 1 && fieldId == 5 ) {
					optionsObj.minDate = 1;
					optionsObj.onClose = function (dateText, inst) {
						 jQuery('#input_1_42').datepicker('option', 'minDate', dateText).datepicker('setDate', dateText);
					};
				}
				return optionsObj;
			});
			</script>";
		}

		if ( $field->id == 24 && $postitem && $current_page == 2 && $startpage == 1) {
			$field->content = steefs_aanvraagiteminfo_html( $postitem );
		}
		if ( $field->id == $entryfields['cars']) {
			$field->content = '[elementor-template id="151"]';
		}
		if ( $field->id == $extra_oldtimer_1 or $field->id == $extra_oldtimer_2) {
			$field->choices = $options;
		}
		if ( $field->id == $keuze_oldtimer) {
			$field->choices = $optionsextra;
		}
		if ( $field->id == $keuze_arrangementid) {
			$field->choices = $optionsarrangement;
		}
	
		if ( $wedding) {
			if ( $field->id ==  $entryfields['huurdatum']) {
				$field->label = 'Trouwdatum';
			}
			if ( $field->id ==  8) {
				$field->label = 'Jullie gegevens';
			}
		}

		if ( $field->id == $entryfields['business']):
			if($business):
				$field->defaultValue = '1';
			endif;
		endif; 

		if ( $field->id == $entryfields['wedding']):
			if($wedding):
				$field->defaultValue = '1';
			endif;
		endif; 

		if ( $field->id == $entryfields['arrid']):
			if(is_numeric($keuze_arrangement)):
				$field->defaultValue = get_field('uniqid', $keuze_arrangement);
			endif;
		endif; 

		//remove photobooth from options if wedding and photobooth
		if ( $field->id == $entryfields['extra_opties_n'] && $postitem):
			if (str_contains(get_the_title($postitem->ID), 'Photobooth')) {

				$choicesnew = array();
				foreach($field->choices as $choice):
					if (!str_contains($choice['value'], 'photobooth')):

						$choicesnew[] = $choice;
					endif;
				endforeach;

				$field->choices = $choicesnew;
			}
			
		endif; 
		if ( $field->id == $entryfields['gelegenheid_photobooth'] ):
			$photobooth_arrangement_id = get_field('photobooth_arrangement_id', 'options');
			
			if (!is_array($field->conditionalLogic)) :

				$field->conditionalLogic =
				array(
					'actionType' => 'show',
					'logicType' => 'all',
					'rules' =>
						array( 
							array( 'fieldId' => $entryfields['keuze_arrangement'],  'operator' => 'contains', 'value' => $photobooth_arrangement_id )
							)
				);
			endif;
		endif; 
		
	}
	return $form;
		
}
  
add_action( 'gform_pre_entry_detail', 'mark_entry_read', 10, 2 );
function mark_entry_read( $form, $entry ){
	$entryfields = steefs_get_entryfields($form);
	echo rgar( $entry, $entryfields['huurdatum'] );
	$itemid = rgar( $entry, $entryfields['itemid'] );
	$email = rgar( $entry, $entryfields['email'] );

	//var_dump(steefs_entry_checkbox( $form, $entryfields['extra_opties'], $entry ));
	//steefs_set_post_to_api( $entry, $form );


	require_once('api.class.php');
		$token = 'aZUCIuLmH3UHLsT0s0vC9mmLuTc3ve'; //Your API token
		$api = new gripp_API($token);
		$tag_ids = array(6);

		$filters = array(
			array(
				"field" => "product.tags",
				"operator" => "in",
				"value" => $tag_ids
			)
		);
		
		$options = array(
			"paging" => array(
				"firstresult" => 0,
				"maxresults" => 100
			),
			"orderings" => array(
				array(
					"field" => "product.id",
					"direction" => "asc"
				)
			)
		);
		
		$batchresponse = $api->product_get($filters, $options);
		
		$response = $batchresponse[0]['result'];
		foreach($response['rows'] as $row):
			//echo $row['name'];
			//echo $row['id'];
			//echo '<br>';
		endforeach;
		//var_dump($response['rows']);
	echo $email;

}

function steefs_entry_checkbox( $form, $entryid, $entry ){
	$field = GFAPI::get_field( $form,  $entryid );
	$value = is_object( $field ) ? $field->get_value_export( $entry ) : '';
	$pieces = explode(",", $value);
	
	return $pieces;
}

// is car in url
add_filter( 'gform_field_value_items', 'steefs_items_value' );
function steefs_items_value( $value ) {
	$postitem = steefs_get_post_by_item();
	if($postitem):
    	return $value;
	else:
		return '';
	endif;
}

// is event in url
add_filter( 'gform_field_value_arrid', 'steefs_arrid_value' );
function steefs_arrid_value( $value ) {
	$postitem = steefs_get_post_by_item();
	if($postitem):
		if(get_post_type($postitem) == 'arrangement'):
			if(isset($_GET['items'])):
				$item   = isset( $_GET['items'] ) ? sanitize_text_field( wp_unslash( $_GET['items'] ) ) : '';
				return $item;
			endif;
		else:
			return '';

		endif;
	else:
		return '';
	endif;
}


function carsquery_elementor( $query ) {
	remove_action( 'elementor/query/carsquery', 'carsquery_elementor' );
	$postitem = steefs_get_post_by_item();
	add_action( 'elementor/query/carsquery', 'carsquery_elementor' );
	if($postitem):
		if(get_post_type($postitem) == 'arrangement'):

			// If there is no meta query when this filter runs, it should be initialized as an empty array.
			if ( ! $meta_query ) {
				$meta_query = [];
			}

			// Append our meta query
			$meta_query[] = [
				'key' => 'arrangementen',
				'value' => [ $postitem->ID ],
				'compare' => 'LIKE',
			];

			$query->set( 'meta_query', $meta_query );

			
		endif;
	endif;
}
add_action( 'elementor/query/carsquery', 'carsquery_elementor' );

add_filter( 'gform_field_value_uniqitemid', 'uniqitemgenerate' );

function uniqitemgenerate( $value ) {
	$date = new DateTime();

	return $date->format('Hdm') . uniqid('oid');
}

function phonize($phoneNumber, $country) {
    if($country):
    $countryCodes = array(
        'Nederland' => '+31',
        'België' => '+32',
        'Duitsland' => '+49'
    );
    
    return preg_replace('/[^0-9+]/', '', preg_replace('/^0/', $countryCodes[$country], $phoneNumber));
    else:
        return $phoneNumber;
    endif;
}