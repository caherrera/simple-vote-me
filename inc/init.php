<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 30/8/18
 * Time: 4:14 PM
 */

add_action( 'widgets_init', 'gt_simplevoteme_init' );
add_action( 'plugins_loaded', 'gt_simplevoteme_checkversion' );
add_action( 'plugins_loaded', 'gt_simplevoteme_registermeta' );
