<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 29/12/17
 * Time: 12:38 PM
 */

function get_compliment_meta($compliment_id, $key = '', $single = false)
{
    return get_metadata('bp_compliments', $compliment_id, $key, $single);
}

function add_compliment_meta($compliment_id, $meta_key, $meta_value, $unique = false)
{
    $retval = add_metadata('bp_compliments', $compliment_id, $meta_key, $meta_value, $unique);

    return $retval;
}

function metadata_compliment_exists($compliment_id, $meta_key, $meta_value, $unique = false)
{
    $retval = metadata_exists('bp_compliments', $compliment_id, $meta_key, $meta_value, $unique);

    return $retval;
}

function update_compliment_meta($compliment_id, $meta_key, $meta_value, $prev_value = '')
{
    return update_metadata('bp_compliments', $compliment_id, $meta_key, $meta_value, $prev_value);
}