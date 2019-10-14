<?php
namespace OnePlace\Artist;

class Plugin {
    public function createPostType() {
        $labels = array(
            'name'               => _x( 'Artists', 'post type general name', 'wpplc-artist' ),
            'singular_name'      => _x( 'Artist', 'post type singular name', 'wpplc-artist' ),
            'menu_name'          => _x( 'Artists', 'faq', 'wpplc-artist' ),
            'name_admin_bar'     => _x( 'Artists', 'faq', 'wpplc-artist' ),
            'add_new'            => _x( 'New Artist', 'book', 'wpplc-artist' ),
            'add_new_item'       => __( 'New Artist', 'wpplc-artist' ),
            'new_item'           => __( 'New Artist', 'wpplc-artist' ),
            'edit_item'          => __( 'Edit', 'wpplc-artist' ),
            'view_item'          => __( 'View', 'wpplc-artist' ),
            'all_items'          => __( 'Artists', 'wpplc-artist' ),
            'search_items'       => __( 'Find Artist:', 'wpplc-artist' ),
            'parent_item_colon'  => __( 'Parent Artists:', 'wpplc-artist' ),
            'not_found'          => __( 'No Artists found.', 'wpplc-artist' ),
            'not_found_in_trash' => __( 'No Artists in trash', 'wpplc-artist' )
        );

        register_post_type( 'wpplc_artist',
            [
                'labels' => $labels,
                'public' => true,
                'has_archive' => true,
                'supports' => ['editor','author', 'title','excerpt', 'thumbnail'],
                'register_meta_box_cb'=>[$this,'artistMetaBox'],
                'rewrite'=>[
                    'slug' => 'artist',
                    'with_front' => true,
                ],
            ]
        );

        add_action( 'restrict_manage_posts', [$this,'filterAdminPosts'] );
    }

    public function artistMetaBox() {
        add_meta_box(
            'artist_details',
            __( 'Artist Details', 'wpplc-artist' ),
            [$this,'showMetaBox'],
            'wpplc_artist',
            'side',
            'high'
        );

        add_meta_box(
            'artist_box',
            __( 'Artist Box', 'wpplc-artist' ),
            [$this,'showMetaBoxImages'],
            'wpplc_artist',
            'side',
            'high'
        );

        add_meta_box(
            'wpplc_artist-gallery-metabox',
            'Galery',
            [$this,'galleryMetaCallback'],
            'wpplc_artist',
            'normal',
            'high'
        );
    }

    public function showMetaBoxImages($post) {
        $iImgAttID = get_post_meta( $post->ID, 'artist_featured_image', true );
        $image = wp_get_attachment_image_src( $iImgAttID, 'thumbnail', false );

        wp_enqueue_media();

        $sBg = '';
        if(isset($image[0])) {
            $sBg = ' background:url('.$image[0].') no-repeat;';
        }

        $iImgHvrAttID = get_post_meta( $post->ID, 'imagehover_attachment_id', true );
        $imageHvr = wp_get_attachment_image_src( $iImgHvrAttID, 'thumbnail', false );

        wp_enqueue_media();

        $sBgHvr = '';
        if(isset($imageHvr[0])) {
            $sBgHvr = ' background:url('.$imageHvr[0].') no-repeat;';
        }
        ?>
        <div class='image-preview-wrapper'>
            <img id='image-preview' src='' width='100' height='100' style='max-height: 100px; width: 100px;<?=$sBg?>'>
        </div>
        <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
        <input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?=$iImgAttID?>'>
        <hr/>
        <div class='imagehover-preview-wrapper'>
            <img id='imagehover-preview' src='' width='100' height='100' style='max-height: 100px; width: 100px;<?=$sBgHvr?>'>
        </div>
        <input id="upload_imagehover_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
        <input type='hidden' name='imagehover_attachment_id' id='imagehover_attachment_id' value='<?=$iImgHvrAttID?>'>
        <?php
    }

