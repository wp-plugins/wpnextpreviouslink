<?php
/*
  Plugin Name: Codeboxr Next Previous Link
  Plugin URI: http://codeboxr.com/product/show-next-previous-article-plugin-for-wordpress
  Description: WordPress Next Previous Link
  Version: 1.1
  Author: codeboxr
  Author URI: http://codeboxr.com/
 */

register_activation_hook(__FILE__, 'wpnextpreviouslink_activate');
register_deactivation_hook(__FILE__, 'wpnextpreviouslink_deactivation');

function wpnextpreviouslink_activate() {
    $default_options_array = array(
        'image_name'          => 'arrow',
        'style_top'           => '50',
        'unit_type'           => '%',
        'show_home'           => '1',
        'show_archive'        => '1',
        'show_category'       => '1',
        'show_tag'            => '1',
        'show_author'         => '1',
        'show_date'           => '1',
        'same_cat'            => '1',
    );
    $saved_options_array  = (array) get_option('wpnextpreviouslinkbtn');
    $marged_options_array = array_merge($default_options_array, $saved_options_array);
    update_option('wpnextpreviouslinkbtn', $marged_options_array);
}

function wpnextpreviouslink_deactivation() {
    delete_option('wpnextpreviouslinkbtn');
}

function wordPress_next_previous_link() {
    global $style;
    $prev_link = get_previous_posts_link();
    $next_link = get_next_posts_link();

    $default_options_array = array(
        'image_name'          => 'arrow',
        'style_top'           => '50',
        'unit_type'           => '%',
        'show_home'           => '1',
        'show_archive'        => '1',
        'show_category'       => '1',
        'show_tag'            => '1',
        'show_author'         => '1',
        'show_date'           => '1',
        'same_cat'            => 'TRUE',
    );

    $saved_options_array  = get_option('wpnextpreviouslinkbtn');
    $marged_options_array = array_merge($default_options_array, $saved_options_array);
    extract($marged_options_array);

    $style = '<style type="text/css">
        #wpnp_previous{
                    background-image: url("' . WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/l_' . $image_name . '.png") ;
                    background-repeat: no-repeat;
                    
                    width: 35px;
                    height: 60px;
                   
                    position: fixed;
                    left: 10px;
                    top:' . $style_top . $unit_type . ';
                    display: block;
                    text-indent: -99999px;
                    }

        #wpnp_previous:hover{
                    background-image: url("' . WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/l_' . $image_name . '_hover.png");
                    }

        #wpnp_next{
                    background-image: url("' . WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/r_' . $image_name . '.png") ;
                    background-repeat: no-repeat;
                    width: 35px;
                    height: 60px;
                    float: left;
                    position: fixed;
                    right: 10px;
                    top: ' . $style_top . $unit_type . ';
                    display: block;
                    text-indent: -99999px;
                    }
        #wpnp_next:hover{
                    background-image: url("' . WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/r_' . $image_name . '_hover.png");
                    }
        </style>';
    echo $style;

    //var_dump(is_home());

    if (is_single()) {
        $prev_link = previous_post_link('%link', '<span id="wpnp_previous"> &larr; %title</span>', $same_cat); // will return html link
        $next_link = next_post_link('%link', '<span id="wpnp_next"> &larr; %title</span>', $same_cat); //will return html link
        echo $prev_link . $next_link;
    }
    elseif (is_home() || is_front_page()) {
        $show = TRUE;
        if ($show_home == '0') {
            $show = FALSE;
        }
       //var_dump($show);
        if ($show) {
            $prev_link = previous_posts_link(__('<span id="wpnp_previous">&rarr;</span>'));
            //var_dump($prev_link);
            //var_dump($next_link);

            $next_link = next_posts_link(__('<span id="wpnp_next">&larr;</span>'));
            echo $prev_link . $next_link;
        }
    }
    else {
        $show = TRUE;
        if (($show_archive == '0') || ($show_category == '0' && is_category()) || ($show_tag == '0' && is_tag()) || ($show_author == '0' && is_author()) || ($show_date == '0' && is_date())) {
            $show = FALSE;
        }
        if ($show) {
            $prev_link = previous_posts_link(__('<span id="wpnp_previous">&rarr;</span>'));
            $next_link = next_posts_link(__('<span id="wpnp_next">&larr;</span>'));
            echo $prev_link . $next_link;
        }
    }
}

