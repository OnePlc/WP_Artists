<?php

use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

class WPPLC_Artist_SinglePortrait extends \Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
    }

    public function get_name() {
        return 'wpplcartistsingleportrait';
    }

    public function get_title() {
        return __( 'Artist Portrait', 'wpplc-artist' );
    }

    public function get_icon() {
        return 'fa fa-user';
    }

    public function get_categories() {
        return [ 'wpplc-artist' ];
    }

    protected function render() {
        $aSettings = $this->get_settings_for_display();
        if(array_key_exists('wpplc_portrait_artist',$aSettings)) {
            $iArtistID = $aSettings['wpplc_portrait_artist'];
            if($iArtistID == 'guests') {
                $oArtist = (object)['post_title'=>'Guest Artists'];
                $sHref = '/guest-artists';
                $aImage = ['/wp-content/uploads/2019/10/Tattoo-Art-Karlsruhe_Unsere-Artists_Guest-Artists.jpg'];
                $aImageHvr = ['/wp-content/uploads/2019/10/Tattoo-Art-Karlsruhe_Unsere-Artists_Guest-Artists_hover.jpg'];
                $sLinkLabel = 'Mehr über unsere Gast-Künstler';
            } else {
                $oArtist = get_post($iArtistID);
                $iImgAttID = get_post_meta( $iArtistID, 'artist_featured_image', true );
                $aImage = wp_get_attachment_image_src( $iImgAttID, 'large', false );
                $iImgHvrAttID = get_post_meta( $iArtistID, 'imagehover_attachment_id', true );
                $aImageHvr = wp_get_attachment_image_src( $iImgHvrAttID, 'large', false );
                $sHref= get_the_permalink($iArtistID);
                $sLinkLabel = 'Mehr über '.explode(' ',$oArtist->post_title)[0].' & seine Arbeiten';
                $iArtSalution = get_post_meta($iArtistID, 'artist_salution', true);
                if($iArtSalution == 2) {
                    $sLinkLabel = 'Mehr über '.explode(' ',$oArtist->post_title)[0].' & ihre Arbeiten';
                }
                $sSocialIn = get_post_meta( $iArtistID, 'artist_social_in', true );
            }
            ?>
            <div style="width:33%; float:left;">
                <img src="<?=$aImage[0]?>" style="max-width:165px; max-height:200px;" title="<?=$oArtist->post_title?>" alt="<?=$oArtist->post_title?>" />
            </div>
            <div style="width:66%; float:left;">
                <h3 class="plcArtSlidPortTitle" style="padding:0;"><?=$oArtist->post_title?></h3>
                <p class="plcArtPortraitText"><?=$oArtist->post_content?></p>
                <a href="<?=$sSocialIn?>" style="margin:0; padding:0; display: inline-block;" target="_blank">
                    <i class="<?=$aSettings['link_icon']['value']?> plcArtLnkIcn" aria-hidden="true"></i>&nbsp;
                    <span class="plcArtSlidLink">Folgt <?=explode(' ',$oArtist->post_title)[0]?> auf Instagram</span>
                </a>
            </div>
            <?php
        }
    }

    protected function _content_template() {}

    protected function _register_controls() {
        $aArtists = ['guests'=>'Guest Artists'];
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
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'wpplc_portrait_artist',
            [
                'label' => __( 'Artist', 'wpplc-artist' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => $aArtists,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'sliderport_descbox',
            [
                'label' => __( 'Description Box', 'wpplc-artist' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'sliderport_descbox_padding',
            [
                'label' => __( 'Padding', 'wpplc-artist' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .plcArtSlidPortDesc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
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
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'fa4compatibility' => 'icon',
            ]
        );

        $this->add_control(
            'sliderport_descbox_iconcolor',
            [
                'label' => __( 'Icon Color', 'wpplc-artist' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
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
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'sliderport_descboxtitle_color',
            [
                'label' => __( 'Color', 'wpplc-artist' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .plcArtSlidPortTitle' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
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

        $this->end_controls_section('sliderport_descboxtitle');

        /**
         * Artist "View More" Link Style Settings
         */
        $this->start_controls_section(
            'sliderport_descboxtext',
            [
                'label' => __( 'Artist Text', 'wpplc-artist' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'sliderport_descboxtext_color',
            [
                'label' => __( 'Color', 'wpplc-artist' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .plcArtPortraitText' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sliderport_descboxtext_typo',
                'label' => __( 'Typography', 'wpplc-artist' ),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .plcArtPortraitText',
            ]
        );

        $this->end_controls_section('sliderport_descboxtext');

        /**
         * Artist "View More" Link Style Settings
         */
        $this->start_controls_section(
            'sliderport_descboxlink',
            [
                'label' => __( 'Artist Link', 'wpplc-artist' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sliderport_descboxlink_typo',
                'label' => __( 'Typography', 'wpplc-artist' ),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .plcArtSlidLink',
            ]
        );

        $this->end_controls_section('sliderport_descboxlink');
    }

}
?>