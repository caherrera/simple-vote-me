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

include_once( plugin_dir_path( __FILE__ ) . '/admin.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/list-votes.php' );
//    include_once(plugin_dir_path(__FILE__) .'/inc/functions.php');
include_once( plugin_dir_path( __FILE__ ) . '/inc/shortcodes.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/bp-compliments.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/widgets/GTSimpleVoteMeBaseWidget.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/widgets/GTSimpleVoteMeTopVotedWidget.php' );
include_once( plugin_dir_path( __FILE__ ) . '/inc/widgets/GTSimpleVoteMeWidget.php' );

function gt_simplevoteme_init() {
	register_widget( 'GTSimpleVoteMeTopVotedWidget' );
	register_widget( 'GTSimpleVoteMeWidget' );

}

add_action( 'widgets_init', 'gt_simplevoteme_init' );


global $gt_simplevoteme_version;
$gt_simplevoteme_version = "1.3.1";

function gt_simplevoteme_checkversion() {
	global $gt_simplevoteme_version;

	$version = get_option( 'gt_simplevoteme_version' );

	if ( $version === false ) {
		//install plugin


		//check if there are old system of votes
		gt_simplevoteme_check_old_votes();


	} else if ( $version != $gt_simplevoteme_version ) {
		//update tables,vars etc.
	}
	gt_simplevoteme_check_bp_compliments_activate();

	update_option( 'gt_simplevoteme_version', $gt_simplevoteme_version );

}

function gt_simplevoteme_registermeta() {
	global $wpdb;
	$meta_table        = BP_COMPLIMENT_META_TYPE . 'meta';
	$wpdb->$meta_table = $wpdb->prefix . $meta_table;
}

add_action( 'plugins_loaded', 'gt_simplevoteme_checkversion' );
add_action( 'plugins_loaded', 'gt_simplevoteme_registermeta' );


function gt_simplevoteme_check_old_votes() {

	$posts = get_posts( 'meta_key=_simplevotemetotal&amp;' );

	if ( $posts ) {
		$votes = array( 'positives' => array(), 'negatives' => array(), 'neutrals' => array() );
		foreach ( $posts as $post ) {

			$pos = get_post_meta( $post->ID, '_simplevotemepositive', true ) ? get_post_meta( $post->ID,
				'_simplevotemepositive', true ) : 0;
			$neg = get_post_meta( $post->ID, '_simplevotemenegative', true ) ? get_post_meta( $post->ID,
				'_simplevotemenegative', true ) : 0;
			$neu = get_post_meta( $post->ID, '_simplevotemeneutral', true ) ? get_post_meta( $post->ID,
				'_simplevotemeneutral', true ) : 0;

			for ( $i = 0; $i < $pos; $i ++ ) {
				$votes['positives'][] = '0'; //add votes for positive with user_ID 0 like annonymous
			}
			for ( $i = 0; $i < $neg; $i ++ ) {
				$votes['negatives'][] = '0'; //add votes for positive with user_ID 0 like annonymous
			}
			for ( $i = 0; $i < $neu; $i ++ ) {
				$votes['neutrals'][] = '0'; //add votes for positive with user_ID 0 like annonymous
			}

			update_post_meta( $post->ID, '_simplevotemevotes', $votes );

			//echo "updating gt_svtm</br>neg:$neg</br>pos:$pos</br>neu:$neu";
			//print_r($votes);
			delete_post_meta( $post->ID, '_simplevotemetotal', "" );
			delete_post_meta( $post->ID, '_simplevotemepositive', "" );
			delete_post_meta( $post->ID, '_simplevotemenegative', "" );
			delete_post_meta( $post->ID, '_simplevotemeneutral', "" );


		}
	}


}

function gt_simplevoteme_check_bp_compliments_activate() {
	global $bp, $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	if ( defined( 'BP_COMPLIMENTS_TABLE' ) ) {
		$charset_collate = ! empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
		$table           = BP_COMPLIMENTS_TABLE . "meta";
		update_option( 'gt_simplevoteme_bp_compliments_table', $table );
		$exist = $wpdb->get_results( "show tables like '$table';", true );
		if ( ! $exist ) {
			$sql = "CREATE TABLE `$table` (";
			$sql .= "`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,";
			$sql .= "`bp_compliments_id` bigint(20) unsigned NOT NULL DEFAULT '0',";
			$sql .= "`meta_key` varchar(255) DEFAULT NULL,";
			$sql .= "`meta_value` longtext,";
			$sql .= "PRIMARY KEY (`meta_id`),";
			$sql .= "KEY `bp_compliments_id` (`bp_compliments_id`),";
			$sql .= "KEY `meta_key` (`meta_key`(191))";
			$sql .= ") ENGINE=InnoDB {$charset_collate}";

			dbDelta( $sql );
		}
	}

}

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


