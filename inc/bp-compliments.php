<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 29/12/17
 * Time: 12:38 PM
 */

function get_compliment_meta($compliment_id, $key = '', $single = false)
{
    return get_metadata('bp_compliments', $compliment_id, $key, $single);
}

function add_compliment_meta($compliment_id, $meta_key, $meta_value, $unique = false)
{
    $retval = add_metadata('bp_compliments', $compliment_id, $meta_key, $meta_value, $unique);

    return $retval;
}

function metadata_compliment_exists($compliment_id, $meta_key, $meta_value, $unique = false)
{
    $retval = metadata_exists('bp_compliments', $compliment_id, $meta_key, $meta_value, $unique);

    return $retval;
}

function update_compliment_meta($compliment_id, $meta_key, $meta_value, $prev_value = '')
{
    return update_metadata('bp_compliments', $compliment_id, $meta_key, $meta_value, $prev_value);
}

/** Ajax **/
function gt_simplevoteme_compliments_addvote()
{
    $results = '';
    global $wpdb;
    $compliment_ID = $_POST['complimentid'];
    $user_ID = $_POST['userid'];
    $type    = $_POST['tipo'];
    $votes   = get_compliment_meta($compliment_ID, '_simplevotemevotes', true) != "" ? get_post_meta($compliment_ID, '_simplevotemevotes',
        true) : array(
        'positives' => array(),
        'negatives' => array(),
        'neutrals'  => array()
    );

    switch ($type) {
        case 0:
            $votes['neutrals'][] = $user_ID;
            break;
        case 1:
            $votes['positives'][] = $user_ID;
            break;
        case 2:
            $votes['negatives'][] = $user_ID;
            break;
    }
    update_post_meta($compliment_ID, '_simplevotemevotes', $votes);


    $result = gt_simplevoteme_getvotelink(1);


    // Return the String
    die($result);
}

// creating Ajax call for WordPress
add_action('wp_ajax_nopriv_simplevoteme_compliments_addvote', 'gt_simplevoteme_compliments_addvote');
add_action('wp_ajax_simplevoteme_compliments_addvote', 'gt_simplevoteme_compliments_addvote');


function gt_simplevoteme_printvotelink_compliments_auto($content)
{

    $home = get_option('gt_simplevoteme_auto_insert_home');

    $auto = get_option('gt_simplevoteme_auto_insert_content');

    if ( ! $auto && (is_home() && ! $home)) {
        return ($content);
    }

    $login = get_option('gt_simplevoteme_only_login'); //after auto, do not waste resources if is not necessary :)


    if ($login && ! is_user_logged_in()) {
        return ($content);
    }


    $position = get_option('gt_simplevoteme_position');//after login, do not waste resources if is not necessary :)

    if (is_home() && $home) { //if is home and home is active
        if ( ! $position) {
            return $content . gt_simplevoteme_getvotelink();
        } else if ($position == 1) {
            return gt_simplevoteme_getvotelink() . $content;
        } else if ($position == 2) {
            $linksVote = gt_simplevoteme_getvotelink(); //launch just once

            return $linksVote . $content . $linksVote;
        } else {
            return $content;
        }//nothing expected

    } else if (($auto == 1 || $auto == 3) && is_single()) {//if is only post(1) or post&page(3)
        if ( ! $position) {
            return $content . gt_simplevoteme_getvotelink();
        } else if ($position == 1) {
            return gt_simplevoteme_getvotelink() . $content;
        } else if ($position == 2) {
            $linksVote = gt_simplevoteme_getvotelink(); //launch just once

            return $linksVote . $content . $linksVote;
        } else {
            return $content;
        }//nothing expected

    } else if (($auto == 2 || $auto == 3) && is_page()) {//if is only page(2) or post&page(3)
        if ( ! $position) {
            return $content . gt_simplevoteme_getvotelink();
        } else if ($position == 1) {
            return gt_simplevoteme_getvotelink() . $content;
        } else if ($position == 3) {
            $linksVote = gt_simplevoteme_getvotelink(); //launch just once

            return $linksVote . $content . $linksVote;
        } else {
            return $content;
        }//nothing expected
    } else {
        return ($content);
    } //nothing expected


}

add_filter('the_content', 'gt_simplevoteme_printvotelink_compliments_auto');