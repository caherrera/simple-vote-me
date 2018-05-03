<?php

function gt_simplevoteme_admin_scripts()
{
    wp_enqueue_media();
    wp_enqueue_script('wp-media-uploader', SIMPLEVOTEMESURL . '/js/wp_media_uploader.js', array('jquery'), 1.0);
}

function gt_simplevoteme_admin_styles()
{
    if ( ! wp_style_is('thickbox')) {
        wp_enqueue_style('thickbox');
    }
}

add_action('admin_print_scripts', 'gt_simplevoteme_admin_scripts');
add_action('admin_print_styles', 'gt_simplevoteme_admin_styles');

add_action('admin_init', 'gt_simplevoteme_admin_init');
add_action('admin_menu', 'gt_simplevoteme_admin_menu');

//init function
function gt_simplevoteme_admin_init()
{
    add_action('admin_init', 'gt_simplevoteme_admin_options');
}

//page admin
function gt_simplevoteme_admin_menu()
{
    if (is_admin()):
        $page = add_submenu_page(
            'options-general.php',
            __('Simple Vote Me', 'gtsimplevoteme'),
            __('Simple Vote Me', 'gtsimplevoteme'),
            'manage_options',
            __FILE__,
            'gt_simplevoteme_page_admin', 'gt_simplevoteme_page_admin');

        //add CSS only for admin page
        add_action('admin_print_styles-' . $page, 'gt_simplevoteme_admin_style');
    endif;
}

function gt_simplevoteme_admin_style()
{
    wp_enqueue_style('simplevotemestyleadmin');
}

function gt_get_tr_custom_img($vote, $prefix = '')
{
    $title      = __('Custom image for ' . ucwords($prefix ? str_ireplace('_', '',
                $prefix) . ' ' : '') . ucwords($vote));
    $customImg  = get_option("gt_simplevoteme_{$prefix}custom_img_" . $vote) ?: '';
    $tagName    = "gt_simplevoteme_{$prefix}custom_img_$vote";
    $imgTagName = "gt_simplevoteme_{$prefix}custom_thumb_$vote";

    $html   = [];
    $html[] = "<tr><th scope=\"row\">$title</th>";
    $html[] = "<td class='gt_simplevoteme_custom_img_uploader'>";
//    $html[] = "<img id=\"$imgTagName\" src=\"$customImg\" class='gt_simplevoteme_custom_thumb'/>";
    $html[] = "<input style='width: 70%' name=\"$tagName\" id=\"$tagName\" value=\"$customImg\" class=\"gt_simplevoteme_custom_img_input\"/>";
//    $html[] = "<a href=\"#\" class=\"gt_simplevoteme_custom_img_link hide\" data-thumb=\"$imgTagName\" data-input=\"$tagName\">Upload</a>";
    $html[] = "</td>";
    $html[] = "</tr>";

    return implode('', $html);

}

