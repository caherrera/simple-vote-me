<?php

function gt_simplevoteme_admin_scripts() {
	wp_enqueue_media();
	wp_enqueue_script( 'wp-media-uploader', SIMPLEVOTEMESURL . '/js/wp_media_uploader.js', array( 'jquery' ), 1.0 );
	wp_enqueue_script( 'gtsimplevoteme', SIMPLEVOTEMESURL . '/js/simple-vote-me.js',
		array( 'jquery', 'wp-media-uploader' ) );
	wp_register_style( 'simplevotemestyle', SIMPLEVOTEMESURL . '/css/simplevoteme.css' );
	wp_register_style( 'simplevotemestyleadmin', SIMPLEVOTEMESURL . '/css/simplevotemeadmin.css' );
	wp_enqueue_style( 'simplevotemestyle' );
	wp_enqueue_style( 'simplevotemestyleadmin' );

}

function gt_simplevoteme_admin_styles() {
	if ( ! wp_style_is( 'thickbox' ) ) {
		wp_enqueue_style( 'thickbox' );
	}
}

add_action( 'admin_print_scripts', 'gt_simplevoteme_admin_scripts' );
add_action( 'admin_print_styles', 'gt_simplevoteme_admin_styles' );

add_action( 'admin_init', 'gt_simplevoteme_admin_init' );
add_action( 'admin_menu', 'gt_simplevoteme_admin_menu' );

//init function
function gt_simplevoteme_admin_init() {
	add_action( 'admin_init', 'gt_simplevoteme_admin_options' );
}

//page admin
function gt_simplevoteme_admin_menu() {
	if ( is_admin() ):
		$page = add_submenu_page(
			'options-general.php',
			__( 'Simple Vote Me', 'gtsimplevoteme' ),
			__( 'Simple Vote Me', 'gtsimplevoteme' ),
			'manage_options',
			__FILE__,
			'gt_simplevoteme_page_admin', 'gt_simplevoteme_page_admin' );

		//add CSS only for admin page
		add_action( 'admin_print_styles-' . $page, 'gt_simplevoteme_admin_style' );
	endif;
}

function gt_simplevoteme_admin_style() {
	wp_enqueue_style( 'simplevotemestyleadmin' );
}

function gt_simplevoteme_get_tr_custom_img( VoteOption $vote_option ) {
	$title      = $id = $vote_option->id;
	$customImg  = $vote_option->custom_img;
	$name       = $vote_option->name;
	$tagName    = "custom_img_$name";
	$imgTagName = "gt_simplevoteme_custom_thumb_$name";

	$inputName = sprintf( '<input name="gt_simplevoteme_vote[%s][name]" id="GtSimplevotemeVote%sName" value="%s">',
		$id, ucwords( $id ), $vote_option->name );

	$inputRemove = sprintf( '<input type="hidden" name="gt_simplevoteme_vote[%s][to_remove]" class="to_remove" id="GtSimplevotemeVote%sToRemove" value="%s">',
		$id, ucwords( $id ), '' );

	$inputLabel = sprintf( '<input name="gt_simplevoteme_vote[%s][label]" id="GtSimplevotemeVote%sLabel" value="%s">',
		$id, ucwords( $id ), $vote_option->label );

	$options = sprintf( '<a href="#remove" data-vote="GtSimplevotemeVote%s" id="GtSimplevotemeVote%s%s" class="gt_simplevoteme_vote_remove button button-link-delete">%s</a>',
		ucwords( $id ), ucwords( $id ), 'Remove',
		'<span class="dashicons dashicons-trash"></span>' );

	$options .= sprintf( '<a href="#undo-remove" data-vote="GtSimplevotemeVote%s" id="GtSimplevotemeVote%s%s" class="gt_simplevoteme_vote_undo_remove button button-link">%s</a>',
		ucwords( $id ), ucwords( $id ), 'Remove',
		'<span class="dashicons dashicons-image-rotate"></span>' );


	$html   = [];
	$html[] = sprintf( '<tr id="GtSimplevotemeVote%s">', ucwords( $id ) );
	$html[] = "<td>$title $inputRemove</td>";
	$html[] = "<td>$inputName</td>";
	$html[] = "<td>$inputLabel</td>";

	$html[] = "<td class='gt_simplevoteme_custom_img_uploader'>";
	$html[] = "<img id=\"$imgTagName\" style='width: 48px' src=\"$customImg\" class='gt_simplevoteme_custom_thumb'/>";
	$html[] = "<input style='width: 70%' name=\"$tagName\" id=\"$tagName\" value=\"$customImg\" class=\"gt_simplevoteme_custom_img_input\"/>";
//    $html[] = "<a href=\"#\" class=\"gt_simplevoteme_custom_img_link hide\" data-thumb=\"$imgTagName\" data-input=\"$tagName\">Upload</a>";
	$html[] = "</td>";


	$html[] = "<td>$options</td>";
	$html[] = "</tr>";

	return implode( '', $html );

}

