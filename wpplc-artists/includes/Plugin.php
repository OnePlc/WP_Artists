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
        $sSocialFB = get_post_meta( $post->ID, 'artist_social_fb', true );
        $sSocialIn = get_post_meta( $post->ID, 'artist_social_in', true );
        $sSocialYt = get_post_meta( $post->ID, 'artist_social_yt', true );

        ?>
        <label>Artist Category</label>
        <select id="artist_category" name="artist_category">
            <option value="1"<?=($iCategoryID == 1) ? ' selected="selected"' : ''?>>Main Artist</option>
            <option value="2"<?=($iCategoryID == 2) ? ' selected="selected"' : ''?>>Guest Artist</option>
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

    public function addThemeScripts() {
        wp_enqueue_script('gallery-frontend', '/wp-content/plugins/wpplc-artists/js/gallery-frontend.js', ['jquery']);
        wp_enqueue_script('infinite-scroll', '/wp-content/plugins/wpplc-artists/js/infinite-scroll.pkgd.min.js', ['jquery']);
        wp_enqueue_script('jquery-masonry', '/wp-content/plugins/wpplc-artists/js/masonry.pkgd.min.js', ['jquery']);
    }

    public function initElementorWidgets() {

        // Include Widget files
        require_once( __DIR__ . '/../elementor/widgets/gallery.php' );
        require_once( __DIR__ . '/../elementor/widgets/portrait_slider.php' );


        // Register widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \WPPLC_Artist_Gallery() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \WPPLC_Artist_PortraitSlider() );

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