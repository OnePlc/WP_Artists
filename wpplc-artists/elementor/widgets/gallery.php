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
        $iImgCount = 0;

        $sFilter = '';
        $aNextPages = [];
        $aArtists = [];
        $aGalleryIDs = get_post_meta($oPost->ID, 'vdw_gallery_id', true);
        if(array_key_exists('gallery_mode',$aSettings)) {
            $sFilter = $aSettings['gallery_mode'];
            if($sFilter == 'single') {
                $sFilter = $oPost->ID;
                if(is_array($aGalleryIDs)) {
                    $iImgCount = count($aGalleryIDs);
                }
            } else {
                $iCatID = 1;
                if($sFilter == 'guest') {
                    $iCatID = 2;
                }
                // load artists
                /**
                 * get questions for category
                 */
                $args = ['post_type' => 'wpplc_artist',
                    'meta_query' => [
                        [
                            'key' => 'artist_category',
                            'value' => $iCatID,
                        ],
                    ],
                ];

                ?>
                <div class="row text-center" style="text-align:center;">
                    <!-- Filter All -->
                    <button class="btn btn_redinv btn_redinv_active galleryFilter" data-artist-id="<?=$sFilter?>"> ALLE</button>
                    <?php
                    $loop = new WP_Query($args);
                    if($loop->have_posts()) {
                        $iImgCount = 0;
                        while($loop->have_posts()) : $loop->the_post();
                            $aGalleryIDs = get_post_meta(get_the_ID(), 'vdw_gallery_id', true);
                            if(is_array($aGalleryIDs)) {
                                $iImgCount += count($aGalleryIDs);
                                $iPagesTotal = round($iImgCount/5);
                                $aArtists[get_the_ID()] = (object)['id'=>get_the_ID(),'pages'=>$iPagesTotal];
                            }
                            ?>
                            <!-- Filter per Artist -->
                            <a class="btn btn_redinv galleryFilter" href="#" data-artist-id="<?=get_the_ID()?>"><?=get_the_title()?></a>
                            <?php
                        endwhile;
                    }
                    ?>
                </div>
                <?php
            }
        }
        $iPagesTotal = round($iImgCount/5);
        if($sFilter == 'main' || $sFilter == 'guest') {
            for($i = 1;$i <=100;$i++) {
                foreach($aArtists as $oArtist) {
                    if($oArtist->pages >= $i) {
                        $aNextPages[] ='/wp-content/plugins/wpplc-artists/feed.php?page='.$i.'&filter='.$oArtist->id;
                    }
                }
            }
        } else {
            for($i = 1;$i <=$iPagesTotal;$i++) {
                $aNextPages[] ='/wp-content/plugins/wpplc-artists/feed.php?page='.$i.'&filter='.$sFilter;
            }
        }
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
                        columnWidth: 20,
                        percentPosition: false,
                        horizontalOrder: true,
                        stagger: 0,
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

                    var nextPages = <?=json_encode($aNextPages)?>;
                    console.log(nextPages);

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

                    var aArtistInfo = <?=json_encode($aArtists)?>;

                    jQuery('.galleryFilter').on('click',function() {
                        var iArtistID = jQuery(this).attr('data-artist-id');
                        var nextPages;
                        console.log('reload gallery for artist '+iArtistID);
                        if(iArtistID == 'main' || iArtistID == 'guest') {
                            nextPages = <?=json_encode($aNextPages)?>;
                        } else {
                            var iMaxPages = aArtistInfo[iArtistID].pages;
                            nextPages = [];
                            for (i = 1; i <= iMaxPages; i++) {
                                nextPages.push( '/wp-content/plugins/wpplc-artists/feed.php?page='+i+'&filter='+iArtistID);
                            }
                        }

                        jQuery('.gallery-feed').infiniteScroll('destroy');
                        jQuery('.gallery-feed').data('infiniteScroll', null);
                        jQuery('.infinite_scroll_item').remove();

                        var $grid = jQuery('.gallery-feed').masonry({
                            itemSelector: 'none', // select none at first
                            //itemSelector: '.infinite_scroll_item',
                            columnWidth: 40,
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

                        return false;
                    });
                });
            </script>
            <style>
                .grid-sizer,
                .infinite_scroll_item { width: 40%; }

                .infinite_scroll_item {
                    margin-bottom:6px;
                }

                /**
                .grid__col-sizer {
                    width: 50%;
                }
                .infinite_scroll_item {
                    margin-bottom: 6px;
                }
                .infinite_scroll_item img {
                    width: 100%;
                }
                 */
                .gallery-feed {
                    padding-left:0px;
                    padding-right:0px;
                    width:100%;
                }
                .artistCustomTitle::before {
                    content:'<?=strtoupper(substr($sName,0,1))?>' !important;
                }
                .btn_redinv {
                    font-family:Oswald;
                    font-weight:300;
                    line-height:58px;
                }

                .btn_redinv_active {
                    line-height:20px;
                }

                .galleryFilter {
                    white-space: nowrap;
                }

                @media (min-width: 600px) {
                    .gallery-feed {
                        padding-left:0px;
                        padding-right:0px;
                        width:100%;
                    }

                    .infinite_scroll_item {
                        margin-bottom:6px;
                    }

                    .grid-sizer,
                    .infinite_scroll_item { width: 19%; }
                }
            </style>
        <?php
        }
        ?>
        <?php
    }

    protected function _content_template() {}

    protected function _register_controls() {
        /**
         * Gallery Mode
         */
        $this->start_controls_section(
            'gallery_mode_settings',
            [
                'label' => __( 'Gallery Mode', 'wpplc-artist' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'gallery_mode',
            [
                'label' => __( 'Mode', 'wpplc-artist' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'single',
                'options' => [
                    'main'  => __( 'Main Artists', 'wpplc-artist' ),
                    'guest' => __( 'Guest Artists', 'wpplc-artist' ),
                    'single' => __( 'Single', 'wpplc-artist' ),
                ],
            ]
        );

        $this->end_controls_section('gallery_mode_settings');

    }

}
?>