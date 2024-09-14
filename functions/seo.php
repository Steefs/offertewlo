<?php
function datalayerecommerce_func(){
    ob_start();

    if(isset($_GET['oid'])):
        $offerte_form_id = get_field('offerte_form_id' , 'options');
        $offerteid_veld_id = get_field('offerteid_veld_id' , 'options');
        $unique_code_veld_id = get_field('unique_code_veld_id' , 'options');
        $huurdatum_veld_id = get_field('huurdatum_veld_id' , 'options');

      
        $search_criteria = array(
            'status'        => 'active',
            'field_filters' => array(
                'mode' => 'any',
                array(
                    'key'   =>  $unique_code_veld_id,
                    'value' => $_GET['oid']
                )
            )
        );
        $offerteid = false;
        // Getting the entries
        $entries = GFAPI::get_entries(  $offerte_form_id, $search_criteria );
        foreach($entries as $entrie):
            $offerteid = rgar( $entrie, $offerteid_veld_id );
            $huurdatum = rgar( $entrie, $huurdatum_veld_id );

        endforeach;
        if( $offerteid):

            require_once('api.class.php');
            $token = 'aZUCIuLmH3UHLsT0s0vC9mmLuTc3ve'; //Your API token
            $api = new gripp_API($token);
    
            $filters = array(
                array(
                    "field" => "offer.id",
                    "operator" => "equals",    	
                    "value" => $offerteid
                )
            );
            $responses = $api->offer_getone($filters);
            $response = $responses[0]['result'];
            if($response['rows']):

                $price = '';
                $cat = '';

                $companyid =  $response['rows'][0]['company']['id'];
                $datalayer = array();
                $datalayer['event'] = 'offerte_aangevraagd'; 
                $datalayer['klantnr'] = $companyid; 
                $datalayer['ecommerce'] = [
                    'transaction_id' => $response['rows'][0]['number'],
                    'trouwdatum' => $huurdatum
                ]; 
                foreach($response['rows'][0]['tags'] as $tag):
                    $cat = $tag['searchname'];
                    break;

                endforeach;
                $items = array();
                foreach($response['rows'][0]['offerlines'] as $offerline):

                    $items[] = [
                        'item_name' => $offerline['product']['searchname'],
                        'item_category' => $cat,
                        'price' => $offerline['sellingprice'],
                        'quantity' => 1
                    ]; 
                    $price = $offerline['sellingprice'];
                endforeach;

                $datalayer['ecommerce']['value'] = $price;
                $datalayer['items'] =  $items;

                echo '<script>';
                echo 'window.dataLayer.push({ ecommerce: null });';

                echo 'window.dataLayer.push(' . wp_json_encode( $datalayer ) . ');';
                echo '</script>';
            endif;

        endif;
endif;
	$return = ob_get_contents();
	ob_get_clean();
	return $return;
}
add_shortcode( 'datalayerecommerce', 'datalayerecommerce_func' );
