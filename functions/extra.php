<?php
add_action('init', 'anur_cron_activation');
function anur_cron_activation() {
    if (! wp_next_scheduled ( 'gripp_api_update' )) {
      wp_schedule_event(time(), 'hourly', 'gripp_api_update');
    }
}
add_action('gripp_api_update', 'gripp_api_update_offers');
function gripp_api_update_offers() {
  if(get_bloginfo('url') == 'https://www.devolkswagenbus.nl/offerte'):

    get_gripp_offertes();
  endif;
}



//[foobar]
function get_gripp_offertes(){
	//ob_start();
   //echo get_bloginfo('url');
    $gripp_sync_last = get_option( 'gripp_sync_last' );
    $updateid = get_option( 'gripp_last_number' );

	require_once('api.class.php');
	$token = 'aZUCIuLmH3UHLsT0s0vC9mmLuTc3ve'; //Your API token
	$api = new gripp_API($token);
	$filters = array(
        array(
            "field" => "offer.id",
            "operator" => "greater",
            "value" => $updateid
        )
    );
    
    $options = array(
        "paging" => array(
            "firstresult" => 0,
            "maxresults" => 1
        ),
        "orderings" => array(
            array(
                "field" => "offer.id",
                "direction" => "asc"
            )
        )
    );

    $batchresponse = $api->offer_get($filters, $options);
    $response = $batchresponse[0]['result'];
    $nr = 1;
    if($response['rows']):
        foreach($response['rows'] as $row):
            //echo '<pre>';
           //	print_r($row);
//echo '</pre>';
            if($nr == 1):
                $updateid = $row['id'];
            endif;



            $companyfilters = array(
                array(
                    "field" => "company.id",
                    "operator" => "like",    	
                    "value" => $row['company']['id']
                )
            );
            $companyresponses = $api->company_getone($companyfilters);
            $companyresponse = $companyresponses[0]['result'];
            if($companyresponse['rows']):
                $email = $companyresponse['rows'][0]['email'];
                $plaats = $companyresponse['rows'][0]['visitingaddress_city'];
            endif;


            $name = $row['name'];
            $van = $row['customfield_huurdatum'];
            $tot = $row['customfield_huurdatumtot'];
            $status = $row['status']['searchname'];
            $offerte = $row['name'];
            $number = $row['number'];
            $fase = $row['phase']['searchname'];
            $Offertedatum = $row['updatedon']['date'];
            if($row['date']):
             $Offertedatum = $row['date']['date'];
             else:
             $Offertedatum = $row['createdon']['date'];
             endif;
            $Conceptdatum = $row['createdon']['date'];
            $Basiswaarde = $row['totalinclvat'];
            $Acceptatiewaarde = $row['totalexclvat'];
            $Optioneel = '';
            $Geaccepteerd = $row['number'];
            $Acceptatiestatus = $row['acceptancestatus']['searchname'];
            $tags = '';//
            $tagnr = 0;
            if($row['tags']):
                foreach($row['tags'] as $tag):
                    $tagnr++;
                    if(tagnr > 1):
                        $tags .= ', ';
                    endif;
                    $tags .= $tag['searchname'];
                endforeach;
            endif;
           
            $Identiteit = $row['identity']['searchname'];
            $clientid = $row['clientreference'];
             $Oldtimer = $row['customfield_oldtimer'];
             $Gelegenheid = $row['customfield_gelegenheid'];
             $Website = $row['customfield_website'];
            $relatie = $row['company']['searchname'];
            $rowresultnumber = $row['id'];

            
            $form_id = 3;
            $input_values  = array();
            $input_values['input_1'] = $status;
            $input_values['input_3'] = $offerte;
            $input_values['input_4'] = $number;
            $input_values['input_5'] = $fase;
            if($Offertedatum):
                $input_values['input_6'] = substr($Offertedatum, 0, 10);
            endif;
            if($Conceptdatum):
                $input_values['input_7'] =  substr($Conceptdatum, 0, 10);
            endif;
            $input_values['input_8'] = $Basiswaarde;
            $input_values['input_9'] = $Acceptatiewaarde;
            $input_values['input_10'] = $Optioneel;
            $input_values['input_11'] = $Acceptatiewaarde;
            if($Acceptatiestatus == 'Geaccepteerd'):
            $input_values['input_12'] = 'Ja';
                        $input_values['input_13'] = 1;

            else:
            $input_values['input_12'] = 'Nee';
                                    $input_values['input_13'] = 2;

            endif;
            $input_values['input_14'] = $tags;
            $input_values['input_15'] = $email;
                        $input_values['input_16'] = $plaats;

            $input_values['input_17'] = $Identiteit;
            $input_values['input_18'] = $clientid;
            $input_values['input_19'] = $van;
            $input_values['input_20'] = $tot;
            $input_values['input_21'] = $Oldtimer;
            $input_values['input_22'] = $Gelegenheid;
            $input_values['input_23'] = $Website;
            $input_values['input_24'] = $relatie;
            $input_values['input_25'] = $rowresultnumber;

            $result = GFAPI::submit_form( $form_id, $input_values );                
            $nr++;
        endforeach;
    endif;

    $date = new DateTime();
    $date->setTimestamp($gripp_sync_last);

	
    $d = new DateTime();

    update_option( 'gripp_sync_last', $d->getTimestamp(), '', 'no' );
    update_option( 'gripp_last_number', $updateid, '', 'no' );



   /*$return = ob_get_contents();
   ob_get_clean();
	return $return;
    */

}
add_shortcode( 'testing', 'get_gripp_offertes' );