//page admin
function gt_simplevoteme_page_admin()
{
    if (isset($_POST['submit'])) {
        if ($_POST['gt_simplevoteme_reset']) {
            $gt_simplevoteme_reset = true;
            unset($_POST['gt_simplevoteme_reset']);
        }
        foreach ($_POST as $item => $value) {
            if (preg_match('/gt_simplevoteme_.*/', $item)) {
                update_option($item, $value);
            }
        }

        if ($gt_simplevoteme_reset) {
            gt_simplevoteme_reset(1);
        }
    }
    ?>
    <div class="wrap">
        <h2> Simple Vote me </h2>

        <form method="post" action="" id="simplevoteme">

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo __('Title'); ?></th>
                    <td>
                        <?php $title = get_option('gt_simplevoteme_title'); ?>
                        <input name="gt_simplevoteme_title" value="<?php if ($title) {
                            echo $title;
                        } ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Auto Insert in Content?'); ?></th>
                    <td>
                        <?php $auto = get_option('gt_simplevoteme_auto_insert_content'); ?>
                        <select id="auto" name="gt_simplevoteme_auto_insert_content">
                            <option value="0" <?php if ( ! $auto) {
                                echo "selected";
                            } ?>>No
                            </option>
                            <option value="1" <?php if ($auto == 1) {
                                echo "selected";
                            } ?>>Only in post
                            </option>
                            <option value="2" <?php if ($auto == 2) {
                                echo "selected";
                            } ?>>Only in pages
                            </option>
                            <option value="3" <?php if ($auto == 3) {
                                echo "selected";
                            } ?>>Post and Pages
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Select all options where you want the poll:'); ?></th>
                    <td style="-webkit-column-count: 2;-moz-column-count: 2;column-count: 2;">
                        <?php $typesActive = get_option('gt_simplevoteme_custom_post_types');
                        $types             = get_post_types(array('public' => true), 'objects');
                        foreach ($types as $type) {
                            ?>
                            <div>
                                <input type="checkbox" id="<?php echo $type->name; ?>"
                                       name="gt_simplevoteme_custom_post_types[]"
                                       value="<?php echo $type->name; ?>" <?php if (in_array($type->name,
                                    (array)$typesActive)) {
                                    echo 'checked';
                                } ?>/>
                                <label for="<?php echo $type->name; ?>"><?php echo $type->labels->menu_name; ?></label>
                                <?php //print_r($type);
                                ?>
                            </div>
                            <?php
                        }

                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Display in Home?'); ?></th>
                    <td>
                        <?php $home = get_option('gt_simplevoteme_auto_insert_home'); ?>
                        <label for="home_yes"><?php echo __('Yes'); ?></label>
                        <input type="radio" id="home_yes" name="gt_simplevoteme_auto_insert_home"
                               value="1" <?php if ($home) {
                            echo "checked";
                        } ?> />

                        <label for="home_no"><?php echo __('No'); ?></label>
                        <input type="radio" id="home_no" name="gt_simplevoteme_auto_insert_home"
                               value="0" <?php if ( ! $home) {
                            echo "checked";
                        } ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Position of the poll (on content)'); ?></br>
                        <small><?php echo __('(Only if you have selected the auto insert).'); ?></small>
                    </th>
                    <td>
                        <?php $position = get_option('gt_simplevoteme_position'); ?>
                        <select id="position" name="gt_simplevoteme_position">
                            <option value="0" <?php if ( ! $position) {
                                echo "selected";
                            } ?>>After the Content
                            </option>
                            <option value="1" <?php if ($position == 1) {
                                echo "selected";
                            } ?>>Before the content
                            </option>
                            <option value="2" <?php if ($position == 2) {
                                echo "selected";
                            } ?>>Both, before and after
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Only for registered users'); ?></th>
                    <td>
                        <?php $login = get_option('gt_simplevoteme_only_login'); ?>
                        <label for="only_login_yes"><?php echo __('Yes'); ?></label>
                        <input type="radio" id="only_login_yes" name="gt_simplevoteme_only_login"
                               value="1" <?php if ($login) {
                            echo "checked";
                        } ?> />
                        <label for="only_login_no"> <?php echo __('No'); ?></label>
                        <input type="radio" id="only_login_yes" name="gt_simplevoteme_only_login"
                               value="0" <?php if ( ! $login) {
                            echo "checked";
                        } ?> />
                    </td>

                </tr>
                <tr>
                    <th scope="row"><?php echo __('How many times can each user vote?'); ?></th>
                    <td>
                        <?php $votes = get_option('gt_simplevoteme_votes'); ?>
                        <select id="votes" name="gt_simplevoteme_votes">
                            <option value="0" <?php if ( ! $votes) {
                                echo "selected";
                            } ?>><?php echo __('Infinite'); ?></option>
                            <option value="1" <?php if ($votes) {
                                echo "selected";
                            } ?>><?php echo __('Once per user'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Show Results?'); ?></th>
                    <td>
                        <?php $results = get_option('gt_simplevoteme_results'); ?>
                        <select id="results" name="gt_simplevoteme_results">
                            <option value="1" <?php if ($results) {
                                echo "selected";
                            } ?>><?php echo __('Always'); ?></option>
                            <option value="2" <?php if ($results == 2) {
                                echo "selected";
                            } ?>><?php echo __('After vote'); ?></option>
                            <option value="0" <?php if ( ! $results) {
                                echo "selected";
                            } ?>><?php echo __('Never'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('What to show?'); ?></th>
                    <td>
                        <?php $results_type = get_option('gt_simplevoteme_results_type'); ?>
                        <select id="results" name="gt_simplevoteme_results_type">
                            <option value="0" <?php if ( ! $results_type) {
                                echo "selected";
                            } ?>><?php echo __('Total votes and percentages'); ?></option>
                            <option value="1" <?php if ($results_type) {
                                echo "selected";
                            } ?>><?php echo __('Only percentages'); ?></option>
                            <option value="2" <?php if ($results_type == 2) {
                                echo "selected";
                            } ?>><?php echo __('Only total votes'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Default CSS'); ?></th>
                    <td>
                        <?php $default_css = get_option('gt_simplevoteme_default_css'); ?>
                        <label for="default_css_yes"><?php echo __('Activate'); ?></label>
                        <input type="radio" id="default_css_yes" name="gt_simplevoteme_default_css"
                               value="0"<?php if ( ! $default_css) {
                            echo "checked";
                        } ?> />
                        <label for="default_css_nope"><?php echo __('Deactivate'); ?></label>
                        <input type="radio" id="default_css_nope" name="gt_simplevoteme_default_css"
                               value="1"<?php if ($default_css) {
                            echo "checked";
                        } ?> />
                    </td>
                </tr>

                <?php echo gt_get_tr_custom_img('good'); ?>
                <?php echo gt_get_tr_custom_img('neutral'); ?>
                <?php echo gt_get_tr_custom_img('bad'); ?>
                <?php echo gt_get_tr_custom_img('good', 'compliment_'); ?>
                <?php echo gt_get_tr_custom_img('neutral', 'compliment_'); ?>
                <?php echo gt_get_tr_custom_img('bad', 'compliment_'); ?>
                <tr>
                    <th scope="row"><?php echo __('Extra Class'); ?></th>
                    <td>
                        <input id="extra_class" name="gt_simplevoteme_extra_class"
                               value="<?php get_option('gt_simplevoteme_extra_class'); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Extra Class for Compliments'); ?></th>
                    <td>
                        <input id="extra_class_compliment" name="gt_simplevoteme_extra_class_compliment"
                               value="<?php get_option('gt_simplevoteme_extra_class_compliment'); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Reset all votes?'); ?></th>
                    <td>
                        <select id="reset" name="gt_simplevoteme_reset">
                            <option value="0"><?php echo __('No'); ?></option>
                            <option value="1"><?php echo __('Yes'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <script>
            jQuery(document).ready(function (e) {
                jQuery.wpMediaUploader({

                    target: '.gt_simplevoteme_custom_img_uploader', // The class wrapping the textbox
                    uploaderTitle: 'Select or upload image', // The title of the media upload popup
                    uploaderButton: 'Set image', // the text of the button in the media upload popup
                    multiple: false, // Allow the user to select multiple images
                    buttonText: 'Upload image', // The text of the upload button
                    buttonClass: '.gt_simplevoteme_custom_img_link', // the class of the upload button
                    previewSize: '150px', // The preview image size
                    modal: false, // is the upload button within a bootstrap modal ?
                    buttonStyle: { // style the button
                        color: '#3bafda',
                        background: '#fff',
                        fontSize: '16px',
                        padding: '10px 15px',
                    },

                });
            });
        </script>
    </div>
    <?php
}

//page admin options
function gt_simplevoteme_admin_options()
{
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_title');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_auto_insert_content');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_auto_insert_home');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_position');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_only_login');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_default_css');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_css');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_results');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_results_type');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_img');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_img_good');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_border_good');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_background_good');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_img_neutral');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_border_neutral');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_background_neutral');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_img_bad');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_border_bad');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_background_bad');

    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_votes');
    register_setting('gt_simplevoteme_options', 'gt_simplevoteme_custom_post_types');

}

