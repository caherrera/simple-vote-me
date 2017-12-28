<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 28/12/17
 * Time: 11:16 AM
 */

//widget Ranking
class GTSimpleVoteMeTopVotedWidget extends GTSimpleVoteMeBaseWidget
{

    public function __construct(
        $id_base = null,
        $name = null,
        array $widget_options = array(),
        array $control_options = array()
    ) {

        parent::__construct(
            'TopVotedWidget', //ID
            'Ranking Simple Vote me', //Nombre
            array(
                'classname'   => 'GTSimpleVoteMeTopVotedWidget',
                'description' => 'Ranking Simple Vote me'
            )
        );
    }


    function form($instance)
    {
        // outputs the options form on admin
        $defaults = array('title' => 'Top Voted Posts', 'numberofposts' => '5');
        $instance = wp_parse_args((array)$instance, $defaults);

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo 'Title:'; ?></label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
                   value="<?php echo $instance['title']; ?>" class="widefat"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('numberofposts'); ?>"><?php echo 'Number of Posts'; ?></label>
            <input id="<?php echo $this->get_field_id('numberofposts'); ?>"
                   name="<?php echo $this->get_field_name('numberofposts'); ?>"
                   value="<?php echo $instance['numberofposts']; ?>" class="widefat"/>
        </p>

        <?php

    }

    function update($new_instance, $old_instance)
    {
        // processes widget options to be saved

        $instance                  = $old_instance;
        $instance['title']         = strip_tags($new_instance['title']);
        $instance['numberofposts'] = $new_instance['numberofposts'];

        return $instance;
    }

    function widget($args, $instance)
    {
        // outputs the content of the widget
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }

        echo '<ul>';
        echo $this->gt_simplevoteme_get_highest_voted_posts($instance['numberofposts']);
        echo '</ul>';
        echo $after_widget;
    }

    //Order by total votes
    function gt_simplevoteme_get_highest_voted_posts($numberofpost)
    {
        $output    = '';
        $the_query = new WP_Query('meta_key=_simplevotemetotal&amp;orderby=meta_value_num&amp;order=DESC&amp;posts_per_page=' . $numberofpost);
        // The Loop
        while ($the_query->have_posts()) : $the_query->the_post();
            $output .= '<li>';
            $output .= '<a href="' . get_permalink() . '" rel="bookmark">' . get_the_title() . '(' . get_post_meta(get_the_ID(),
                    '_simplevotemetotal', true) . ')' . '</a> ';
            $output .= '</li>';
        endwhile;
        wp_reset_postdata();

        return $output;
    }

}
