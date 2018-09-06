<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 30/8/18
 * Time: 4:25 PM
 */

/**
 * @param VoteOption|string $type
 *
 * @return string
 */
function gt_simplevoteme_getimgvote( $type ) {

	$settings = $type instanceof VoteOption ? $type : gt_simplevoteme_get_vote_options( $type );

	if ( ! $customImg = $settings->custom_img ) {
		$customImg = sprintf( "%s/img/%s.png", SIMPLEVOTEMESURL, $settings->name );
	}

	return sprintf( "<img class=\"gt_simplevoteme_img gt_simplevoteme_custom_img gt_simplevoteme_custom_img_%s\" src=\"%s\"/>",
		$settings->name, $customImg );
}

/**
 * @param null $vote
 *
 * @return VoteOption[]|VoteOption|null
 */
function gt_simplevoteme_get_vote_options( $vote = null ) {
	try {
		$option = get_option( 'gt_simplevoteme_options_votes', [
			'negatives' => [
				'id'         => 'negatives',
				'label'      => 'Bad',
				'name'       => 'bad',
				'custom_img' => '/wp-content/uploads/simple-vote-me/bad.png'
			],
			'neutrals'  => [
				'id'         => 'neutrals',
				'label'      => 'Neutral',
				'name'       => 'neutral',
				'custom_img' => '/wp-content/uploads/simple-vote-me/neutral.png'
			],
			'positives' => [
				'id'         => 'positives',
				'label'      => 'Good',
				'name'       => 'good',
				'custom_img' => '/wp-content/uploads/simple-vote-me/good.png'
			],
		] );
	}catch(Exception $e){
		wp_die($e->getMessage());
	}
	$option = array_map( function ( $v ) {
		return new VoteOption( $v );
	}, $option );
	if ( $vote ) {
		return $option[ $vote ] ?: null;
	} else {
		return $option;
	}
}

function gt_simplevoteme_filter_userdata( $votes ) {
	$votes = array_map( function ( $voteType ) {
		return array_map( 'gt_simplevoteme_get_userdata', $voteType );
	}, $votes );

	return $votes;
}

function gt_simplevoteme_insertvote( $votes, $user_ID, $type ) {

	foreach ( $votes as $option => $vote ) {
		if ( array_key_exists( $user_ID, $vote ) ) {
			unset( $votes[ $option ][ $user_ID ] );
		}
	}

	$votes[ $type ][ $user_ID ] = $user_ID;

	return $votes;
}


function gt_simplevoteme_send_json_success( $votes ) {
	$noLinks = get_option( 'gt_simplevoteme_votes' );

	$votes = gt_simplevoteme_filter_userdata( $votes );

	wp_send_json_success( [ 'votes' => $votes, 'noLinks' => (bool) $noLinks ] );
}

