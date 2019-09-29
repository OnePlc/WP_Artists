<?php
class WPPLC_Artist_PortraitSlider extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
    }

    public function get_name() {
        return 'wpplcartistsliderportrait';
    }

    public function get_title() {
        return __( 'Artist Slider Portrait', 'wpplc-artist' );
    }

    public function get_icon() {
        return 'fa fa-images';
    }

    public function get_categories() {
        return [ 'wpplc-artist' ];
    }

    protected function render() {
        $aSettings = $this->get_settings_for_display();
        if(array_key_exists('wpplc_portrait_artist',$aSettings)) {
            $iArtistID = $aSettings['wpplc_portrait_artist'];
            $oArtist = get_post($iArtistID);
            $iImgAttID = get_post_meta( $iArtistID, 'artist_featured_image', true );
            $aImage = wp_get_attachment_image_src( $iImgAttID, 'large', false );
            $iImgHvrAttID = get_post_meta( $iArtistID, 'imagehover_attachment_id', true );
            $aImageHvr = wp_get_attachment_image_src( $iImgHvrAttID, 'large', false );
            ?>
            <div style="background:url(<?=$aImage[0]?>) no-repeat; background-size:contain; width:100%; min-height:420px;" class="plcArtSliderPortrait">
                <div class="plcArtSlidPortDesc" style="z-index:6; width:100%; height:100px; position:absolute; bottom:0; background-color: rgba(0, 0, 0, .8); text-align:center;">
                    <h3 class="plcArtSlidPortTitle" style="padding:0;"><?=$oArtist->post_title?></h3>
                    <a href="<?=get_the_permalink($iArtistID)?>" style="margin:0; padding:0;">
                        <i class="<?=$aSettings['link_icon']['value']?> plcArtLnkIcn" aria-hidden="true"></i>&nbsp;
                        Mehr über <?=explode(' ',$oArtist->post_title)[0]?> & Ihre Arbeiten
                    </a>
                </div>
                <div class="plcArtSldHoverBg" style="z-index:3; position:absolute; top:0; background:url(<?=$aImageHvr[0]?>) no-repeat; background-size:contain; width:100%; min-height:420px;" class="plcArtSliderPortrait">
                </div>
            </div>
            <script>
                /**
                var $lastbG = '';
                jQuery('.plcArtSliderPortrait').on('mouseover',function() {
                    var bgHoverUrl = jQuery(this).find('.plcArtSldHoverBg').val();
                    jQuery(this).css({background:'url('+bgHoverUrl+')'});
                }).on('mouseout',function() {
                    var bgUrl = jQuery(this).find('.plcArtSldBg').val();
                    //jQuery(this).css({background:'url('+bgUrl+')'});
                    jQuery(this).animate({opacity: 0}, 'slow', function() {
                        jQuery(this)
                            .css({'background': 'url('+bgUrl+')'})
                            .animate({opacity: 1});
                    });
                });**/
            </script>
            <?php
        }
    }

    protected function _content_template() {}

    protected function _register_controls() {
        $aArtists = [];
        $args = [
            'post_type'=>'wpplc_artist',
            'posts_per_page' => 10,
        ];
        $loop = new WP_Query( $args );
        while ($loop->have_posts()) : $loop->the_post();
            $aArtists[get_the_ID()] = get_the_title();
        endwhile;

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Artist', 'wpplc-artist' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'wpplc_portrait_artist',
            [
                'label' => __( 'Artist', 'wpplc-artist' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => $aArtists,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'sliderport_descbox',
            [
                'label' => __( 'Description Box', 'wpplc-artist' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'sliderport_descbox_padding',
            [
                'label' => __( 'Padding', 'wpplc-artist' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .plcArtSlidPortDesc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'sliderport_descbox_border',
                'label' => __( 'Border', 'wpplc-artist' ),
                'selector' => '{{WRAPPER}} .plcArtSlidPortDesc',
            ]
        );

        $this->add_control(
            'link_icon',
            [
                'label' => __( 'Link Icon', 'wpplc-artist' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'label_block' => true,
                'fa4compatibility' => 'icon',
            ]
        );

        $this->add_control(
            'sliderport_descbox_iconcolor',
            [
                'label' => __( 'Icon Color', 'wpplc-artist' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'scheme' => [
                    'type' => \Elementor\Scheme_Color::get_type(),
                    'value' => \Elementor\Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .plcArtLnkIcn' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section('sliderport_descbox');

        $this->start_controls_section(
            'sliderport_descboxtitle',
            [
                'label' => __( 'Artist Title', 'wpplc-artist' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'sliderport_descboxtitle_color',
            [
                'label' => __( 'Color', 'wpplc-artist' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'scheme' => [
                    'type' => \Elementor\Scheme_Color::get_type(),
                    'value' => \Elementor\Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .plcArtSlidPortTitle' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'sliderport_descboxtitle_typo',
                'label' => __( 'Typography', 'wpplc-artist' ),
                'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .plcArtSlidPortTitle',
            ]
        );

        $this->add_responsive_control(
            'sliderport_descboxtitle_padding',
            [
                'label' => __( 'Margin', 'wpplc-artist' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .plcArtSlidPortTitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section('list_items');

    }

}
?>