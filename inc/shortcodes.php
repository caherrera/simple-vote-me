<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 28/12/17
 * Time: 11:15 AM
 */
/** Snippet [simple-vote]**/
function gt_shortcode_simplevoteme($atributos){
    $atts = shortcode_atts( array(
        'type' => 'h',
    ), $atributos);


    $login = get_option('gt_simplevoteme_only_login');

    if( $login && !is_user_logged_in() )
        return('');

    $voteme = gt_simplevoteme_getvotelink(0, $atts["type"]);

    ob_start();
    echo $voteme;
    return ob_get_clean();

}
add_shortcode ('simplevoteme', 'gt_shortcode_simplevoteme');