function gt_simplevoteme_reset($reset = false)
{
    if ($reset) {

        $the_query = new WP_Query('meta_key=_simplevotemevotes&amp;orderby=meta_value_num&amp;order=DESC&amp;');
        // The Loop
        while ($the_query->have_posts()) : $the_query->the_post();
            update_post_meta(get_the_ID(), '_simplevotemevotes', "");
        endwhile;
        wp_reset_postdata();
    }
}


/** Show the info in admin panel **/
function gt_simplevoteme_custom_columns()
{
    $types = get_option('gt_simplevoteme_custom_post_types');
    if (is_array($types)) {
        foreach ($types as $type) {
            //add cols
            add_filter('manage_edit-' . $type . '_columns', 'gt_simplevoteme_extra_columns');
            //add content
            add_action('manage_' . $type . '_posts_custom_column', 'gt_simplevoteme_content_column_row', 10, 2);
        }
    }
}

gt_simplevoteme_custom_columns();

function gt_simplevoteme_extra_columns($columns)
{
    $columns['simplevotemetotal']    = __('Votes');
    $columns['simplevotemenegative'] = __(':(');
    $columns['simplevotemeneutral']  = __(':|');
    $columns['simplevotemepositive'] = __(':)');

    return $columns;
}


function gt_simplevoteme_content_column_row($column)
{
    global $post;
    $post_id = $post->ID;
    $votes   = get_post_meta($post_id, '_simplevotemevotes', true) != '' ? get_post_meta($post_id, '_simplevotemevotes',
        true) : array(
        'positives' => array(),
        'negatives' => array(),
        'neutrals'  => array(),
    );
    $users   = array('positives' => array(), 'negatives' => array(), 'neutrals' => array());
    foreach ($votes as $key => $voteType) {
        foreach ($voteType as $vote) {
            if ($vote != 0) {
                $user          = get_userdata($vote);
                $users[$key][] = '<a href="' . get_author_posts_url($vote,
                        $user->display_name) . '" target="_blank">' . $user->display_name . '</a>';

            } else {
                $users[$key][] = __('Anonymous');
            }
        }
    }

    switch ($column):
        case('simplevotemepositive'):
            echo count($votes['positives']);
            foreach ($users["positives"] as $user) {
                echo "</br>" . $user;
            }
            break;

        case('simplevotemenegative'):
            echo count($votes['negatives']);
            foreach ($users["negatives"] as $user) {
                echo "</br>" . $user;
            }
            break;
        case('simplevotemeneutral'):
            echo count($votes['neutrals']);
            foreach ($users["neutrals"] as $user) {
                echo "</br>" . $user;
            }
            break;
        case('simplevotemetotal'):
            echo sizeof($votes, 1) - 3; //rest 3 because arrays for separate votes counts.
            break;

        default:
            break;
    endswitch;

}


