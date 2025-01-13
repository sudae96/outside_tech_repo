<?php
/**
* Template Name: Events Page
*
* @package WordPress
* @subpackage Outside Tech
* @since OutsideTech 1.0
*/
get_header();
?>

<?php
$args = array(
    'post_type'      => 'ss_events',      
    'posts_per_page' => 4,              
    // 'orderby'        => 'rand',          
    'order'          => 'DESC',          
    'post_status'    => 'publish',      
);
$events_query = new WP_Query($args);
?>
<div class="featured-events-section">
    <div class="featured-events-inner">
        <h2>Featured Events</h2>
        <div class="events-wrap event-slider">
            <?php 
            if ($events_query->have_posts()) :
                while ($events_query->have_posts()) : $events_query->the_post();  
                    if ( $option_values = get_post_meta(get_the_ID(), 'option_values', false) ) {
                        $option_values = maybe_unserialize($option_values[ 0 ]);
                        $short_title = ( isset($option_values[ 'short_title' ]) ) ? esc_attr($option_values[ 'short_title' ]) : '';
                        $description = ( isset($option_values[ 'description' ]) ) ? esc_attr($option_values[ 'description' ]) : '';
                        $video_url = ( isset($option_values[ 'video_url' ]) ) ? esc_attr($option_values[ 'video_url' ]) : '';
                    }
                    $url = get_the_post_thumbnail_url();
                    $alt = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true);
                    $mime_type = get_post_mime_type(get_post_thumbnail_id());
                    
                    ?>
                    <div class="event-box">
                        <div class="card">
                            <?php if( !empty($url) || !empty($video_url) ) { ?>
                            <div class="card-image">
                                <?php if(!empty($video_url)) { ?>
                                    <video controls width="640" height="360">
                                        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php } else if(!empty($url)) { ?>
                                    <img src="<?php echo esc_url($url); ?>" alt="<?php echo !empty($alt) ? esc_attr($alt) : 'Card Image'; ?>">
                                    <!-- <span class="play-icon">D</span> -->
                                <?php } ?>
                            </div>
                            <?php } ?>

                            <?php if(!empty($short_title)) { ?>
                                <div class="card-content">
                                    <h3><?php echo esc_html($short_title); ?></h3>
                                    <!-- <p>This is the description content for the card. You can add more text as needed.</p> -->
                                    <div class="plus-icon-wrap"><span>+</span></div>
                                </div>
                            <?php } ?>
                        </div>

                        <?php if(!empty($description)) { ?>
                            <div class="card card-details" style="display: none;">
                                <?php echo $description; ?>
                                <div class="close-btn"><span>X</span></div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php 
                endwhile;
                wp_reset_postdata();
                else : 
                ?>
                <div class="event-box">
                    <div class="card">
                        <div class="card-image">
                            <img src="https://picsum.photos/500/300?image=1016" alt="Card Image">
                            <span class="play-icon">D</span>
                        </div>
                        <div class="card-content">
                            <h3>Race disproportionately predicts student outcomes in school and in conventional measures of life success.</h3>
                            <!-- <p>This is the description content for the card. You can add more text as needed.</p> -->
                            <div class="plus-icon-wrap"><span>+</span></div>
                        </div>
                    </div>
                </div>
                <div class="event-box">
                    <div class="card">
                        <div class="card-image">
                            <img src="https://picsum.photos/500/300?image=1016" alt="Card Image">
                        </div>
                        <div class="card-content">
                            <h3>Race disproportionately predicts student outcomes in school and in conventional measures of life success.</h3>
                            <!-- <p>This is the description content for the card. You can add more text as needed.</p> -->
                            <div class="plus-icon-wrap"><span>+</span></div>
                        </div>
                    </div>
                </div>
                <div class="event-box">
                    <div class="card">
                        <div class="card-image">
                            <img src="https://picsum.photos/500/300?image=1016" alt="Card Image">
                        </div>
                        <div class="card-content">
                            <h3>Race disproportionately predicts student outcomes in school and in conventional measures of life success.</h3>
                            <!-- <p>This is the description content for the card. You can add more text as needed.</p> -->
                            <div class="plus-icon-wrap"><span>+</span></div>
                        </div>
                    </div>
                </div>
                <div class="event-box">
                    <div class="card">
                        <div class="card-image">
                            <!-- <img src="https://picsum.photos/500/300?image=1016" alt="Card Image"> -->
                            <video controls width="640" height="360">
                                <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                                <source src="https://www.w3schools.com/html/mov_bbb.ogg" type="video/ogg">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="card-content">
                            <h3>Race disproportionately predicts student outcomes in school and in conventional measures of life success.</h3>
                            <div class="plus-icon-wrap"><span>+</span></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$events_shortcode = get_option( 'events_shortcode' );
$shortcode = isset($events_shortcode['events_shortcode']) && !empty($events_shortcode['events_shortcode']) ? $events_shortcode['events_shortcode'] : '[ot_event_search ]';
echo do_shortcode($shortcode); 
?>


<style>
    
</style>
<?php
get_footer();