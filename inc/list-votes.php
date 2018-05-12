<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 4/5/18
 * Time: 11:20 AM
 */

function gt_simplevoteme_draw_list_votes($votes,$id) {

    ob_start();

	?>

        <style>

        </style>

	<ul data-simplevotemeid="<?php echo $id?>" class="gt_simplevoteme categorychecklist" style="text-transform: capitalize;display: none">
		<?php


		$users = array('positives' => array(), 'negatives' => array(), 'neutrals' => array());
		foreach ($votes as $key => $voteType) {
			foreach ($voteType as $vote) {
				if ($vote != 0) {
					$user          = get_userdata($vote);
					if (is_plugin_active('buddypress/bp-loader.php')) {
						$users[ $key ][] = '<div data-href="' .get_home_url().'/members/'.  ( $user->user_nicename)
						                   . '">' . get_avatar($user->ID,48). '<b>'.$user->display_name . '</b></div>';
					}else {
						$users[ $key ][] = '<div data-href="' . get_author_posts_url( $vote,
								$user->display_name ) . '" target="_blank">' . $user->display_name . '</div>';
					}

				} else {
					$users[$key][] = __('Anonymous');
				}
			}
		}
		?>
		<li><span>Total:</span><?php echo sizeof($votes, 1) - 3; ?></li>
		<?php
		gt_simplevoteme_draw_resume($key='positives',$users[$key]);
		gt_simplevoteme_draw_resume($key='neutrals',$users[$key]);
		gt_simplevoteme_draw_resume($key='negatives',$users[$key]);

		?>
	</ul>
	<?php
	gt_simplevoteme_draw_votes($key='positives',$users[$key]);
	gt_simplevoteme_draw_votes($key='neutrals',$users[$key]);
	gt_simplevoteme_draw_votes($key='negatives',$users[$key]);

	?>



	<?php
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