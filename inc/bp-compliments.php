<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 29/12/17
 * Time: 12:38 PM
 */
define('BP_COMPLIMENT_META_TYPE', 'bp_compliments');
function get_compliment_meta($compliment_id, $key = '', $single = false)
{
    return get_metadata(BP_COMPLIMENT_META_TYPE, $compliment_id, $key, $single);
}

function add_compliment_meta($compliment_id, $meta_key, $meta_value, $unique = false)
{
    $retval = add_metadata(BP_COMPLIMENT_META_TYPE, $compliment_id, $meta_key, $meta_value, $unique);

    return $retval;
}

function metadata_compliment_exists($compliment_id, $meta_key, $meta_value, $unique = false)
{
    $retval = metadata_exists(BP_COMPLIMENT_META_TYPE, $compliment_id, $meta_key, $meta_value, $unique);

    return $retval;
}

function update_compliment_meta($compliment_id, $meta_key, $meta_value, $prev_value = '')
{
    return update_metadata(BP_COMPLIMENT_META_TYPE, $compliment_id, $meta_key, $meta_value, $prev_value);
}

/** Ajax **/
function gt_simplevoteme_compliments_addvote()
{
    $results = '';
    global $wpdb;
    $compliment_ID = $_POST['complimentid'];
    $user_ID       = $_POST['userid'];
    $type          = $_POST['tipo'];
    $votes         = get_compliment_votes($compliment_ID);


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
    update_compliment_meta($compliment_ID, '_simplevotemevotes', $votes);


    $result = gt_simplevoteme_compliment_getvotelink(1, $compliment_ID);


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

function gt_simplevoteme_compliment_getvotelink($noLinks = false, $compliment_id = false, $tipo = 'h')
{
    $votemelink        = "";
    $user_ID           = get_current_user_id();
    $limitVotesPerUser = get_option('gt_simplevoteme_votes');


    if ( ! $compliment_id) {
        if (isset($_POST['complimentid']) && filter_var($_POST['complimentid'], FILTER_VALIDATE_INT)) {
            $compliment_id = $_POST['complimentid'];
        } else {
            wp_die('must give compliment_id');
        }
    }


    $votes = get_compliment_votes($compliment_id);
    //if no limit votes per user or user not logged
    if ($limitVotesPerUser && $user_ID != 0 && (in_array($user_ID, $votes['positives']) || in_array($user_ID,
                $votes['negatives']) || in_array($user_ID, $votes['neutrals']))) {
        $noLinks = 1;
    }//check if there are limit per user and the user is in array, if is $nolinks = 1

    $votemePositive = count($votes['positives']);

    $votemeNeutral = count($votes['neutrals']);

    $votemeNegative = count($votes['negatives']);

    $votemeTotal = sizeof($votes, 1) - 3; //rest 3 because arrays for separate votes counts.

    $votemeResults     = get_option('gt_simplevoteme_results');
    $votemeResultsType = get_option('gt_simplevoteme_results_type');
    if ($votemeResults) {
        if ($votemeResults == 1 || ($votemeResults == 2 && $noLinks)) {

            if ($votemeTotal != 0 || $votemeTotal != '') {

                if ($votemeNegative > 0) //if there are votes
                {
                    $percentNegative = round($votemeNegative / $votemeTotal, 2) * 100 . "%";
                } else {
                    $percentNegative = "0%";
                }

                if ($votemeResultsType == 2)//just total votes
                {
                    $votemePercentNegative = $votemeNegative;
                } else if ($votemeResultsType == 1)//only percentages
                {
                    $votemePercentNegative = $percentNegative;
                } else //all
                {
                    $votemePercentNegative = "$percentNegative<small> ($votemeNegative) </small>";
                }


                if ($votemeNeutral > 0) //if there are votes
                {
                    $percentNeutral = round($votemeNeutral / $votemeTotal, 2) * 100 . "%";
                } else {
                    $percentNeutral = "0%";
                }

                if ($votemeResultsType == 2)//just total votes
                {
                    $votemePercentNeutral = $votemeNeutral;
                } else if ($votemeResultsType == 1)//only percentages
                {
                    $votemePercentNeutral = $percentNeutral;
                } else //all
                {
                    $votemePercentNeutral = "$percentNeutral<small> ($votemeNeutral) </small>";
                }


                if ($votemePositive > 0) {
                    $percentPositive = round($votemePositive / $votemeTotal, 2) * 100 . "%";
                } else {
                    $percentPositive = "0%";
                }

                if ($votemeResultsType == 2)//just total votes
                {
                    $votemePercentPositive = $votemePositive;
                } else if ($votemeResultsType == 1)//only percentages
                {
                    $votemePercentPositive = $percentPositive;
                } else //all
                {
                    $votemePercentPositive = "$percentPositive<small> ($votemePositive) </small>";
                }


            } else {
                $votemePercentNegative = "";
                $votemePercentNeutral  = "";
                $votemePercentPositive = "";
            }
        } else {
            $votemePercentNegative = "";
            $votemePercentNeutral  = "";
            $votemePercentPositive = "";
        }

    } else {

        $votemePercentNegative = "";
        $votemePercentNeutral  = "";
        $votemePercentPositive = "";
    }

    if ( ! $noLinks) {

        $linkPositivo = '<a onclick="simplevotemeaddvotecompliment(' . $compliment_id . ', 1,' . $user_ID . ');">' . gt_simplevoteme_getimgvote("good") . '</a>';
        $linkNegativo = '<a onclick="simplevotemeaddvotecompliment(' . $compliment_id . ', 2,' . $user_ID . ');">' . gt_simplevoteme_getimgvote("bad") . '</a>';
        $linkNeutral  = '<a onclick="simplevotemeaddvotecompliment(' . $compliment_id . ', 0,' . $user_ID . ');">' . gt_simplevoteme_getimgvote("neutral") . '</a>';
    } else {
        $linkPositivo = gt_simplevoteme_compliment_getimgvote("good");
        $linkNegativo = gt_simplevoteme_compliment_getimgvote("bad");
        $linkNeutral  = gt_simplevoteme_compliment_getimgvote("neutral");
    }

    $title = get_option('gt_simplevoteme_title');

    $votemelink = "<div class='simplevotemeWrapper $tipo' id='simplevoteme-$compliment_id' >$title";
    $votemelink .= "<span class='good'>$linkPositivo <span class='result'>$votemePercentPositive</span></span>";
    $votemelink .= "<span class='neutro'>$linkNeutral <span class='result'>$votemePercentNeutral</span></span>";
    $votemelink .= "<span class='bad'>$linkNegativo <span class='result'>$votemePercentNegative</span></span>";
    $votemelink .= "</div>";

    $result = $votemelink;


    return $result;
}

function get_compliment_votes($compliment_id = false)
{
    $votes = get_compliment_meta($compliment_id, '_simplevotemevotes', true);
    if ( ! $votes || ! $compliment_id) {
        $votes = [
            'positives' => [],
            'negatives' => [],
            'neutrals'  => [],
        ];
    }

    return $votes;
}

function gt_simplevoteme_compliment_getimgvote($type)
{
    $custom    = get_option('gt_simplevoteme_compliment_custom_img');
    $customImg = get_option("gt_simplevoteme_compliment_custom_img_$type");

    if ( ! $custom || ($custom && ! $customImg)) {
        return "<img src='" . SIMPLEVOTEMESURL . "/img/$type.png'/>";
    } else {
        return "<img src='$customImg'/>";
    }
}