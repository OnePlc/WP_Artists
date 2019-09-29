<?php
class WPPLC_Artist_Gallery extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
    }

    public function get_name() {
        return 'wpplcartistgallery';
    }

    public function get_title() {
        return __( 'Artist Gallery', 'wpplc-artist' );
    }

    public function get_icon() {
        return 'fa fa-images';
    }

    public function get_categories() {
        return [ 'wpplc-artist' ];
    }

    protected function render() {
        $aSettings = $this->get_settings_for_display();

        $oPost = get_post();
        $sName = get_the_title();
        $aGalleryIDs = get_post_meta($oPost->ID, 'vdw_gallery_id', true);
        if(is_array($aGalleryIDs)) {
            ?>
            <div class="gallery-feed" style="padding-top:12px;">
                <div class="grid__col-sizer"></div>
                <div class="grid__gutter-sizer"></div>
                <!-- initial content loaded with prefill -->
            </div>

            <div class="page-load-status">
                <div class="loader-ellips infinite-scroll-request">
                    <span class="loader-ellips__dot"></span>
                    <span class="loader-ellips__dot"></span>
                    <span class="loader-ellips__dot"></span>
                    <span class="loader-ellips__dot"></span>
                </div>
                <p class="infinite-scroll-last">Ende der Galerie</p>
                <p class="infinite-scroll-error">Hier gibt es keine weiteren Bilder die wir dir anzeigen könnten</p>
            </div>

            <script>
                jQuery(function() {
                    var $grid = jQuery('.gallery-feed').masonry({
                        itemSelector: 'none', // select none at first
                        //itemSelector: '.infinite_scroll_item',
                        columnWidth: '.grid__col-sizer',
                        gutter: '.grid__gutter-sizer',
                        percentPosition: true,
                        horizontalOrder: true,
                        stagger: 30,
                        // nicer reveal transition
                        visibleStyle: { transform: 'translateY(0)', opacity: 1 },
                        hiddenStyle: { transform: 'translateY(100px)', opacity: 0 }
                    });

                    var msnry = $grid.data('masonry');

                    // initial items reveal
                    $grid.imagesLoaded( function() {
                        $grid.removeClass('item_invisible');
                        $grid.masonry( 'option', { itemSelector: '.infinite_scroll_item' });
                        var $items = $grid.find('.infinite_scroll_item');
                        $grid.masonry( 'appended', $items );
                    });

                    var nextPages = [
                        '/wp-content/plugins/wpplc-artists/feed.php?page=1&filter=<?=$oPost->ID?>',
                        '/wp-content/plugins/wpplc-artists/feed.php?page=2&filter=<?=$oPost->ID?>',
                        '/wp-content/plugins/wpplc-artists/feed.php?page=3&filter=<?=$oPost->ID?>',
                        '/wp-content/plugins/wpplc-artists/feed.php?page=4&filter=<?=$oPost->ID?>',
                        '/wp-content/plugins/wpplc-artists/feed.php?page=5&filter=<?=$oPost->ID?>',
                        '/wp-content/plugins/wpplc-artists/feed.php?page=6&filter=<?=$oPost->ID?>',
                        '/wp-content/plugins/wpplc-artists/feed.php?page=7&filter=<?=$oPost->ID?>'
                    ];

                    //-------------------------------------//
                    // init Infinite Scroll
                    jQuery('.gallery-feed').infiniteScroll({
                        path: function() {
                            return nextPages[ this.loadCount ];
                        },
                        append: '.infinite_scroll_item',
                        prefill:true,
                        history:false,
                        outlayer: msnry,
                        status: '.page-load-status'
                    });
                });
            </script>
            <style>
                .infinite_scroll_item, .grid__col-sizer {
                    width: 22%;
                }
                .infinite_scroll_item {
                    margin-bottom: 6px;
                }
                .infinite_scroll_item img {
                    width: 100%;
                }
                .gallery-feed {
                    padding-left:20px;
                    padding-right:20px;
                    -webkit-column-count: 5; /* Chrome, Safari, Opera */
                    -moz-column-count: 5; /* Firefox */
                    column-count: 5;
                }
                .artistCustomTitle::before {
                    content:'<?=strtoupper(substr($sName,0,1))?>' !important;
                }
            </style>
        <?php
        }
        ?>
        <?php
    }

    protected function _content_template() {}

    protected function _register_controls() {

    }

}
?>