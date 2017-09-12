<?php
/*
WPMU plugin to allow 'embed', 'object', 'param' tags
writted by Nada O'Neal, 05/06/2009
*/

// disable the upgrade message
// revised for 2.8.x
//remove_action( 'wp_version_check', 'wp_version_check' );
//remove_action( 'admin_init', '_maybe_update_core' );
//add_filter( 'pre_transient_update_core', create_function( '$a', "return null;" ) );


function cul_kses_add_tags($content) {
    $content += array(
        'object' => array(
            'width' => array(),
            'height' => array(),
            'data' => array(),
            'type' => array(),
            'classid' => array(),
            ), 
        'param' => array(
            'name' => array(),
            'value' => array(),
            ), 
        'iframe' => array(
            'src' => array(),
            'style' => array(),
            'width' => array(),
            'height' => array(),
            'frameborder' => array(),
            'scrolling' => array(),
            ),
        'embed' => array(
            'src' => array(),
            'type' => array(),
            'bgcolor' => array(),
            'allowfullscreen' => array(),
            'flashvars' => array(),
            'wmode' => array(),
            'width' => array(),
            'height' => array(),
            'quality' => array(),
            'style' => array(),
            'id' => array(),
            'flashvars' => array(),
            )
        );
    return $content;
}
add_filter('edit_allowedposttags', 'cul_kses_add_tags');
?>
