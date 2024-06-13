<?php

function steefs_enqueue_scripts() {
	wp_enqueue_style( 'anur-style', get_stylesheet_directory_uri() .'/dist/css/style.css', array('style'), filemtime(get_stylesheet_directory() .'/dist/css/style.css'), 'all' );
}
add_action( 'wp_enqueue_scripts', 'steefs_enqueue_scripts', 20 );

function steefs_page_redirect() {
    if ( is_author() ) {
        wp_redirect( home_url() );
    }
    if ( is_archive() ) {
        wp_redirect( home_url() );
    }
}
add_action( 'template_redirect', 'steefs_page_redirect' );

// Disable /users rest routes
add_filter('rest_endpoints', function( $endpoints ) {
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
});

function steefs_get_post_by_item($item = false)
{
	if(!$item):
		if(isset($_GET['items'])):
			$item   = isset( $_GET['items'] ) ? sanitize_text_field( wp_unslash( $_GET['items'] ) ) : '';
		endif;
	endif;
    if($item):
        
		$postitem = steefs_check_all_codes($item);
		return $postitem;

	endif;
	return false;
}
function steefs_check_all_codes($item)
{   
    $args = array(
        'meta_key' => 'uniqid',
        'meta_value' => $item,
    );
    $postitem = steefs_get_post_by_meta($args );

    if(!$postitem):
        $args = array(
            'meta_key' => 'uniqid_ex',
            'meta_value' => $item,
        );
        $postitem = steefs_get_post_by_meta($args );
    endif;

    if(!$postitem):
        $args = array(
            'meta_key' => 'uniqid_ex2   ',
            'meta_value' => $item,
        );
        $postitem = steefs_get_post_by_meta($args );
    endif;
    return $postitem;


}
function steefs_get_post_by_meta( $args = array() )
{
   
    // Parse incoming $args into an array and merge it with $defaults - caste to object ##
    $args = ( object )wp_parse_args( $args );
   
    // grab page - polylang will take take or language selection ##
    $args = array(
        'meta_query'        => array(
            array(
                'key'       => $args->meta_key,
                'value'     => $args->meta_value
            )
        ),
        'post_type'         => array('arrangement','auto'),
        'posts_per_page'    => '1'
    );
   
    // run query ##
    $posts = get_posts( $args );
    // check results ##
    if ( ! $posts || is_wp_error( $posts ) ) return false;
   
    // test it ##
    #pr( $posts[0] );
   
    // kick back results ##
    return $posts[0];
}


