<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 4/5/18
 * Time: 11:20 AM
 */

function gt_simplevoteme_get_userdata( $vote ) {
	if ( $vote != 0 ) {
		$user = get_userdata( $vote );
		if ( is_admin() && ! in_array( current_action(),
				[ 'wp_ajax_simplevoteme_addvote', 'wp_ajax_simplevoteme_compliments_addvote' ] ) ) {
			$user_url = get_edit_user_link( $vote ); //. '" target="_blank">' . $user->display_name . '</a>';
			$map      = '<a id="SimpleVoteMeUser%s" href="%s" target="_blank" data-id="%s">%s</a>';
		} else {
			if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {

				$user_url = bp_core_get_userlink( $vote, false, true );
			} else {
				$user_url = get_author_posts_url( $vote, $user->user_nicename );
			}
			$map = '<div id="SimpleVoteMeUser%s" data-href="%s" data-id="%s">%s</div>';
		}
		$display_name = get_avatar( $user->ID, 48 ) . '<b>' . $user->display_name . '</b>';
		$user_name    = sprintf( $map, $vote, $user_url, $vote, $display_name );

	} else {
		$user_name = __( 'Anonymous' );
	}

	return $user_name;
}

function gt_simplevoteme_draw_list_votes( $votes, $id ) {

	ob_start();

	?>
    <ul data-simplevotemeid="<?php echo $id ?>" class="gt_simplevoteme categorychecklist"
        style="text-transform: capitalize;display: none">


		<?php
		foreach ( $votes as $key => $voteKey ) {
			gt_simplevoteme_draw_resume( $key, $voteKey );
		}
		?>
    </ul>
	<?php
	foreach ( $votes as $key => $voteKey ) {
		gt_simplevoteme_draw_votes( $key, $voteKey );
	}
	$html = ob_get_clean();

	return $html;
}

function gt_simplevoteme_draw_votes( $key, $usersCat ) {
	echo "<ul id='gt_simplevoteme_votes_$key' class='children gt_simplevoteme_votes_list' style='display: none;'>";
//	foreach ( $usersCat as $usr ) {
//		echo "<li>$usr</li>";
//	}

	echo "</ul>";
}

function gt_simplevoteme_draw_resume( $key, $usersCat ) {
	$countUsersCat = count( $usersCat );
	$imgvote       = gt_simplevoteme_getimgvote( $key );
	echo "<li data-key=\"$key\" class=\"gt_simplevoteme_resume_div\" >$imgvote $countUsersCat</li>";


}