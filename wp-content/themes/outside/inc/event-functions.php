<?php

class EventFunction
{

    private $text_domain = 'outside-tech';

    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        // to add the events post type meta boxes and save
        add_action('add_meta_boxes', array( $this, 'event_options' ));
        add_action('save_post', array( $this, 'save_metabox_configuration' ));

        // Ajax Pagination
        add_action('wp_ajax_nopriv_ajax_pagination', [$this, 'ajax_pagination_template']);
        add_action('wp_ajax_ajax_pagination', [$this, 'ajax_pagination_template']);

        // Shortcode to display the search events widget
        add_shortcode('ot_event_search', array($this, 'event_search_widget'));

        // Add Events Settings Sub menu under the Events Post Type Menu
        add_action('admin_menu', [$this, 'add_events_submenu']);
        add_action('admin_post_events_settings_save', array( $this, 'events_settings_save' ));
    }

    function events_settings_save() {

        if ( current_user_can('manage_options') ) {
            if(!empty($_POST) && wp_verify_nonce( $_POST['events_settings_nonce_field'], 'events_settings_nonce' )) {	
                $events_shortcode = array();

                //Remove Unnecessary Fields From The Array
                if(isset($_POST['events_settings_nonce_field'])){
                    unset($_POST['events_settings_nonce_field']);
                }
                if(isset($_POST['_wp_http_referer'])){
                    unset($_POST['_wp_http_referer']);
                }
                if(isset($_POST['action'])){
                    unset($_POST['action']);
                }

                //Strip Slashes Deep Inside The array
                $events_shortcode = stripslashes_deep($this->sanitize_array($_POST));

                update_option('events_shortcode',$events_shortcode);
                wp_redirect(admin_url().'admin.php?page=events-settings&message=1');
            }
        }
    }

    public function add_events_submenu() {
        add_submenu_page(
            'edit.php?post_type=ss_events', 
            'Events Settings',                 
            'Events Settings',                
            'manage_options',              
            'events-settings',                 
            [$this, 'events_settings_page_callback']    
        );
    }

    public function events_settings_page_callback() {
        echo '<div class="wrap">';
        echo '<h1>Evemts Settings</h1>';
        $events_shortcode = get_option( 'events_shortcode' );
        ?>
        
        <div class="wrap">
            <?php
            if(isset($_GET['message']) && $_GET['message'] =='1'){ ?>
                <div class="notice notice-success is-dismissible">
                    <p>Settings saved successfully</p>
                </div>
            <?php } ?>

            <?php
            if(isset($_GET['message']) && $_GET['message'] == '0'){ ?>
                <div class="notice notice-error is-dismissible">
                    <p>Settings save failed.</p>
                </div>
            <?php } ?>

            <div class="content">
                <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
                    <?php wp_nonce_field('events_settings_nonce', 'events_settings_nonce_field'); ?>
                    <input type="hidden" name="action" value="events_settings_save"/>

                    <div class="ot-field-wrap">
                        <label>Search Events Shortcode</label>
                        <textarea rows="3" name="events_shortcode"><?php echo isset($events_shortcode) && !empty($events_shortcode) ? $events_shortcode['events_shortcode'] : '';  ?></textarea>
                    </div>

                    <div class="ot-notice">Note - you can add parameters on shortcode [ot_event_search posts_per_page="2"] to display only 2 posts per page</div>    

                    <button class="button-primary">Save Settings</button>
                </form>
            </div>
        </div>

        <?php
        echo '</div>';
    }
   
    function event_options() {
        $args = array(
            'public' => true,
        );

        $output = 'names'; 
        $operator = 'and';

        $post_types = get_post_types($args, $output, $operator);

        add_meta_box('options-metabox', 'Event Options', array( $this, 'display_configuration_metabox' ), array(  'ss_events', $post_types ));
    }


    function display_configuration_metabox() {
 
        if ( $option_values = get_post_meta(get_the_ID(), 'option_values', false) ) {
            $option_values = maybe_unserialize($option_values[ 0 ]);
        }


        $short_title = ( isset($option_values[ 'short_title' ]) ) ? esc_attr($option_values[ 'short_title' ]) : NULL;
        $description = ( isset($option_values[ 'description' ]) ) ? esc_attr($option_values[ 'description' ]) : NULL;
        $video_url = ( isset($option_values[ 'video_url' ]) ) ? esc_attr($option_values[ 'video_url' ]) : NULL;
        ?>

        <div class="ot-item-wrap">

            <div class="ot-field-wrap">
                <label>Short Title</label>
                <input type="text" name="options[short_title]" value="<?php echo isset($short_title) && !empty($short_title) ? $short_title : null;  ?>"/>
            </div>

            <div class="ot-field-wrap">
                <label>Description</label>
                <textarea rows="4" name="options[description]"><?php echo isset($description) && !empty($description) ? $description : null;  ?></textarea>
            </div>

            <div class="ot-field-wrap">
                <label>Video URL</label>
                <input type="url" name="options[video_url]" value="<?php echo isset($video_url) && !empty($video_url) ? $video_url : null;  ?>">
            </div>

            <?php wp_nonce_field( 'metabox_configuration_nonce', 'metabox_process' ); ?>
        </div>
    <?php
    }

    function save_metabox_configuration($post_id) {
        if ( isset($_POST[ 'metabox_process' ]) && wp_verify_nonce($_POST[ 'metabox_process' ], 'metabox_configuration_nonce') ) {
            
            $sanitized_val = stripslashes_deep($this->sanitize_array($_POST[ 'options' ]));
            update_post_meta($post_id, 'option_values', $sanitized_val);
        } else {
            return;
        }
    }

    /**
     * Sanitizes Multi-Dimensional Array
     * @param array $array
     * @param array $sanitize_rule
     * @return array
     *
     * @since 1.0.0
     */
    static function sanitize_array($array = array(), $sanitize_rule = array()) {
        if ( !is_array($array) || count($array) == 0 ) {
            return array();
        }

        foreach ( $array as $k => $v ) {
            if ( !is_array($v) ) {
                $default_sanitize_rule = (is_numeric($k)) ? 'html' : 'text';
                $sanitize_type = isset($sanitize_rule[ $k ]) ? $sanitize_rule[ $k ] : $default_sanitize_rule;
                $array[ $k ] = self:: sanitize_value($v, $sanitize_type);
            }

            if ( is_array($v) ) {
                $array[ $k ] = self:: sanitize_array($v, $sanitize_rule);
            }
        }

        return $array;
    }

    /**
     * Sanitizes Value
     *
     * @param type $value
     * @param type $sanitize_type
     * @return string
     *
     * @since 1.0.0
     */
    static function sanitize_value($value = '', $sanitize_type = 'html') {
        switch ( $sanitize_type ) {
            case 'text':
                $allowed_html = wp_kses_allowed_html('post');
                // var_dump($allowed_html);
                $allowed_html[ 'iframe' ] = array(
                    'class' => 1,
                    'height' => 1,
                    'width' => 1,
                    'style' => 1,
                    'id' => 1,
                    'type' => 1,
                    'src' => 1,
                    'frameborder' => 1,
                    'allowfullscreen' => 1,
                    'allow' => 1,
                    'data-src' => 1,
                    'webkitallowfullscreen' => 1,
                    'mozallowfullscreen' => 1,
                    'scrolling' => true,
                    'marginwidth' => true,
                    'marginheight' => true,
                    'name' => true,
                    'align' => true,
                );
                return wp_kses($value, $allowed_html);
                break;
            default:
                return sanitize_text_field($value);
                break;
        }
    }

    public function event_search_widget($atts) {
        ob_start();
        $atts = shortcode_atts(
            array(
                'posts_per_page' => 3, 
            ),
            $atts,
            'ot_event_search'
        );
        ?>
        <div class="search-events-section">
            <div class="search-events-inner">
                <div class="search-wrap">
                    <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/events/#ajax-posts')); ?>">
                        <input type="text" name="event_serch" id="event_serch" placeholder="Search here..." value="<?php echo isset($_GET['event_serch']) ? $_GET['event_serch'] : ''; ?>" />
                    </form>
                </div>

                <?php $this->ajax_pagination_template($atts); ?>
            </div>
        </div>        
        <?php
        $html = ob_get_contents();
        ob_get_clean();
        return $html;
    }

    function ajax_pagination_template($atts) {
        $post_per_page = isset($atts['posts_per_page']) && !empty($atts['posts_per_page']) ? $atts['posts_per_page'] : ($_POST['posts_per_page'] ? $_POST['posts_per_page'] : 3);
        ?>
        <div id="ajax-posts">
            <div class="events-wrap">
            <?php
            $paged = (isset($_POST['paged']) && !empty($_POST['paged'])) ? $_POST['paged'] : 1;
            
            $args1 = array(
                'post_type'      => 'ss_events', 
                'posts_per_page' => $post_per_page,      
                'paged'          => $paged,
                'orderby' => 'title',
                'order' => 'ASC',
                
            );
            if(isset($_GET['event_serch'])) {
                $args1['s'] = $_GET['event_serch'];
            }
            $search_events_query = new WP_Query($args1);
            
            if ($search_events_query->have_posts()) :
                while ($search_events_query->have_posts()) : $search_events_query->the_post();  
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
                ?>
                
                <?php
                
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
            <?php endif; ?>
            </div>
            
            <?php 
            $total_pages = $search_events_query->max_num_pages;
            if ($total_pages > 1) {
            ?>
            <div class="pagination-wrap">
                <div class="pagination" data-postsperpage="<?php echo $post_per_page; ?>">
                    <?php
                    
                        for ($i = 1; $i <= $total_pages; $i++) {
                            
                            $active_class = ($i == $paged) ? ' active' : null;
                            
                            echo '<a href="#" class="pagination-link' .$active_class. '" data-page="' . $i . '">' . $i . '</a>';
                        }
                    
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php
        if (wp_doing_ajax()) {	
            die(); 
        } 
    }
    
   
}