//page admin
function gt_simplevoteme_page_admin() {
	if ( isset( $_POST['submit'] ) ) {
		if ( $_POST['gt_simplevoteme_reset'] ) {
			$gt_simplevoteme_reset = true;
			unset( $_POST['gt_simplevoteme_reset'] );
		}
		foreach ( $_POST as $item => $value ) {
			if ( preg_match( '/gt_simplevoteme_.*/', $item ) ) {
				update_option( $item, $value );
			}
		}

		if ( $gt_simplevoteme_reset ) {
			gt_simplevoteme_reset( 1 );
		}
	}
	?>
    <div class="wrap">
        <h2> Simple Vote me </h2>

        <form method="post" action="" id="simplevoteme">

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo __( 'Title' ); ?></th>
                    <td>
						<?php $title = get_option( 'gt_simplevoteme_title' ); ?>
                        <input name="gt_simplevoteme_title" value="<?php if ( $title ) {
							echo $title;
						} ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'Auto Insert in Content?' ); ?></th>
                    <td>
						<?php $auto = get_option( 'gt_simplevoteme_auto_insert_content' ); ?>
                        <select id="auto" name="gt_simplevoteme_auto_insert_content">
                            <option value="0" <?php if ( ! $auto ) {
								echo "selected";
							} ?>>No
                            </option>
                            <option value="1" <?php if ( $auto == 1 ) {
								echo "selected";
							} ?>>Only in post
                            </option>
                            <option value="2" <?php if ( $auto == 2 ) {
								echo "selected";
							} ?>>Only in pages
                            </option>
                            <option value="3" <?php if ( $auto == 3 ) {
								echo "selected";
							} ?>>Post and Pages
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'Select all options where you want the poll:' ); ?></th>
                    <td style="-webkit-column-count: 2;-moz-column-count: 2;column-count: 2;">
						<?php $typesActive = get_option( 'gt_simplevoteme_custom_post_types' );
						$types             = get_post_types( array( 'public' => true ), 'objects' );
						foreach ( $types as $type ) {
							?>
                            <div>
                                <input type="checkbox" id="<?php echo $type->name; ?>"
                                       name="gt_simplevoteme_custom_post_types[]"
                                       value="<?php echo $type->name; ?>" <?php if ( in_array( $type->name,
									(array) $typesActive ) ) {
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
                    <th scope="row"><?php echo __( 'Display in Home?' ); ?></th>
                    <td>
						<?php $home = get_option( 'gt_simplevoteme_auto_insert_home' ); ?>
                        <label for="home_yes"><?php echo __( 'Yes' ); ?></label>
                        <input type="radio" id="home_yes" name="gt_simplevoteme_auto_insert_home"
                               value="1" <?php if ( $home ) {
							echo "checked";
						} ?> />

                        <label for="home_no"><?php echo __( 'No' ); ?></label>
                        <input type="radio" id="home_no" name="gt_simplevoteme_auto_insert_home"
                               value="0" <?php if ( ! $home ) {
							echo "checked";
						} ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'Position of the poll (on content)' ); ?></br>
                        <small><?php echo __( '(Only if you have selected the auto insert).' ); ?></small>
                    </th>
                    <td>
						<?php $position = get_option( 'gt_simplevoteme_position' ); ?>
                        <select id="position" name="gt_simplevoteme_position">
                            <option value="0" <?php if ( ! $position ) {
								echo "selected";
							} ?>>After the Content
                            </option>
                            <option value="1" <?php if ( $position == 1 ) {
								echo "selected";
							} ?>>Before the content
                            </option>
                            <option value="2" <?php if ( $position == 2 ) {
								echo "selected";
							} ?>>Both, before and after
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'Only for registered users' ); ?></th>
                    <td>
						<?php $login = get_option( 'gt_simplevoteme_only_login' ); ?>
                        <label for="only_login_yes"><?php echo __( 'Yes' ); ?></label>
                        <input type="radio" id="only_login_yes" name="gt_simplevoteme_only_login"
                               value="1" <?php if ( $login ) {
							echo "checked";
						} ?> />
                        <label for="only_login_no"> <?php echo __( 'No' ); ?></label>
                        <input type="radio" id="only_login_yes" name="gt_simplevoteme_only_login"
                               value="0" <?php if ( ! $login ) {
							echo "checked";
						} ?> />
                    </td>

                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'How many times can each user vote?' ); ?></th>
                    <td>
						<?php $votes = get_option( 'gt_simplevoteme_votes' ); ?>
                        <select id="votes" name="gt_simplevoteme_votes">
                            <option value="0" <?php if ( ! $votes ) {
								echo "selected";
							} ?>><?php echo __( 'Infinite' ); ?></option>
                            <option value="1" <?php if ( $votes ) {
								echo "selected";
							} ?>><?php echo __( 'Once per user' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'Show Results?' ); ?></th>
                    <td>
						<?php $results = get_option( 'gt_simplevoteme_results' ); ?>
                        <select id="results" name="gt_simplevoteme_results">
                            <option value="1" <?php if ( $results ) {
								echo "selected";
							} ?>><?php echo __( 'Always' ); ?></option>
                            <option value="2" <?php if ( $results == 2 ) {
								echo "selected";
							} ?>><?php echo __( 'After vote' ); ?></option>
                            <option value="0" <?php if ( ! $results ) {
								echo "selected";
							} ?>><?php echo __( 'Never' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'What to show?' ); ?></th>
                    <td>
						<?php $results_type = get_option( 'gt_simplevoteme_results_type' ); ?>
                        <select id="results" name="gt_simplevoteme_results_type">
                            <option value="0" <?php if ( ! $results_type ) {
								echo "selected";
							} ?>><?php echo __( 'Total votes and percentages' ); ?></option>
                            <option value="1" <?php if ( $results_type ) {
								echo "selected";
							} ?>><?php echo __( 'Only percentages' ); ?></option>
                            <option value="2" <?php if ( $results_type == 2 ) {
								echo "selected";
							} ?>><?php echo __( 'Only total votes' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'Default CSS' ); ?></th>
                    <td>
						<?php $default_css = get_option( 'gt_simplevoteme_default_css' ); ?>
                        <label for="default_css_yes"><?php echo __( 'Activate' ); ?></label>
                        <input type="radio" id="default_css_yes" name="gt_simplevoteme_default_css"
                               value="0"<?php if ( ! $default_css ) {
							echo "checked";
						} ?> />
                        <label for="default_css_nope"><?php echo __( 'Deactivate' ); ?></label>
                        <input type="radio" id="default_css_nope" name="gt_simplevoteme_default_css"
                               value="1"<?php if ( $default_css ) {
							echo "checked";
						} ?> />
                    </td>
                </tr>


                <tr>
                    <th scope="row"><?php echo __( 'Extra Class' ); ?></th>
                    <td>
                        <input id="extra_class" name="gt_simplevoteme_extra_class"
                               value="<?php get_option( 'gt_simplevoteme_extra_class' ); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'Extra Class for Compliments' ); ?></th>
                    <td>
                        <input id="extra_class_compliment" name="gt_simplevoteme_extra_class_compliment"
                               value="<?php get_option( 'gt_simplevoteme_extra_class_compliment' ); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __( 'Reset all votes?' ); ?></th>
                    <td>
                        <select id="reset" name="gt_simplevoteme_reset">
                            <option value="0"><?php echo __( 'No' ); ?></option>
                            <option value="1"><?php echo __( 'Yes' ); ?></option>
                        </select>
                    </td>
                </tr>


            </table>
            <fieldset id="GtSimpleVoteMeStructure">
                <legend><h2><?php echo __( 'Votes' ); ?></h2> <a href="#add-new" id="GtSimplevotemeAddNew"
                                                                 class="gt_simplevoteme_add_new button button-primary"><?php echo __( 'Add New' ); ?></a>
                </legend>


                <table width="100%" class="gt_simplevoteme_table">
                    <thead>
                    <tr>
                        <td>Key</td>
                        <td>Name</td>
                        <td>Label</td>
                        <td style="width: 100%">Image</td>
                        <td>Actions</td>
                    </tr>
                    </thead>
                    <tbody>
					<?php

					foreach ( gt_simplevoteme_get_vote_options() as $vote_option ) {
						echo gt_simplevoteme_get_tr_custom_img( $vote_option );
					}
					?>
                    </tbody>
                </table>
            </fieldset>
			<?php submit_button(); ?>
        </form>
		<?php wp_add_inline_script( 'gtsimplevoteme',
			'jQuery(document).ready(function (e) {jQuery(\'#GtSimpleVoteMeStructure\').GtSimpleVotemeAdmin();});' ); ?>

    </div>
	<?php
}

