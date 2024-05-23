<?php

//ACF
function my_acf_json_load_point( $paths ) {
    // Append the new path and return it.
    $paths[] = get_stylesheet_directory() . '/acf-json/post-types';
    $paths[] = get_stylesheet_directory() . '/acf-json/taxonomy';
    $paths[] = get_stylesheet_directory() . '/acf-json/fields';
    $paths[] = get_stylesheet_directory() . '/acf-json/options';

    return $paths;    
}
add_filter( 'acf/settings/load_json', 'my_acf_json_load_point' );

function my_acf_cpt_save_folder( $path ) {
    return get_stylesheet_directory() . '/acf-json/post-types'; 
}
add_filter( 'acf/settings/save_json/type=acf-post-type', 'my_acf_cpt_save_folder' );


function my_acf_ctax_save_folder( $path ) {
    return get_stylesheet_directory() . '/acf-json/taxonomy'; 
}
add_filter( 'acf/settings/save_json/type=acf-taxonomy', 'my_acf_ctax_save_folder' );

function my_acf_cfields_save_folder( $path ) {
    return get_stylesheet_directory() . '/acf-json/fields'; 
}
add_filter( 'acf/settings/save_json/type=acf-field-group', 'my_acf_cfields_save_folder' );


function my_acf_coptions_save_folder( $path ) {
    return get_stylesheet_directory() . '/acf-json/options'; 
}
add_filter( 'acf/settings/save_json/type=acf-ui-options-page', 'my_acf_coptions_save_folder' );