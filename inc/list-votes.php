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
		if ( is_admin() ) {
			$user_url= get_edit_user_link( $vote ) ; //. '" target="_blank">' . $user->display_name . '</a>';
            $map='<a href="%s" target="_blank">%s</a>';
//            $display_name=$user->display_name;
			$display_name = get_avatar( $user->ID, 48 ) . '<b>' . $user->display_name;
		} else {
			if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
//			$user_url = get_home_url() . '/members/' . ( $user->user_nicename );
			$user_url = bp_core_get_userlink($vote,false,true);
//			$user_name = '<div data-href="' . get_home_url() . '/members/' . ( $user->user_nicename )
//			             . '">' . get_avatar( $user->ID, 48 ) . '<b>' . $user->display_name . '</b></div>';
			} else {
			$user_url= get_author_posts_url( $vote,$user->user_nicename ) ;
//          $user_name = '<div data-href="' . get_author_posts_url( $vote,
//					$user->user_nicename ) . '" target="_blank">' . $user->display_name . '</div>';
			}
			$display_name = get_avatar( $user->ID, 48 ) . '<b>' . $user->display_name;
			$map='<div data-href="%s" target="_blank">%s</div>';
		}
		$user_name=sprintf($map,$user_url,$display_name);

	} else {
		$user_name = __( 'Anonymous' );
	}

	return $user_name;
}

function gt_simplevoteme_draw_list_votes($votes,$id) {

    ob_start();

	?>
    <ul data-simplevotemeid="<?php echo $id?>" class="gt_simplevoteme categorychecklist" style="text-transform: capitalize;display: none">

		<li><span>Total:</span><?php echo sizeof($votes, 1) - 3; ?></li>
		<?php
        foreach($votes as $key=>$voteKey) {
	        gt_simplevoteme_draw_resume($key,$voteKey);
        }
		?>
	</ul>
	<?php
	foreach($votes as $key=>$voteKey) {
		gt_simplevoteme_draw_votes($key,$voteKey);
	}
	$html=ob_get_clean();
	return $html;
}

function gt_simplevoteme_draw_votes($key,$usersCat){
	echo "<ul id='gt_simplevoteme_votes_$key' class='children gt_simplevoteme_votes_list' style='display: none;'>";
	foreach ($usersCat as $usr) {
		echo "<li>$usr</li>";
	}

	echo "</ul>";
}
function gt_simplevoteme_draw_resume($key,$usersCat){
	$countUsersCat=count($usersCat);
	switch($key) {
		case 'positives':$imgvote=gt_simplevoteme_getimgvote('good');break;
		case 'neutrals':$imgvote=gt_simplevoteme_getimgvote('neutral');break;
		case 'negatives':$imgvote=gt_simplevoteme_getimgvote('bad');break;
	}



	echo "<li data-key=\"$key\" class=\"gt_simplevoteme_resume_div\" >$imgvote $countUsersCat</li>";


}