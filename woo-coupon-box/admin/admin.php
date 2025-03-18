<?php

/*
Class Name: VI_WOO_COUPON_BOX_Admin_Admin
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2015 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VI_WOO_COUPON_BOX_Admin_Admin {
    
    protected $settings;
    
    function __construct() {
        add_filter( 'plugin_action_links_woo-coupon-box/woo-coupon-box.php', array(
            $this,
            'settings_link',
        ) );
        $this->settings = new VI_WOO_COUPON_BOX_DATA();
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_filter( 'manage_wcb_posts_columns', array( $this, 'add_column' ), 10, 1 );
        add_action( 'manage_wcb_posts_custom_column', array( $this, 'add_column_data' ), 10, 2 );
        /*filter email by campaign*/
        add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
        add_action( 'parse_query', array( $this, 'parse_query' ) );
    }
    
    public function add_column( $columns ) {
        $columns['campaign']     = esc_html__( 'Email campaign', 'woo-coupon-box' );
        $columns['coupon']       = esc_html__( 'Given coupon', 'woo-coupon-box' );
        $columns['coupon_value'] = esc_html__( 'Coupon value', 'woo-coupon-box' );
        $columns['expire']       = esc_html__( 'Expiry date', 'woo-coupon-box' );
        
        return $columns;
    }
    
    public function add_column_data( $column, $post_id ) {
        $meta         = get_post_meta( $post_id, 'woo_coupon_box_meta', true );
        $coupon_code  = isset( $meta['coupon'] ) ? $meta['coupon'] : '';
        $coupon_value = '';
        $expire       = '';
        if ( $coupon_code ) {
            $coupon = new WC_Coupon( $coupon_code );
            if ( $coupon ) {
                if ( $coupon->get_discount_type() == 'percent' ) {
                    $coupon_value = $coupon->get_amount() . '%';
                } else {
                    $coupon_value = $this->wc_price( $coupon->get_amount() );
                }
            }
            if ( $coupon->get_date_expires() ) {
                $date_expire = $coupon->get_date_expires();
                $expire      = date_i18n( get_option( 'date_format' ), strtotime( $date_expire ) );
            }
        }
        switch ( $column ) {
            case 'campaign':
                if ( $meta ) {
                    if ( isset( $meta['campaign'] ) ) {
                        $campaign = get_term_by( 'id', $meta['campaign'], 'wcb_email_campaign' );
                        echo $campaign ? esc_html( $campaign->name ) : '';
                    }
                } else {
                    $term_ids = get_the_terms( $post_id, 'wcb_email_campaign' );
                    if ( is_array( $term_ids ) && count( $term_ids ) ) {
                        foreach ( $term_ids as $term_id ) {
                            echo esc_html( $term_id->name );
                        }
                    }
                }
                break;
            case 'coupon':
                echo isset( $meta['coupon'] ) ? esc_html( $meta['coupon'] ) : '';
                break;
            case 'coupon_value':
                echo esc_html( $coupon_value );
                break;
            case 'expire':
                echo esc_html( $expire );
                break;
        }
    }
    
    public function wc_price( $price, $args = array() ) {
        extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
            'ex_tax_label'       => false,
            'currency'           => get_option( 'woocommerce_currency' ),
            'decimal_separator'  => get_option( 'woocommerce_price_decimal_sep' ),
            'thousand_separator' => get_option( 'woocommerce_price_thousand_sep' ),
            'decimals'           => get_option( 'woocommerce_price_num_decimals', 2 ),
            'price_format'       => get_woocommerce_price_format(),
        ) ) ) );
        
        $currency_pos = get_option( 'woocommerce_currency_pos' );
        $price_format = '%1$s%2$s';
        
        switch ( $currency_pos ) {
            case 'left' :
                $price_format = '%1$s%2$s';
                break;
            case 'right' :
                $price_format = '%2$s%1$s';
                break;
            case 'left_space' :
                $price_format = '%1$s&nbsp;%2$s';
                break;
            case 'right_space' :
                $price_format = '%2$s&nbsp;%1$s';
                break;
        }
        
        $negative = $price < 0;
        $price    = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * - 1 : $price ) );
        $price    = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
        
        if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
            $price = wc_trim_zeros( $price );
        }
        
        $formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, $currency, $price );
        
        return $formatted_price;
    }
    
    public function restrict_manage_posts() {
        global $typenow;
        $post_type = 'wcb'; // change to your post type
        $taxonomy  = 'wcb_email_campaign'; // change to your taxonomy
        if ( $typenow == $post_type ) {
            $selected      = isset( $_GET[ $taxonomy ] ) ? sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $info_taxonomy = get_taxonomy( $taxonomy );
            wp_dropdown_categories( array(
                'show_option_all' => esc_html__( "Show All", 'woo-coupon-box' ) . " {$info_taxonomy->label}",
                'taxonomy'        => $taxonomy,
                'name'            => $taxonomy,
                'orderby'         => 'name',
                'selected'        => $selected,
                'show_count'      => true,
                'hide_empty'      => true,
            ) );
        };
    }
    
    public function parse_query( $query ) {
        global $pagenow;
        $post_type = 'wcb'; // change to your post type
        $taxonomy  = 'wcb_email_campaign'; // change to your taxonomy
        $q_vars    = &$query->query_vars;
        if ( 'edit.php' == $pagenow && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == $post_type
             && isset( $q_vars[ $taxonomy ] )
             && is_numeric( $q_vars[ $taxonomy ] )
             && 0 != $q_vars[ $taxonomy ] ) {
            $term                = get_term_by( 'id', $q_vars[ $taxonomy ], $taxonomy );
            $q_vars[ $taxonomy ] = $term->slug;
        }
    }
    
    /**
     * Update hidden note
     */
    public function admin_init() {
        $current_time = current_time( 'U' );
        $hide         = isset( $_GET['wcb_hide'] ) ? sanitize_text_field( $_GET['wcb_hide'] ) : '';
        
        if ( $hide ) {
            update_option( 'wcb_note', 0 );
            update_option( 'wcb_note_time', $current_time );
        }
        
        $time_off = get_option( 'wcb_note_time' );
        if ( ! $time_off ) {
            update_option( 'wcb_note', 1 );
        } else {
            $time_next = $time_off + 30 * 24 * 60 * 60;
            if ( $time_next < $current_time ) {
                update_option( 'wcb_note', 1 );
            }
        }
    }
    
    /**
     * Link to Settings
     *
     * @param $links
     *
     * @return mixed
     */
    function settings_link( $links ) {
        $settings_link = '<a href="edit.php?post_type=wcb&page=woo_coupon_box" title="' . esc_html__( 'Settings', 'woo-coupon-box' ) . '">' . esc_html__( 'Settings', 'woo-coupon-box' ) . '</a>';
        array_unshift( $links, $settings_link );
        
        return $links;
    }
    
    /**
     * Function init when run plugin+
     */
    function init() {
        /*Register taxonomy for post type*/
        $this->register_taxonomy();
        
        /*Register post type*/
        $this->register_post_type();
        $this->load_plugin_textdomain();
        if ( class_exists( 'VillaTheme_Support' ) ) {
            new \VillaTheme_Support( array(
                'support'    => 'https://wordpress.org/support/plugin/woo-coupon-box/',
                'docs'       => 'http://docs.villatheme.com/?item=woo-coupon-box',
                'review'     => 'https://wordpress.org/support/plugin/woo-coupon-box/reviews/?rate=5#rate-response',
                'pro_url'    => 'https://1.envato.market/DzJ12',
                'css'        => VI_WOO_COUPON_BOX_CSS,
                'image'      => VI_WOO_COUPON_BOX_IMAGES,
                'slug'       => 'woo-coupon-box',
                'menu_slug'  => 'edit.php?post_type=wcb',
                'version'    => VI_WOO_COUPON_BOX_VERSION,
                'survey_url' => 'https://script.google.com/macros/s/AKfycbx1T8qn8xt66WKb_cudyzYgsDd18NGZ4MymotVJGGFslYhs30att1fKlEL6eFmPNZh7/exec',
            ) );
        }
    }
    
    /** Register taxonomy*/
    protected function register_taxonomy() {
        if ( taxonomy_exists( 'wcb_email_campaign' ) ) {
            return;
        }
        register_taxonomy( 'wcb_email_campaign', 'wcb', array(
            'hierarchical' => true,
            'label'        => 'Email Campaign',
            'public'       => false,
            'rewrite'      => false,
            'show_ui'      => true,
        ) );
        
        if ( ! term_exists( 'Uncategorized', 'wcb_email_campaign' ) ) {
            wp_insert_term( 'Uncategorized', 'wcb_email_campaign', array(
                'description' => '',
                'slug'        => 'uncategorized',
            ) );
        }
    }
    
    /**
     * Register post type email
     */
    protected function register_post_type() {
        if ( post_type_exists( 'wcb' ) ) {
            return;
        }
        
        $labels = array(
            'name'               => esc_html__( 'Email', 'woo-coupon-box' ),
            'singular_name'      => esc_html__( 'Email', 'woo-coupon-box' ),
            'menu_name'          => esc_html_x( 'Coupon Box for WooCommerce', 'Admin menu', 'woo-coupon-box' ),
            'name_admin_bar'     => esc_html_x( 'Email', 'Add new on Admin bar', 'woo-coupon-box' ),
            'add_new'            => esc_html_x( 'Add New Subscribe', 'role', 'woo-coupon-box' ),
            'add_new_item'       => esc_html__( 'Add New Email Subscribe', 'woo-coupon-box' ),
            'new_item'           => esc_html__( 'New Email', 'woo-coupon-box' ),
            'edit_item'          => esc_html__( 'Edit Email', 'woo-coupon-box' ),
            'view_item'          => esc_html__( 'View Email', 'woo-coupon-box' ),
            'all_items'          => esc_html__( 'Email Subscribe', 'woo-coupon-box' ),
            'search_items'       => esc_html__( 'Search Email', 'woo-coupon-box' ),
            'parent_item_colon'  => esc_html__( 'Parent Email:', 'woo-coupon-box' ),
            'not_found'          => esc_html__( 'No Email found.', 'woo-coupon-box' ),
            'not_found_in_trash' => esc_html__( 'No Email found in Trash.', 'woo-coupon-box' ),
        );
        $args   = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'email-subscribe' ),
            'capability_type'     => 'post',
            'capabilities'        => array(
                'create_posts' => false,
            ),
            'map_meta_cap'        => true,
            'has_archive'         => false,
            'taxonomies'          => array( 'wcb_email_campaign' ),
            'hierarchical'        => false,
            'menu_position'       => 2,
            'supports'            => array( 'title' ),
            'menu_icon'           => "dashicons-products",
            'exclude_from_search' => true,
        );
        register_post_type( 'wcb', $args );
    }
    
    /**
     * load Language translate
     */
    function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'woo-coupon-box' );
        load_textdomain( 'woo-coupon-box', VI_WOO_COUPON_BOX_LANGUAGES . "woo-coupon-box-$locale.mo" );
        load_plugin_textdomain( 'woo-coupon-box', false, VI_WOO_COUPON_BOX_LANGUAGES );
    }
    
}