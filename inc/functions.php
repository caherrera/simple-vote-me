<?php 


    
    //Order by total votes
    function gt_simplevoteme_get_highest_voted_posts($numberofpost){
        $output = '';
        $the_query = new WP_Query( 'meta_key=_simplevotemetotal&amp;orderby=meta_value_num&amp;order=DESC&amp;posts_per_page='.$numberofpost );
        // The Loop
        while ( $the_query->have_posts() ) : $the_query->the_post();
        $output .= '<li>';
        $output .= '<a href="'.get_permalink(). '" rel="bookmark">'.get_the_title().'('.get_post_meta(get_the_ID(), '_simplevotemetotal', true).')'.'</a> ';
        $output .= '</li>';
        endwhile;
        wp_reset_postdata();
        return $output;
    }

    

    function gt_simplevoteme_ranking_widget_init() {
     
        // Check for the required API functions
        if ( !function_exists('register_widget') )
        return;
     
        register_widget('GTSimpleVoteMeTopVotedWidget');
    }
 
    add_action('widgets_init', 'gt_simplevoteme_ranking_widget_init');

    
    
    //widget Vote

 
    function gt_simplevoteme_widget_init() {
     
        // Check for the required API functions
        if ( !function_exists('register_widget') )
        return;
     
        register_widget('GTSimpleVoteMeWidget');
    }
 
    add_action('widgets_init', 'gt_simplevoteme_widget_init');