//Meta box for post
function gt_simplevoteme_metabox_votes($post)
{
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    ?>
    <script>jQuery(document).ready(function() {
        jQuery('#gt_simplevoteme_votes').mouseout(function(){
            jQuery('.gt_simplevoteme_votes_list.active').slideUp().removeClass('active');
        });
        });</script>
    <ul class="categorychecklist" style="text-transform: capitalize;">
        <?php
        $votes = get_post_meta($post->ID, '_simplevotemevotes', true) != '' ? get_post_meta($post->ID,
            '_simplevotemevotes', true) : array('positives' => array(), 'negatives' => array(), 'neutrals' => array());

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

    <style>
        #gt_simplevoteme_votes > .inside ul {
            overflow: auto;
        }

        #gt_simplevoteme_votes ul.categorychecklist > li {
            width: 33.333%;
            padding: 0;
            float: left;
            text-align: center;
        }

        #gt_simplevoteme_votes ul.categorychecklist > li >ul.children {
            display: none;
        }
        #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list{
            display: none;
        }
        #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list > li {
            clear: both;
            float: left;
        }
        #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list > li > a {
            display: block;
            float: left;
            width: 100%;
        }
        #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list > li > a > * {
            float:left;
            height: 48px;
            line-height: 48px;
        }
        #gt_simplevoteme_votes ul.gt_simplevoteme_votes_list > li img.avatar {
            border-radius: 100%;
            margin-right: 4px;

        }

        #gt_simplevoteme_votes span {
            font-size: 1.1em;
            font-weight: bold;
        }

        #gt_simplevoteme_votes ul.categorychecklist > li:first-child {
            width: 100%;
            margin-bottom: .5em;
        }
    </style>
    <?php

}

function gt_simplevoteme_draw_votes($key,$usersCat){
	echo "<ul id='gt_simplevoteme_votes_$key' class='children gt_simplevoteme_votes_list'>";
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
	$mouseOver="if(!$('#gt_simplevoteme_votes_$key').hasClass('active'))".'{'."$('.gt_simplevoteme_votes_list.active').removeClass('active').hide();$('#gt_simplevoteme_votes_$key').slideDown().addClass('active');}";


	echo "<li><div onmouseover=\"$mouseOver\">$imgvote $countUsersCat</div>";

	echo "</li>";
}

function gt_simplevoteme_add_meta_box_votes()
{
    $types = get_option('gt_simplevoteme_custom_post_types');
    if (is_array($types)) {
        foreach ($types as $type) {
            add_meta_box("gt_simplevoteme_votes", "Votes", "gt_simplevoteme_metabox_votes", $type, "side", "high",
                null);
        }
    }
}

add_action("add_meta_boxes", "gt_simplevoteme_add_meta_box_votes");

