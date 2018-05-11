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
            .simplevotemeWrapper #gt_simplevoteme_votes {
                width: 251px; clear: both; margin: auto
            }
            #gt_simplevoteme_votes > .inside ul {
                overflow: hidden;
            }

            #gt_simplevoteme_votes ul.categorychecklist > li {
                width: 33%;
                padding: 0;
                float: left;
                text-align: center;
            }

            #gt_simplevoteme_votes ul.categorychecklist > li >ul.children {
                display: none;
                overflow: hidden;
            }
            #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list{
                display: none;
                overflow: hidden;

            }
            #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list > li {
                clear: both;
                float: left;
                margin: 10px 0 0 10px;
                list-style: none;
            }
            #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list > li > a {
                display: block;
                float: left;
                width: 100%;
                color:inherit !important;
            }
            #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list > li > a > * {
                float:left;
                height: 48px;
                line-height: 48px;
            }
            #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list.active{
                padding: 0px;
                width: 300px;
                margin: auto;
            }
            #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list.active li{
                padding: 0px;
                list-style: none;

            }
            #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list > li img.avatar {
                border-radius: 100%;
                margin-right: 4px;
                margin-top: 0;

            }

            #gt_simplevoteme_votes span {
                font-size: 1.1em;
                font-weight: bold;
            }

            #gt_simplevoteme_votes ul.categorychecklist > li:first-child {
                width: 100%;
                margin-bottom: .5em;
                padding-top: 0;
                /*border: 0;*/
            }
            #gt_simplevoteme_votes ul.categorychecklist > li {
                padding: 15px 0px;
                /*border-bottom:1px solid #cccccc;*/

            }
            #gt_simplevoteme_votes ul.categorychecklist > li.active ,
            .simplevotemeWrapper.h > span.active {

                background:#ddd;

            }
        </style>

	<ul data-simplevotemeid="<?php echo $id?>" class="gt_simplevoteme categorychecklist" style="text-transform: capitalize;display: none">
		<?php


		$users = array('positives' => array(), 'negatives' => array(), 'neutrals' => array());
		foreach ($votes as $key => $voteType) {
			foreach ($voteType as $vote) {
				if ($vote != 0) {
					$user          = get_userdata($vote);
					if (is_plugin_active('buddypress/bp-loader.php')) {
						$users[ $key ][] = '<a href="' .get_home_url().'/members/'.  ( $user->user_nicename)
						                   . '" target="_blank">' . get_avatar($user->ID,48). '<b>'.$user->display_name . '</b></a>';
					}else {
						$users[ $key ][] = '<a href="' . get_author_posts_url( $vote,
								$user->display_name ) . '" target="_blank">' . $user->display_name . '</a>';
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