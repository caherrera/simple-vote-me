<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 28/12/17
 * Time: 11:17 AM
 */
class GTSimpleVoteMeWidget extends GTSimpleVoteMeBaseWidget {

    public function __construct($id_base, $name, array $widget_options = array(), array $control_options = array())
    {

        parent::__construct(
            'Widget', //ID
            'Simple Vote me Widget', //Nombre
            array(
                'classname'   => 'GTSimpleVoteMeWidget',
                'description' => 'Simple Vote me Widget'
            )
        );
    }

    function form($instance) {
        // outputs the options form on admin
        $defaults = array( 'title' => 'Vote me!', 'type' => 'v' );
        $instance = wp_parse_args( (array) $instance, $defaults );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo 'Title:'; ?></label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php echo __('Type of Widget'); ?></label>
            <select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat" >
                <option value="v" <?php if ($instance['type'] == "v") echo "selected"; ?>><?php echo __('Vertical'); ?></option>
                <option value="h" <?php if ($instance['type'] == "h") echo "selected"; ?>><?php echo __('Horizontal'); ?></option>
            </select>
        </p>

        <?php

    }

    function update($new_instance, $old_instance) {
        // processes widget options to be saved

        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['type'] = $new_instance['type'];
        return $instance;
    }

    function widget($args, $instance) {
        // outputs the content of the widget
        extract( $args );
        $title = apply_filters('widget_title', $instance['title'] );
        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;

        echo do_shortcode('[simplevoteme type="'. $instance['type'] .'"]');

        echo $after_widget;
    }

}