    public function showMetaBox($post) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'artist_details_nonce', 'artist_details_nonce' );


        $iCategoryID = get_post_meta( $post->ID, 'artist_category', true );
        $sDateFrom = get_post_meta( $post->ID, 'artist_date_from', true );
        $sDateTo = get_post_meta( $post->ID, 'artist_date_to', true );
        $sArtSal = get_post_meta( $post->ID ,'artist_salution', true );
        $sSocialFB = get_post_meta( $post->ID, 'artist_social_fb', true );
        $sSocialIn = get_post_meta( $post->ID, 'artist_social_in', true );
        $sSocialYt = get_post_meta( $post->ID, 'artist_social_yt', true );

        ?>
        <label>Artist Category</label>
        <select id="artist_category" name="artist_category">
            <option value="1"<?=($iCategoryID == 1) ? ' selected="selected"' : ''?>>Main Artist</option>
            <option value="2"<?=($iCategoryID == 2) ? ' selected="selected"' : ''?>>Guest Artist</option>
        </select><br/>

        <label>Artist Gender</label>
        <select id="artist_salution" name="artist_salution">
            <option value="1"<?=($sArtSal == 1) ? ' selected="selected"' : ''?>>Male</option>
            <option value="2"<?=($sArtSal == 2) ? ' selected="selected"' : ''?>>Female</option>
        </select><br/>

        <label>Date From</label>
        <input type="date" name="artist_date_from" value="<?=$sDateFrom?>" /><br/>

        <label>Date To</label>
        <input type="date" name="artist_date_to" value="<?=$sDateTo?>" /><br/>

        <hr/>
        <h4>Social Media</h4>
        <label>Facebook</label>
        <input type="text" name="artist_social_fb" value="<?=$sSocialFB?>" /><br/>
        <label>Instagram</label>
        <input type="text" name="artist_social_in" value="<?=$sSocialIn?>" /><br/>
        <label>Youtube</label>
        <input type="text" name="artist_social_yt" value="<?=$sSocialYt?>" /><br/>
        <?php
    }

    function galleryMetaCallback($post) {
        $ids = get_post_meta($post->ID, 'vdw_gallery_id', true);
        ?>
        <table class="form-table">
            <tr><td>
                    <a class="gallery-add button" href="#" data-uploader-title="Add image(s) to gallery" data-uploader-button-text="Add image(s)">Add image(s)</a>

                    <ul id="gallery-metabox-list">
                        <?php if ($ids) : foreach ($ids as $key => $value) : $image = wp_get_attachment_image_src($value); ?>

                            <li>
                                <input type="hidden" name="vdw_gallery_id[<?php echo $key; ?>]" value="<?php echo $value; ?>">
                                <img class="image-preview" src="<?php echo $image[0]; ?>" title="<?php echo basename($image[0]); ?>">
                                <a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br>
                                <small><a class="remove-image" href="#">Remove image</a></small>
                            </li>

                        <?php endforeach; endif; ?>
                    </ul>

                </td></tr>
        </table>
    <?php }


    public function filterAdminPosts() {
        global $typenow;

        if( $typenow == 'wpplc_artist' ) {
            ?><select name="ADMIN_FILTER_FAQ_TYPE">
            <option value="0">All Artists</option>
            <option value="1">Main Artists</option>
            <option value="2">Guests Artists</option>
            </select>
            <?php
        }
    }

    public function galleryMetaboxEnqueue($hook) {
        if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
            wp_enqueue_script('gallery-metabox', '/wp-content/plugins/wpplc-artists/js/gallery-metabox.js', array('jquery', 'jquery-ui-sortable'));
            wp_enqueue_style('gallery-metabox', '/wp-content/plugins/wpplc-artists/css/gallery-metabox.css');
        }
    }

    public function media_selector_print_scripts() {

        $my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );

        ?><script type='text/javascript'>
            jQuery( document ).ready( function( $ ) {
                // Uploading files
                var artist_featured_1;
                if(typeof wp.media != 'undefined') {
                    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                    var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
                    jQuery('#upload_image_button').on('click', function (event) {
                        event.preventDefault();
                        // If the media frame already exists, reopen it.
                        if (artist_featured_1) {
                            // Set the post ID to what we want
                            artist_featured_1.uploader.uploader.param('post_id', set_to_post_id);
                            // Open frame
                            artist_featured_1.open();
                            return;
                        } else {
                            // Set the wp.media post id so the uploader grabs the ID we want when initialised
                            wp.media.model.settings.post.id = set_to_post_id;
                        }
                        // Create the media frame.
                        artist_featured_1 = wp.media.frames.artist_featured_1 = wp.media({
                            title: 'Select a image to upload',
                            button: {
                                text: 'Use this image',
                            },
                            multiple: false	// Set to true to allow multiple files to be selected
                        });
                        // When an image is selected, run a callback.
                        artist_featured_1.on('select', function () {
                            // We set multiple to false so only get one image from the uploader
                            attachment = artist_featured_1.state().get('selection').first().toJSON();
                            // Do something with attachment.id and/or attachment.url here
                            $('#image-preview').attr('src', attachment.url).css('width', 'auto');
                            $('#image_attachment_id').val(attachment.id);
                            // Restore the main post ID
                            wp.media.model.settings.post.id = wp_media_post_id;
                        });
                        // Finally, open the modal
                        artist_featured_1.open();
                    });

                    var artist_featured_2;
                    jQuery('#upload_imagehover_button').on('click', function (event) {
                        event.preventDefault();
                        // If the media frame already exists, reopen it.
                        if (artist_featured_2) {
                            // Set the post ID to what we want
                            artist_featured_2.uploader.uploader.param('post_id', set_to_post_id);
                            // Open frame
                            artist_featured_2.open();
                            return;
                        } else {
                            // Set the wp.media post id so the uploader grabs the ID we want when initialised
                            wp.media.model.settings.post.id = set_to_post_id;
                        }
                        // Create the media frame.
                        artist_featured_2 = wp.media.frames.artist_featured_2 = wp.media({
                            title: 'Select a image to upload',
                            button: {
                                text: 'Use this image',
                            },
                            multiple: false	// Set to true to allow multiple files to be selected
                        });
                        // When an image is selected, run a callback.
                        artist_featured_2.on('select', function () {
                            // We set multiple to false so only get one image from the uploader
                            attachment = artist_featured_2.state().get('selection').first().toJSON();
                            // Do something with attachment.id and/or attachment.url here
                            $('#imagehover-preview').attr('src', attachment.url).css('width', 'auto');
                            $('#imagehover_attachment_id').val(attachment.id);
                            // Restore the main post ID
                            wp.media.model.settings.post.id = wp_media_post_id;
                        });
                        // Finally, open the modal
                        artist_featured_2.open();
                    });
                }
            });
        </script><?php
    }

    public function saveMetabox($post_id) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['artist_details_nonce'] ) ? $_POST['artist_details_nonce'] : '';
        $nonce_action = 'artist_details_nonce';

        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }

        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }

        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( isset($_POST['image_attachment_id']) ) {
            update_post_meta($post_id, 'artist_featured_image', sanitize_text_field( $_POST['image_attachment_id']));
        }

        if ( isset($_POST['imagehover_attachment_id']) ) {
            update_post_meta($post_id, 'imagehover_attachment_id', sanitize_text_field( $_POST['imagehover_attachment_id']));
        }

        if ( isset($_POST['vdw_gallery_id']) ) {
            update_post_meta($post_id, 'vdw_gallery_id',  $_POST['vdw_gallery_id']);
        }

        if ( isset($_POST['artist_date_from']) ) {
            update_post_meta($post_id, 'artist_date_from', sanitize_text_field( $_POST['artist_date_from']));
        }

        if ( isset($_POST['artist_date_to']) ) {
            update_post_meta($post_id, 'artist_date_to', sanitize_text_field( $_POST['artist_date_to']));
        }

        if ( isset($_POST['artist_salution']) ) {
            update_post_meta($post_id, 'artist_salution', sanitize_text_field( $_POST['artist_salution']));
        }

        if ( isset($_POST['artist_social_fb']) ) {
            update_post_meta($post_id, 'artist_social_fb', sanitize_text_field( $_POST['artist_social_fb']));
        }

        if ( isset($_POST['artist_social_in']) ) {
            update_post_meta($post_id, 'artist_social_in', sanitize_text_field( $_POST['artist_social_in']));
        }

        if ( isset($_POST['artist_social_yt']) ) {
            update_post_meta($post_id, 'artist_social_yt', sanitize_text_field( $_POST['artist_social_yt']));
        }

        if ( isset($_POST['artist_category']) ) {
            update_post_meta($post_id, 'artist_category', sanitize_text_field( $_POST['artist_category']));
        }
    }

    public function __construct()
    {
        add_action( 'init', [$this,'createPostType'] );
        add_action( 'save_post', [$this, 'saveMetabox'], 10, 2 );
        // flush_rewrite_rules( false );
        add_action('admin_enqueue_scripts', [$this,'galleryMetaboxEnqueue']);

        // remove ugly shit
        add_action( 'admin_footer', [$this,'media_selector_print_scripts'] );
        add_action( 'elementor/elements/categories_registered', [$this,'addElementorWidgetCategories'] );
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'initElementorWidgets' ] );
        add_action( 'wp_enqueue_scripts', [$this,'addThemeScripts'] );
    }

    public function addThemeScripts()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            wp_enqueue_script('elementor-mobile-column-slider', '/wp-content/plugins/wpplc-artists/js/elementor-mobile-column-slider.js', ['jquery']);
        }
        wp_enqueue_script('gallery-frontend', '/wp-content/plugins/wpplc-artists/js/gallery-frontend.js', ['jquery']);
        wp_enqueue_script('infinite-scroll', '/wp-content/plugins/wpplc-artists/js/infinite-scroll.pkgd.min.js', ['jquery']);
        wp_enqueue_script('jquery-masonry', '/wp-content/plugins/wpplc-artists/js/masonry.pkgd.min.js', ['jquery']);
    }

    public function initElementorWidgets() {

        // Include Widget files
        require_once( __DIR__ . '/../elementor/widgets/gallery.php' );
        require_once( __DIR__ . '/../elementor/widgets/portrait_slider.php' );
        require_once( __DIR__ . '/../elementor/widgets/single_portrait.php' );

        // Register widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \WPPLC_Artist_Gallery() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \WPPLC_Artist_PortraitSlider() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \WPPLC_Artist_SinglePortrait() );
    }

    public function addElementorWidgetCategories( $elements_manager ) {
        $elements_manager->add_category(
            'wpplc-artist',
            [
                'title' => __( 'WP Artists', 'wpplc-artist' ),
                'icon' => 'fa fa-users',
            ]
        );
    }
}

$oPlugin = new Plugin();
?>