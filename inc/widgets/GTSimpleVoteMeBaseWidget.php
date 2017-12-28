<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 28/12/17
 * Time: 11:58 AM
 */
abstract class GTSimpleVoteMeBaseWidget extends WP_Widget
{

    public function __construct($id_base, $name, array $widget_options = array(), array $control_options = array())
    {

        parent::__construct(
            "GTSimpleVoteMe".$id_base,
            '(Simple Vote Me)'.$name, //Nombre
            $widget_options,
            $control_options
        );
    }
}