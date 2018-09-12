<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 29/12/17
 * Time: 12:38 PM
 */
define( 'BP_COMPLIMENT_META_TYPE', 'bp_compliments' );
function get_compliment_meta( $compliment_id, $key = '', $single = false ) {
	return get_metadata( BP_COMPLIMENT_META_TYPE, $compliment_id, $key, $single );
}

function add_compliment_meta( $compliment_id, $meta_key, $meta_value, $unique = false ) {
	$retval = add_metadata( BP_COMPLIMENT_META_TYPE, $compliment_id, $meta_key, $meta_value, $unique );

	return $retval;
}

function metadata_compliment_exists( $compliment_id, $meta_key, $meta_value, $unique = false ) {
	$retval = metadata_exists( BP_COMPLIMENT_META_TYPE, $compliment_id, $meta_key, $meta_value, $unique );

	return $retval;
}

function update_compliment_meta( $compliment_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( BP_COMPLIMENT_META_TYPE, $compliment_id, $meta_key, $meta_value, $prev_value );
}

/** Ajax **/
function gt_simplevoteme_compliments_addvote() {

	$compliment_ID = $_POST['compliment_id'];
	$user_ID       = $_POST['user_id'];
	$vote_selected = $_POST['vote_selected'];
	$votes         = gt_simplevoteme_get_compliment_votes( $compliment_ID );

	if ( $user_ID && $vote_selected ) {
		$votes = gt_simplevoteme_insertvote( $votes, $user_ID, $vote_selected );
		if ( is_wp_error( update_compliment_meta( $compliment_ID, '_simplevotemevotes', $votes ) ) ) {
			wp_send_json_error();
		}
	}
	gt_simplevoteme_send_json_success( $votes );
}

// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_simplevoteme_compliments_addvote', 'gt_simplevoteme_compliments_addvote' );
add_action( 'wp_ajax_simplevoteme_compliments_addvote', 'gt_simplevoteme_compliments_addvote' );


function gt_simplevoteme_compliment_getvotelink( $noLinks = false, $compliment_id = false, $style = 'h' ) {
	$vote_options = gt_simplevoteme_get_vote_options();
	$user_ID      = get_current_user_id();

	foreach ( $vote_options as $vote_option ) {
		$vote_option->setType( 'compliment' );
	}

	if ( ! $compliment_id ) {
		if ( isset( $_POST['compliment_id'] ) && filter_var( $_POST['compliment_id'], FILTER_VALIDATE_INT ) ) {
			$compliment_id = $_POST['compliment_id'];
		} else {
			wp_die( 'must give compliment_id' );
		}
	}


	$votes  = gt_simplevoteme_get_compliment_votes( $compliment_id, true );
	$result = gt_simplevoteme_print_result( 'compliment', $noLinks, $votes, $vote_options, $user_ID, $compliment_id,
		$style );

	return $result;
}

function gt_simplevoteme_get_compliment_votes( $compliment_id = false ) {
	$votes = get_compliment_meta( $compliment_id, '_simplevotemevotes', true );
	if ( ! is_array( $votes ) ) {
		$votes = [];
	}
	$votes = wp_parse_args( $votes, gt_simplevoteme_init_votes() );

	$votes = gt_simplevoteme_filter_userdata( $votes );

	return $votes;
}


function gt_simplevoteme_compliment_getimgvote( $type ) {
	$customImg = get_option( "gt_simplevoteme_compliment_custom_img_$type" );

	if ( ! $customImg ) {
		return "<img src='" . SIMPLEVOTEMESURL . "/img/$type.png'/>";
	} else {
		return "<img src='$customImg'/>";
	}
}