function gt_simplevoteme_getimgvote( $type ) {
	$customImg = get_option( "gt_simplevoteme_custom_img_$type" );
	if ( ! $customImg ) {
		return "<img class=\"gt_simplevoteme_custom_img_$type\" src='" . SIMPLEVOTEMESURL . "/img/$type.png'/>";
	} else {
		return "<img src='$customImg' class=\"gt_simplevoteme_custom_img_$type\" />";
	}
}

function gt_simplevoteme_getvotelink( $noLinks = false, $post_ID = false, $tipo = 'h' ) {
	$votemelink        = "";
	$user_ID           = get_current_user_id();
	$limitVotesPerUser = get_option( 'gt_simplevoteme_votes' );

	if ( ! $post_ID ) {
		if ( ! $noLinks ) {
			$post_ID = get_the_ID();
		} else {
			$post_ID = $_POST['postid'];
		}
	}

	$votes = get_post_meta( $post_ID, '_simplevotemevotes', true ) != "" ? get_post_meta( $post_ID,
		'_simplevotemevotes',
		true ) : array(
		'positives' => array(),
		//id users array
		'negatives' => array(),
		'neutrals'  => array(),
	);
	//if no limit votes per user or user not logged
	if ( $limitVotesPerUser && $user_ID != 0 && ( in_array( $user_ID, $votes['positives'] ) || in_array( $user_ID,
				$votes['negatives'] ) || in_array( $user_ID, $votes['neutrals'] ) ) ) {
		$noLinks = 1;
	}//check if there are limit per user and the user is in array, if is $nolinks = 1

	$votemePositive = count( $votes['positives'] );

	$votemeNeutral = count( $votes['neutrals'] );

	$votemeNegative = count( $votes['negatives'] );

	$votemeTotal = sizeof( $votes, 1 ) - 3; //rest 3 because arrays for separate votes counts.

	$votemeResults     = get_option( 'gt_simplevoteme_results' );
	$votemeResultsType = get_option( 'gt_simplevoteme_results_type' );
	if ( $votemeResults ) {
		if ( $votemeResults == 1 || ( $votemeResults == 2 && $noLinks ) ) {

			if ( $votemeTotal != 0 || $votemeTotal != '' ) {

				if ( $votemeNegative > 0 ) //if there are votes
				{
					$percentNegative = round( $votemeNegative / $votemeTotal, 2 ) * 100 . "%";
				} else {
					$percentNegative = "0%";
				}

				if ( $votemeResultsType == 2 )//just total votes
				{
					$votemePercentNegative = $votemeNegative;
				} else if ( $votemeResultsType == 1 )//only percentages
				{
					$votemePercentNegative = $percentNegative;
				} else //all
				{
					$votemePercentNegative = "$percentNegative<small> ($votemeNegative) </small>";
				}


				if ( $votemeNeutral > 0 ) //if there are votes
				{
					$percentNeutral = round( $votemeNeutral / $votemeTotal, 2 ) * 100 . "%";
				} else {
					$percentNeutral = "0%";
				}

				if ( $votemeResultsType == 2 )//just total votes
				{
					$votemePercentNeutral = $votemeNeutral;
				} else if ( $votemeResultsType == 1 )//only percentages
				{
					$votemePercentNeutral = $percentNeutral;
				} else //all
				{
					$votemePercentNeutral = "$percentNeutral<small> ($votemeNeutral) </small>";
				}


				if ( $votemePositive > 0 ) {
					$percentPositive = round( $votemePositive / $votemeTotal, 2 ) * 100 . "%";
				} else {
					$percentPositive = "0%";
				}

				if ( $votemeResultsType == 2 )//just total votes
				{
					$votemePercentPositive = $votemePositive;
				} else if ( $votemeResultsType == 1 )//only percentages
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

	if ( ! $noLinks ) {

		$linkPositivo = '<a onclick="simplevotemeaddvote(' . $post_ID . ', 1,' . $user_ID . ',this);">' . gt_simplevoteme_getimgvote( "good" ) . '</a>';
		$linkNegativo = '<a onclick="simplevotemeaddvote(' . $post_ID . ', 2,' . $user_ID . ',this);">' . gt_simplevoteme_getimgvote( "bad" ) . '</a>';
		$linkNeutral  = '<a onclick="simplevotemeaddvote(' . $post_ID . ', 0,' . $user_ID . ',this);">' . gt_simplevoteme_getimgvote( "neutral" ) . '</a>';
	} else {
		$linkPositivo = gt_simplevoteme_getimgvote( "good" );
		$linkNegativo = gt_simplevoteme_getimgvote( "bad" );
		$linkNeutral  = gt_simplevoteme_getimgvote( "neutral" );
	}

	$title = get_option( 'gt_simplevoteme_title' );

	$votemelink = "<div class='simplevotemeWrapper $tipo' id='simplevoteme-$post_ID' >$title";
	$votemelink .= "<span class='good'>$linkPositivo <span class='result'>$votemePercentPositive</span></span>";
	$votemelink .= "<span class='neutro'>$linkNeutral <span class='result'>$votemePercentNeutral</span></span>";
	$votemelink .= "<span class='bad'>$linkNegativo <span class='result'>$votemePercentNegative</span></span>";

	$imgloading = SIMPLEVOTEMESURL . '/img/ajax_loader_red_32.gif';
	$votemelink .= "</div><script type='text/javascript'>var simplevotemeLoading='$imgloading';</script>";


	$result = $votemelink . gt_simplevoteme_draw_list_votes( $votes, $post_ID );

	return $result;
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
		if (!in_array(get_post_type(),get_option('gt_simplevoteme_custom_post_types'))) return $content;

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
		if (!in_array(get_post_type(),get_option('gt_simplevoteme_custom_post_types'))) return $content;

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


function gt_simplevoteme_aftervote() {
	$linkPositivo = gt_simplevoteme_getimgvote( "good" );
	$linkNegativo = gt_simplevoteme_getimgvote( "bad" );
	$linkNeutral  = gt_simplevoteme_getimgvote( "neutral" );
}

/** Ajax **/
function gt_simplevoteme_addvote() {
	$results = '';
	global $wpdb;
	$post_ID = $_POST['postid'];
	$user_ID = $_POST['userid'];
	$type    = $_POST['tipo'];
	$votes   = get_post_meta( $post_ID, '_simplevotemevotes', true ) != "" ? get_post_meta( $post_ID,
		'_simplevotemevotes',
		true ) : array(
		'positives' => array(),
		'negatives' => array(),
		'neutrals'  => array()
	);

	$votes = gt_simplevoteme_insertvote( $votes, $user_ID, $type );
	update_post_meta( $post_ID, '_simplevotemevotes', $votes );

	$noLinks = get_option( 'gt_simplevoteme_votes' );

	$result = gt_simplevoteme_getvotelink( $noLinks, $post_ID );


	// Return the String
	die( $result );
}

function gt_simplevoteme_insertvote( $votes, $user_ID, $type ) {

	if ( false !== ( $key = array_search( $user_ID, $votes['neutrals'] ) ) ) {
		unset( $votes['neutrals'][ $key ] );
	}
	if ( false !== ( $key = array_search( $user_ID, $votes['positives'] ) ) ) {
		unset( $votes['positives'][ $key ] );
	}
	if ( false !== ( $key = array_search( $user_ID, $votes['negatives'] ) ) ) {
		unset( $votes['negatives'][ $key ] );
	}

	switch ( $type ) {
		case 0:
			$votes['neutrals'][ $user_ID ] = $user_ID;
			break;
		case 1:
			$votes['positives'][ $user_ID ] = $user_ID;
			break;
		case 2:
			$votes['negatives'][ $user_ID ] = $user_ID;
			break;
	}


	return $votes;
}


// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_simplevoteme_addvote', 'gt_simplevoteme_addvote' );
add_action( 'wp_ajax_simplevoteme_addvote', 'gt_simplevoteme_addvote' );
            
            

  