add_action( 'wp_enqueue_scripts', 'nextprev_add_my_stylesheet' );
function nextprev_add_my_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'nextprev-style', plugins_url('css\wpnextpreviouslink.css', __FILE__) );
    wp_enqueue_style( 'nextprev-style' );
}

$cbnextprevioushook = add_action('admin_menu', 'cbnextprevious_admin');   //adding menu in admin menu settings

function cbnextprevious_admin() {
    global $cbnextprevioushook;
    if (function_exists('add_options_page')) {
        $page_hook = add_options_page('WP Next Previous Options', 'Next Previous Option', 8, 'cbnextpreviouslink', 'cbnextpreviousform');
        //add_action( 'admin_print_styles-' . $page_hook, 'cbnextprevadminstyles' );
    }
}

function cbnextprevadminstyles() {
   wp_register_style( 'cbnextprevtoggle', plugins_url('css\css-toggle-switch-gh-pages\toggle-switch.css', __FILE__) );
   wp_enqueue_style( 'cbnextprevtoggle' );
}

function cbnextpreviousform() {
    if (isset($_POST['wpnextpreviouslinkbtn'])) {

        check_admin_referer('admin_head-settings_page_wpnextpreviouslink', 'wpnextpreviouslink');
        $buttonno      = $_POST['image_name'];
        $style_top     = $_POST['style_top'];
        $unit_type     = $_POST['unit_type'];
        $show_home     = $_POST['show_home'];
        $show_archive  = $_POST['show_archive'];
        $show_category = $_POST['show_category'];
        $show_tag      = $_POST['show_tag'];
        $show_author   = $_POST['show_author'];
        $show_date     = $_POST['show_date'];
        $same_cat      = $_POST['same_cat'];
        $ar            = array(
            'image_name'    => $buttonno,
            'style_top'     => $style_top,
            'unit_type'     => $unit_type,
            'show_home'     => $show_home,
            'show_archive'  => $show_archive,
            'show_category' => $show_category,
            'show_tag'      => $show_tag,
            'show_author'   => $show_author,
            'show_date'     => $show_date,
            'same_cat'      => $same_cat,
        );

        $updatesuccess = update_option('wpnextpreviouslinkbtn', $ar);
//         $updatesuccess = update_option('wpnextpreviouslinkbtn', '');
    }
    $ar            = get_option('wpnextpreviouslinkbtn');
    $image_name    = $ar['image_name'];
    ?>
    <?php
    echo '<style type="text/css">
        #wpnp_previous{
                    /*
                    background-image: url("' . WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/l_' . $image_name . '.png") ;
                    background-repeat: no-repeat;
                    
                    width: 35px;
                    height: 60px;
                    float: left;
                    position: relative;
                    left: 10px;
                    text-indent: -99999px;
                    */
                    display: block;                    
                    }
       
        </style>';
    ?>
    <div class="wrap">
        <div class="icon32" id="icon-options-general"></div>
        <h2>WP Next Previous</h2>
        <div id="poststuff" class="metabox-holder has-right-sidebar">                                                    
            <div id="post-body">
                <div id="post-body-content">
                <table cellspacing="0" class="widefat post fixed">
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <thead>
                    <tr>
                        <th colspan="3">Plugin Options</th>
                    </tr>
                </thead>
                <tbody>
                    <tr valign="top">
                        <td>Image Type:</td>
                        <td colspan="2">
                            <select id="image_name" name="image_name" style="width: 100px;">
                                <option value="arrow" <?php selected($ar['image_name'], 'arrow'); ?>>Classic</option>
                                <option value="arrow_blue" <?php selected($ar['image_name'], 'arrow_blue'); ?>>Blue</option>
                                <option value="arrow_dark" <?php selected($ar['image_name'], 'arrow_dark'); ?>>Dark</option>
                                <option value="arrow_green" <?php selected($ar['image_name'], 'arrow_green'); ?>>Green</option>
                                <option value="arrow_orange" <?php selected($ar['image_name'], 'arrow_orange'); ?>>Orange</option>
                                <option value="arrow_red" <?php selected($ar['image_name'], 'arrow_red'); ?>>Red</option>
                                <option value="sm_arrow_round" <?php selected($ar['image_name'], 'sm_arrow_round'); ?>>Round</option>
                            </select>
                            <?php //var_dump($ar['image_name']); ?>
                            <div style="margin-top:10px;" id="wpnp_previous"><img id="wpnp_previousimg" src="<?php echo WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/l_'.$ar['image_name'].'.png'; ?>" alt="arrow image" /></div>
                        </td>
                        
                <br />
                </tr>
                <tr valign="top">
                    <td>Position from top</td>
                    <td colspan="2">
                        <input type="text" style="width: 100px;" name="style_top" value="<?php echo $ar['style_top']; ?>" />
                        <select name="unit_type" style="width: 100px;">
                            <option value="%" <?php selected($ar['unit_type'], '%'); ?>>%</option>
                            <option value="px" <?php selected($ar['unit_type'], 'px'); ?>>px</option>
                        </select>
                        <br />
                    </td>
                </tr>
                <tr valign="top">
                    <td>Show in Home page</td>
                    <td colspan="2">
                        <div class="switch candy green" style="float: left; width: 100px;">
                            <input id="homeyes" name="show_home" type="radio" value="1" <?php checked($ar['show_home'], '1'); ?> >
                            <label for="homeyes" onclick="">Yes</label>

                            <input id="homeno" name="show_home" type="radio" value="0" <?php checked($ar['show_home'], '0'); ?> > 
                            <label for="homeno" onclick="">No</label>
                            
                            <span class="slide-button">&nbsp;</span>
                        </div>

                        <!-- <input type="radio" name="show_home" value="1" <?php checked($ar['show_home'], '1'); ?> />Yes -->

                        <!-- <input style="margin-left: 15px;" type="radio" name="show_home" value="0" <?php checked($ar['show_home'], '0'); ?> />No -->
                        <!-- <br /> -->
                    </td>
                </tr>

                <!--                global on off archive view-->
                <tr valign="top">
                    <td>Show Archive View (Category, Tag, Author, Date)</td>
                    <td colspan="2">
                        <div class="switch candy green" style="float: left; width: 100px;">
                            <input id="archiveyes" name="show_archive" type="radio" value="1" <?php checked($ar['show_archive'], '1'); ?> >
                            <label for="archiveyes" onclick="">Yes</label>

                            <input id="archiveno" name="show_archive" type="radio" value="0" <?php checked($ar['show_archive'], '0'); ?> > 
                            <label for="archiveno" onclick="">No</label>
                            
                            <span class="slide-button">&nbsp;</span>
                        </div>
                        <!-- <input type="radio" name="show_archive" value="1" <?php checked($ar['show_archive'], '1'); ?> />Yes
                        <input style="margin-left: 15px;" type="radio" name="show_archive" value="0" <?php checked($ar['show_archive'], '0'); ?> />No
                        <br /> -->
                    </td>
                </tr>

                <!--                category-->
                <tr valign="top" >
                    <td style="padding-left: 40px;">Show in Category View</td>
                    <td colspan="2">
                        <div class="switch candy green" style="float: left; width: 100px;">
                            <input id="categoryyes" name="show_category" type="radio" value="1" <?php checked($ar['show_category'], '1'); ?> >
                            <label for="categoryyes" onclick="">Yes</label>

                            <input id="categoryno" name="show_category" type="radio" value="0" <?php checked($ar['show_category'], '0'); ?> > 
                            <label for="categoryno" onclick="">No</label>
                            
                            <span class="slide-button">&nbsp;</span>
                        </div>
                        <!-- <input type="radio" name="show_category" value="1" <?php checked($ar['show_category'], '1'); ?> />Yes
                        <input style="margin-left: 15px;" type="radio" name="show_category" value="0" <?php checked($ar['show_category'], '0'); ?> />No
                        <br /> -->
                    </td>
                </tr>

                <!--                tag-->
                <tr valign="top">
                    <td style="padding-left: 40px;">Show in Tag View</td>
                    <td colspan="2">
                        <div class="switch candy green" style="float: left; width: 100px;">
                            <input id="tagyes" name="show_tag" type="radio" value="1" <?php checked($ar['show_tag'], '1'); ?> >
                            <label for="tagyes" onclick="">Yes</label>

                            <input id="tagno" name="show_tag" type="radio" value="0" <?php checked($ar['show_tag'], '0'); ?> > 
                            <label for="tagno" onclick="">No</label>
                            
                            <span class="slide-button">&nbsp;</span>
                        </div>
                        <!-- <input type="radio" name="show_tag" value="1" <?php checked($ar['show_tag'], '1'); ?> />Yes
                        <input style="margin-left: 15px;" type="radio" name="show_tag" value="0" <?php checked($ar['show_tag'], '0'); ?> />No
                        <br /> -->
                    </td>
                </tr>

                <!--                author -->
                <tr valign="top">
                    <td style="padding-left: 40px;">Show in Author View</td>
                    <td colspan="2">
                        <div class="switch candy green" style="float: left; width: 100px;">
                            <input id="authoryes" name="show_author" type="radio" value="1" <?php checked($ar['show_author'], '1'); ?> >
                            <label for="authoryes" onclick="">Yes</label>

                            <input id="authorno" name="show_author" type="radio" value="0" <?php checked($ar['show_author'], '0'); ?> > 
                            <label for="authorno" onclick="">No</label>
                            
                            <span class="slide-button">&nbsp;</span>
                        </div>
                        <!-- <input type="radio" name="show_author" value="1" <?php checked($ar['show_author'], '1'); ?> />Yes
                        <input style="margin-left: 15px;" type="radio" name="show_author" value="0" <?php checked($ar['show_author'], '0'); ?> />No
                        <br /> -->
                    </td>
                </tr>

                <!--                date-->
                <tr valign="top">
                    <td style="padding-left: 40px;">Show in Date View</td>
                    <td colspan="2">
                        <div class="switch candy green" style="float: left; width: 100px;">
                            <input id="dateyes" name="show_date" type="radio" value="1" <?php checked($ar['show_date'], '1'); ?> >
                            <label for="dateyes" onclick="">Yes</label>

                            <input id="dateno" name="show_date" type="radio" value="0" <?php checked($ar['show_date'], '0'); ?> > 
                            <label for="dateno" onclick="">No</label>
                            
                            <span class="slide-button">&nbsp;</span>
                        </div>
                        <!-- <input type="radio" name="show_date" value="1" <?php checked($ar['show_date'], '1'); ?> />Yes
                        <input style="margin-left: 15px;" type="radio" name="show_date" value="0" <?php checked($ar['show_date'], '0'); ?> />No
                        <br /> -->
                    </td>
                </tr>

                <!--                global value-->
                <tr valign="top">
                    <td>Navigate by Category</td>
                    <td colspan="2">
                        <div class="switch candy green" style="float: left; width: 100px;">
                            <input id="samecatyes" name="same_cat" type="radio" value="1" <?php checked($ar['same_cat'], '1'); ?> >
                            <label for="samecatyes" onclick="">Yes</label>

                            <input id="samecatno" name="same_cat" type="radio" value="0" <?php checked($ar['same_cat'], '0'); ?> > 
                            <label for="samecatno" onclick="">No</label>
                            
                            <span class="slide-button">&nbsp;</span>
                        </div>
                        <!-- <input type="radio" name="same_cat" value="1" <?php checked($ar['same_cat'], '1'); ?> />Yes
                        <input style="margin-left: 15px;" type="radio" name="same_cat" value="0" <?php checked($ar['same_cat'], '0'); ?> />No
                        <br /> -->
                    </td>
                </tr>

                </tbody>
            </table>
            <?php wp_nonce_field('admin_head-settings_page_wpnextpreviouslink', 'wpnextpreviouslink'); ?>
            <p class="submit"><input type="submit" name="wpnextpreviouslinkbtn" class="button-primary" value="Save Changes" ></p>
        </form>
            </div>
        </div><!--postbody-->
         <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#image_name').change(function(){
                var imagename = jQuery('#image_name').val();
                //jQuery('#wpnp_previous').css({'background-image': 'url("<?php echo WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/l_'?>'+imagename+'.png")'});
                jQuery('#wpnp_previousimg').attr('src', '<?php echo WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/l_'?>'+imagename+'.png');
                /*
                jQuery('#wpnp_previous').hover(function() {
                    jQuery(this).css({'background-image': 'url("<?php echo WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/l_'?>'+imagename+'_hover.png")'});                       
                }, function() {
                    jQuery(this).css({'background-image': 'url("<?php echo WP_CONTENT_URL . '/plugins/wpnextpreviouslink/images/l_'?>'+imagename+'.png")'});
                });
                */

            });
        });
        </script>
        <div id="side-info-column" class="inner-sidebar">                                        
                                        <?php 
                                        $plugin_data = get_plugin_data( __FILE__ );
                                        //var_dump($plugin_data);
                                        ?>                                        
                                        <div class="postbox">
                                                <h3>Plugin Info</h3>
                                                <div class="inside">
                                                        <p>Plugin Name : <?php echo $plugin_data['Title']?> <?php echo $plugin_data['Version']?></p>
                                                        <!--p>Plugin Url: <?php echo $plugin_data['PluginURI']; ?></p-->
                                                        <p>Author : <?php echo $plugin_data['Author']?></p>
                                                        <p>Website : <a href="http://codeboxr.com" target="_blank">codeboxr.com</a></p>
                                                        <p>Email : <a href="mailto:info@codeboxr.com" target="_blank">info@codeboxr.com</a></p>
                                                        <p>Twitter : <a href="http://twitter.com/codeboxr" target="_blank">@Codeboxr</a></p>
                                                        <p>Facebook : <a href="http://facebook.com/codeboxr" target="_blank">http://facebook.com/codeboxr</a></p>
                                                        <p>Linkedin : <a href="www.linkedin.com/company/codeboxr" target="_blank">http://linkedin.com/company/codeboxr</a></p>
                                                        <p>Gplus : <a href="https://plus.google.com/104289895811692861108" target="_blank">Google Plus</a></p>
                                                </div>
                                        </div>
                                                                  
                                        <div class="postbox">
                                                <h3>Help & Supports</h3>
                                                <div class="inside">
                                                    <p>Support: <a href="http://codeboxr.com/contact-us.html" target="_blank">Contact Us</a></p>
                                                    <p><i class="icon-envelope"></i> <a href="mailto:info@codeboxr.com">info@codeboxr.com</a></p>
                                                    <p><i class="icon-phone"></i> <a href="tel:008801717308615">+8801717308615</a> (CEO: Sabuj Kundu)</p>
                                                    <!--p><i class="icon-building"></i>  Address: Flat-11B1, 252 Elephant Road (Near Kataban Crossing), Dhaka 1205, Bangladesh.<br-->
                                                </div>
                                        </div>  
                                        <div class="postbox">
                                                <h3>Codeboxr Updates</h3>
                                                <div class="inside">
                                                    <?php
                                                        include_once(ABSPATH . WPINC . '/feed.php');
                                                        if(function_exists('fetch_feed')) {
                                                                $feed = fetch_feed('http://codeboxr.com/feed');
                                                                // $feed = fetch_feed('http://feeds.feedburner.com/codeboxr'); // this is the external website's RSS feed URL
                                                                if (!is_wp_error($feed)) : $feed->init();
                                                                        $feed->set_output_encoding('UTF-8'); // this is the encoding parameter, and can be left unchanged in almost every case
                                                                        $feed->handle_content_type(); // this double-checks the encoding type
                                                                        $feed->set_cache_duration(21600); // 21,600 seconds is six hours
                                                                        $limit = $feed->get_item_quantity(6); // fetches the 18 most recent RSS feed stories
                                                                        $items = $feed->get_items(0, $limit); // this sets the limit and array for parsing the feed
                                                                        
                                                                        $blocks = array_slice($items, 0, 6); // Items zero through six will be displayed here
                                                                        echo '<ul>';
                                                                        foreach ($blocks as $block) {
                                                                            $url = $block->get_permalink();
                                                                            echo '<li><a target="_blank" href="'.$url.'">';
                                                                            echo '<strong>'.$block->get_title().'</strong></a>';                                                                            
                                                                            echo '</li>';
                                                                            
                                                                        }//end foreach
                                                                        echo '</ul>';
                                                                endif;
                                                        }
                                                        ?>
                                                </div>
                                        </div>
                                        <div class="postbox">
                                            <h3>Video demo</h3>
                                            <div class="inside">
                                                <!-- <iframe width="260" height="158" frameborder="0" src="http://www.screenr.com/embed/2Ow7"></iframe> -->
                                            </div>
                                        </div>
                                        <div class="postbox">
                                            <h3>Codeboxr on facebook</h3>
                                            <div class="inside">
                                                <iframe scrolling="no" frameborder="0" allowtransparency="true" style="border:none; overflow:hidden; width:260px; height:258px;" src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fcodeboxr&amp;width=260&amp;height=258&amp;show_faces=true&amp;colorscheme=light&amp;stream=false&amp;border_color&amp;header=false&amp;appId=558248797526834"></iframe>
                                            </div>
                                        </div>                                                
                                </div> <!-- side-info-column -->
    </div>

    </div>
    <?php
}

add_action('wp_footer', 'wordPress_next_previous_link');

// $plugin = plugin_basename(__FILE__); 
// add_filter("plugin_action_links_$plugin", 'wpnp_settings_link' );
// function wpnp_settings_link($links) { 
//     $settings_link = '<a href="admin.php?page=cbnextprevious_admin">Settings</a>'; 
//     array_unshift($links, $settings_link); 
//     return $links; 
// }