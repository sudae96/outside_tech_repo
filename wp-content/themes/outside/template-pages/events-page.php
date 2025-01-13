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
    'orderby'        => 'rand',          
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

<?php echo do_shortcode('[ot_event_search]'); ?>


<style>
    * {
        margin: 0;
        padding: 0;
    }
    body {
        background: #d9d9d9;    
    }

    .card.card-details {
        padding: 25px;
        background: #FFDDA2;
    }

    .close-btn {
        padding: 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #211651;
        border-radius: 44px;
        height: 15px;
        width: 15px;
        color: #211651;
        margin-top: 10px;
        font-weight: 600;
        cursor: pointer;
    }

    .pagination-wrap {
        background: #66619F;
        height: 45px;
        margin-top: 4%;
        position: relative;
        width: 40%;
        margin-left: auto;
        margin-right: auto;
    }
    .pagination-wrap .pagination {
        background: #97DAEB;
        height: 100%;
        width: 100%;
        position: absolute;
        bottom: 30%;
        left: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 19px 0;
    }
    .pagination-wrap .pagination a {
        text-decoration: none;
        padding: 13px;
        line-height: 0;
        font-weight: 700;
        color: #000;
    }
    .pagination-wrap .pagination a.active {
        background: #EE5A46;
        border: 2px solid #66619F;
    }


    .search-events-section .events-wrap {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
        grid-gap: 18px;
        margin-top: 35px;
    }
    .search-events-section .events-wrap .event-box {
        background: #EE5A46;
    }
    .search-events-section .events-wrap .event-box .card {
        position: relative;
        left: 7px;
        top: -8px;
        background: #fff;
    }
    .search-events-section .card-content {
        padding: 10px;
        line-height: 1.2;   
        color: #211651;
    }
    .search-events-section .card-image video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .featured-events-section,
    .search-events-section {
        padding: 40px;
    }
    .featured-events-section .featured-events-inner,
    .search-events-section .search-events-inner {
        width: 60%;
        margin: 0 auto;
    }
    .featured-events-section .events-wrap {
        padding: 40px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between; 
        gap: 20px; 
        
        margin: 0 auto;
    }
    .featured-events-section .events-wrap .event-box {
        width: calc(50% - 30px);
        background: #EE5A46;
        margin-bottom: 20px;
    }

    .featured-events-section .card {
        width: 300px; 
        width: 100%; 
        height: 400px; 
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        position: relative;
        top: -4%;
        left: 15px;
    }

    .featured-events-section .card .card-image {
        flex: 1; /* Upper half */
        overflow: hidden;
    }

    .featured-events-section .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover; 
    }

    .featured-events-section .card-content {
        flex: 1; /* Bottom half */
        padding: 15px;
        background: #fff;
        color: #211651;
    }

    .featured-events-section .card-content h3 {
        margin: 0 0 10px;
        font-size: 18px;
    }

    .featured-events-section .card-content p {
        margin: 0;
        font-size: 14px;
        color: #555;
    }
    .featured-events-section .card-image video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
    }
    .search-events-section .card-image,
    .featured-events-section .card-image {
        position: relative;
    }
    .featured-events-section .play-icon,
    .search-events-section .play-icon {
        position: absolute;
        color: white;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
    .featured-events-section .plus-icon-wrap,
    .search-events-section .plus-icon-wrap {
        color: #EE5A46;
        border: 2px solid #EE5A46;
        border-radius: 45px;
        width: 35px;
        height: 35px;
        text-align: center;
        margin-top: 10px;
        cursor: pointer;
        font-weight: 600;
    }
    .featured-events-section .plus-icon-wrap span {
        line-height: 1.7px;
        vertical-align: middle;
        cursor: pointer;
    }

    .featured-events-section .events-wrap.slick-initialized {
        position: relative;
    }
    .featured-events-section .events-wrap.slick-initialized .slick-arrow {
        position: absolute;
    }
    .featured-events-section .events-wrap.slick-initialized .slick-prev {
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
    }
    .featured-events-section .events-wrap.slick-initialized .slick-next {
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
    }

    /* Search Events */
    .search-events-section {
        background: #87AB87;
    }
    .search-events-section .search-wrap {
        height: 45px;
        width: 90%;
        background: #66619F;
        margin: 0 auto;
        position: relative;
    }
    .search-events-section .search-wrap input[type="text"] {
        width: 100%;
        height: 45px;
        border-radius: 0;
        outline: 0;
        position: absolute;
        bottom: 30%;
        left: 15px;
        font-size: 19px;
        padding-left: 2%;
        font-weight: 700;
    }
    .search-events-section .card .plus-icon-wrap span {
        line-height: 1.7;
    }

    @media (max-width: 576px) {
        body {
            font-size: 12px; 
        }
        .card-content h3, .slick-list h3 {
            font-size: 14px !important; 
        }
        .plus-icon-wrap  {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>
<?php
get_footer();