//page admin options
function gt_simplevoteme_admin_options() {
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_title' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_auto_insert_content' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_auto_insert_home' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_position' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_only_login' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_default_css' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_custom_css' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_results' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_results_type' );


	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_votes' );
	register_setting( 'gt_simplevoteme_options', 'gt_simplevoteme_custom_post_types' );

}

function gt_simplevoteme_reset( $reset = false ) {
	if ( $reset ) {

		$the_query = new WP_Query( 'meta_key=_simplevotemevotes&amp;orderby=meta_value_num&amp;order=DESC&amp;' );
		// The Loop
		while ( $the_query->have_posts() ) : $the_query->the_post();
			update_post_meta( get_the_ID(), '_simplevotemevotes', "" );
		endwhile;
		wp_reset_postdata();
	}
}


/** Show the info in admin panel **/
function gt_simplevoteme_custom_columns() {
	$types = get_option( 'gt_simplevoteme_custom_post_types' );
	if ( is_array( $types ) ) {
		foreach ( $types as $type ) {
			//add cols
			add_filter( 'manage_edit-' . $type . '_columns', 'gt_simplevoteme_extra_columns' );
			//add content
			add_action( 'manage_' . $type . '_posts_custom_column', 'gt_simplevoteme_content_column_row', 10, 2 );
		}
	}
}

