<?php
/*
Plugin Name: Simple Vote Me
Plugin URI: https://wordpress.org/plugins/simple-vote-me/
Description: This plugin add cute and simple votes for Wordpress post.
Author: Gonzalo Torreras
Version: 1.3.1
Author URI: http://www.gonzalotorreras.com
*/

define( 'SIMPLEVOTEMESURL', WP_PLUGIN_URL . "/" . dirname( plugin_basename( __FILE__ ) ) );

include_once( plugin_dir_path( __FILE__ ) . '/classes/VoteOption.php' );
include_once( plugin_dir_path( __FILE__ ) . '/admin.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/setup.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/functions.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/list-votes.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/shortcodes.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/bp-compliments.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/widgets/GTSimpleVoteMeBaseWidget.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/widgets/GTSimpleVoteMeTopVotedWidget.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/widgets/GTSimpleVoteMeWidget.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/init.php' );


global $gt_simplevoteme_version;
$gt_simplevoteme_version = "2.0";


function gt_simplevoteme_enqueuescripts() {

	wp_register_style( 'simplevotemestyle', SIMPLEVOTEMESURL . '/css/simplevoteme.css' );

	wp_register_style( 'simplevotemestyleadmin', SIMPLEVOTEMESURL . '/css/simplevotemeadmin.css' );

	wp_enqueue_script( 'gtsimplevoteme', SIMPLEVOTEMESURL . '/js/simple-vote-me.js', array( 'jquery' ) );


	wp_localize_script( 'gtsimplevoteme', 'gtsimplevotemeajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	$css = get_option( 'gt_simplevoteme_custom_default_css' );

	if ( ! $css )//default = yes = 0
	{
		wp_enqueue_style( 'simplevotemestyle' );
	}

}

add_action( 'wp_enqueue_scripts', 'gt_simplevoteme_enqueuescripts' );


function gt_simplevoteme_check_previous_votes( $votes, $user ) {
	foreach ( $votes as $v ) {
		if ( array_key_exists( $user, $v ) ) {
			return true;
		}
	}

	return false;
}

function gt_simplevoteme_getvotelink( $noLinks = false, $post_ID = false, $style = 'h' ) {
	$vote_options = gt_simplevoteme_get_vote_options();
	$user_ID      = get_current_user_id();

	foreach ( $vote_options as $vote_option ) {
		$vote_option->setType( 'post' );
	}

	if ( ! $post_ID ) {
		if ( ! $noLinks ) {
			$post_ID = get_the_ID();
		} else {
			$post_ID = $_POST['post_id'];
		}
	}

	$votes  = gt_simplevoteme_get_post_meta( $post_ID, true );
	$result = gt_simplevoteme_print_result( $noLinks, $votes, $vote_options, $user_ID, $post_ID, $style );

	return $result;
}

function gt_simplevoteme_print_result( $noLinks, $votes, $vote_options, $user_ID, $ID, $style ) {
	$limitVotesPerUser = get_option( 'gt_simplevoteme_votes' );

	if ( $limitVotesPerUser && gt_simplevoteme_check_previous_votes( $votes, $user_ID ) ) {
		$noLinks = 1;
	}//check if there are limit per user and the user is in array, if is $nolinks = 1

	$vote_options = gt_simplevoteme_load_votes( $vote_options, $votes, $noLinks );

	$votemelink = gt_simplevoteme_links( $vote_options, $style, $user_ID, $ID );

	$result = $votemelink . gt_simplevoteme_draw_list_votes( $votes, $ID );

	return $result;
}

function gt_simplevoteme_load_votes( $vote_options, $votes, $noLinks ) {
	foreach ( $vote_options as $vote_option ) {
		$vote_option->setVotes( $votes );
		$vote_option->setAllowLink( ! $noLinks );
	}

	return $vote_options;
}

/**
 * @param VoteOption[] $vote_options
 * @param $style
 * @param $ID
 *
 * @return string
 */
function gt_simplevoteme_links( $vote_options, $style, $user_id, $ID ) {
	$title      = get_option( 'gt_simplevoteme_title' );
	$votemelink = "<div class='simplevotemeWrapper $style' id='simplevoteme-$ID' >$title";
	foreach ( $vote_options as $vote_option ) {
		$votemelink .= sprintf( "<span class='%s' id='SimpleVoteMeVoteOption%s' data-key='%s'>%s<span class='result'>%s</span></span>",
			$vote_option->name, $vote_option->id,$vote_option->id, $vote_option->getVoteLink( $ID, $user_id ),
			$vote_option->getResult() );
	}

	$imgloading = SIMPLEVOTEMESURL . '/img/ajax_loader_red_32.gif';
	$votemelink .= "</div><script type='text/javascript'>var simplevotemeLoading='$imgloading';</script>";

	return $votemelink;
}


function gt_simplevoteme_printvotelink_auto( $content ) {

	$home = get_option( 'gt_simplevoteme_auto_insert_home' );

	$auto = get_option( 'gt_simplevoteme_auto_insert_content' );

	if ( ! $auto && ( is_home() && ! $home ) ) {
		return ( $content );
	}

	$login = get_option( 'gt_simplevoteme_only_login' ); //after auto, do not waste resources if is not necessary :)


	if ( $login && ! is_user_logged_in() ) {
		return ( $content );
	}


	$position = get_option( 'gt_simplevoteme_position' );//after login, do not waste resources if is not necessary :)

	if ( is_home() && $home ) { //if is home and home is active
		if ( ! $position ) {
			return $content . gt_simplevoteme_getvotelink();
		} else if ( $position == 1 ) {
			return gt_simplevoteme_getvotelink() . $content;
		} else if ( $position == 2 ) {
			$linksVote = gt_simplevoteme_getvotelink(); //launch just once

			return $linksVote . $content . $linksVote;
		} else {
			return $content;
		}//nothing expected

	} else if ( ( $auto == 1 || $auto == 3 ) && is_single() ) {//if is only post(1) or post&page(3)
		if ( ! in_array( get_post_type(), get_option( 'gt_simplevoteme_custom_post_types' ) ) ) {
			return $content;
		}

		if ( ! $position ) {
			return $content . gt_simplevoteme_getvotelink();
		} else if ( $position == 1 ) {
			return gt_simplevoteme_getvotelink() . $content;
		} else if ( $position == 2 ) {
			$linksVote = gt_simplevoteme_getvotelink(); //launch just once

			return $linksVote . $content . $linksVote;
		} else {
			return $content;
		}//nothing expected

	} else if ( ( $auto == 2 || $auto == 3 ) && is_page() ) {//if is only page(2) or post&page(3)
		if ( ! in_array( get_post_type(), get_option( 'gt_simplevoteme_custom_post_types' ) ) ) {
			return $content;
		}

		if ( ! $position ) {
			return $content . gt_simplevoteme_getvotelink();
		} else if ( $position == 1 ) {
			return gt_simplevoteme_getvotelink() . $content;
		} else if ( $position == 3 ) {
			$linksVote = gt_simplevoteme_getvotelink(); //launch just once

			return $linksVote . $content . $linksVote;
		} else {
			return $content;
		}//nothing expected
	} else {
		return ( $content );
	} //nothing expected


}

add_filter( 'the_content', 'gt_simplevoteme_printvotelink_auto' );


/**
 * @deprecated
 */
function gt_simplevoteme_aftervote() {

}

/** Ajax **/
function gt_simplevoteme_addvote() {

	$post_ID       = $_POST['post_id'];
	$user_ID       = $_POST['user_id'];
	$vote_selected = $_POST['vote_selected'];
	$votes         = gt_simplevoteme_get_post_meta( $post_ID );

	$votes = gt_simplevoteme_insertvote( $votes, $user_ID, $vote_selected );
	if ( update_post_meta( $post_ID, '_simplevotemevotes', $votes )!==false ) {
		gt_simplevoteme_send_json_success( $votes );
	} else {
		wp_send_json_error();
	}
}


// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_simplevoteme_addvote', 'gt_simplevoteme_addvote' );
add_action( 'wp_ajax_simplevoteme_addvote', 'gt_simplevoteme_addvote' );


function gt_simplevoteme_get_post_meta( $post_id, $userdata = false ) {
	$votes = get_post_meta( $post_id, '_simplevotemevotes', true );

	if ( ! is_array( $votes ) ) {
		$votes = [];
	}
	$votes = wp_parse_args( $votes, $init_votes=gt_simplevoteme_init_votes() );
	$votes = array_intersect_key( $votes, $init_votes );
	if ( $userdata ) {
		$votes = array_map( function ( $voteType ) {
			return array_map( 'gt_simplevoteme_get_userdata', $voteType );
		}, $votes );
	}

	return $votes;
}
  
