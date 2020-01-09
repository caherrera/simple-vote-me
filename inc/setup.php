<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 30/8/18
 * Time: 4:20 PM
 */

function gt_simplevoteme_init() {
	register_widget( 'GTSimpleVoteMeTopVotedWidget' );
	register_widget( 'GTSimpleVoteMeWidget' );

}


function gt_simplevoteme_checkversion() {
	global $gt_simplevoteme_version;

	$version = get_option( 'gt_simplevoteme_version' );

	if ( $version === false ) {
		//install plugin


		//check if there are old system of votes


	} else if ( $version != $gt_simplevoteme_version ) {
		//update tables,vars etc.
	}
	gt_simplevoteme_check_old_votes();
//	gt_simplevoteme_check_bp_compliments_activate();

	update_option( 'gt_simplevoteme_version', $gt_simplevoteme_version );

}

function gt_simplevoteme_registermeta() {
	global $wpdb;
	$meta_table        = BP_COMPLIMENT_META_TYPE . 'meta';
	$wpdb->$meta_table = $wpdb->prefix . $meta_table;
}

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
