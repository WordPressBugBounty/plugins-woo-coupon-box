jQuery(document).ready(function ($) {
    'use strict';
    const api = wp.customize;
    api.panel('wcb_coupon_box_design', function (panel) {
        panel.expanded.bind(function (isExpanded) {
            var iframe = jQuery('iframe').contents().find('body')
            if (isExpanded) {
                iframe.find('.wcb-current-layout').addClass('wcb-md-show')
            } else {
                iframe.find('.wcb-current-layout').removeClass('wcb-md-show')
            }
        })
    })

    /*Popup icon*/
    api('woo_coupon_box_params[wcb_popup_icon]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_popup_icon label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_popup_icon .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })
    api.section('wcb_coupon_box_design_popup_icon', function (section) {
        section.expanded.bind(function (isExpanded) {
            var iframe = jQuery('iframe').contents().find('body')
            if (isExpanded) {
                iframe.find('.wcb-current-layout').removeClass('wcb-md-show')
            } else {
                iframe.find('.wcb-current-layout').addClass('wcb-md-show')
            }
        })
    })
    //                /*Button close*/
    api('woo_coupon_box_params[wcb_button_close]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_button_close label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_button_close .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_facebook_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_facebook_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_facebook_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_twitter_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_twitter_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_twitter_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_pinterest_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_pinterest_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_pinterest_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_instagram_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_instagram_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_instagram_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_dribbble_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_dribbble_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_dribbble_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_tumblr_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_tumblr_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_tumblr_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_google_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_google_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_google_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_vkontakte_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_vkontakte_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_vkontakte_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_linkedin_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_linkedin_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_linkedin_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })

    api('woo_coupon_box_params[wcb_social_icons_youtube_select]', function (value) {
        value.bind(function (newval) {
            jQuery('.woo_coupon_box_params-wcb_social_icons_youtube_select label').removeClass('wcb-radio-icons-active')
            jQuery('.woo_coupon_box_params-wcb_social_icons_youtube_select .' + newval).parent().addClass('wcb-radio-icons-active')
        })
    })
});