gt_simplevoteme_custom_columns();

function gt_simplevoteme_extra_columns( $columns ) {
	$columns['simplevotemetotal'] = __( 'Votes' );
	foreach ( gt_simplevoteme_get_vote_options() as $t => $vote_option ) {
		$columns[ 'simplevoteme' . $t ] = $vote_option->getImage();
	}

	return $columns;
}


function gt_simplevoteme_init_votes() {
	$k = array_keys( gt_simplevoteme_get_vote_options() );

	return array_combine( $k, array_fill( 0, count( $k ), [] ) );
}

function gt_simplevoteme_admin_list( $votes ) {
	return count( $votes ) . '<br>' . implode( '<br>', $votes );
}


function gt_simplevoteme_content_column_row( $column ) {
	if ( preg_match( '/^simplevoteme(.*)/', $column, $match ) ) {
		global $post;
		$post_id = $post->ID;
		$votes   = gt_simplevoteme_get_post_meta( $post_id );


//		echo '<div class="simplevoteme_admin_list">';
		if ( $column === 'simplevotemetotal' ) {
			echo sizeof( $votes, 1 ) - 3; //rest 3 because arrays for separate votes counts.
		} else {
			echo count( $votes[ $match[1] ] );
		}
//		echo '</div>';
	}
}


//Meta box for post
function gt_simplevoteme_metabox_votes( $post ) {
//	wp_nonce_field( basename( __FILE__ ), "meta-box-nonce" );
	$votes = gt_simplevoteme_get_post_meta( $post->ID, true );
	$total = sizeof( $votes, 1 ) - 3;

	wp_add_inline_script( 'gtsimplevoteme',
		"jQuery(document).ready(function () { $('#gt_simplevoteme_votes > h2').text('Votes ($total)');});" );
	echo gt_simplevoteme_draw_list_votes( $votes, $post->ID );
}


function gt_simplevoteme_add_meta_box_votes() {
	$types = get_option( 'gt_simplevoteme_custom_post_types' );
	if ( is_array( $types ) ) {
		foreach ( $types as $type ) {
			add_meta_box( "gt_simplevoteme_votes", "Votes", "gt_simplevoteme_metabox_votes", $type, "side", "high",
				null );
		}
	}
}

add_action( "add_meta_boxes", "gt_simplevoteme_add_meta_box_votes" );

add_action( 'admin_head', 'gt_simplevoteme_add_admin_head' );

function gt_simplevoteme_add_admin_head() {

}