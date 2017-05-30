<?php
$styling_section[] = array(
    "type" => "sub_group",
    "id" => "mk_options_fullscreen_nav_skin",
    "name" => __("Styling & Coloring / Full Screen Navigation", "mk_framework") ,
    "desc" => __("This section allows you to modify the coloring of Full Screen navigation.", "mk_framework") ,
    "fields" => array(
        array(
            "name" => __('Logo', "mk_framework") ,
            "id" => "fullscreen_nav_logo",
            "default" => 'dark',
            "options" => array(
                "none" => __('None', "mk_framework"),
                "light" => __('Light', "mk_framework"),
                "dark" => __('Dark', "mk_framework"),
            ) ,
            "type" => "dropdown"
        ) ,
        array(
            "name" => __('Mobile Logo', "mk_framework") ,
            "id" => "fullscreen_nav_mobile_logo",
            "default" => 'dark',
            "options" => array(
                "dark" => __('Dark', "mk_framework"),
                "light" => __('Light', "mk_framework"),
                "custom" => __( 'Custom', 'mk_framework' ),
            ) ,
            "type" => "dropdown"
        ) ,
        array(
            "name" => __("Custom logo for Full screen menu on mobile screens ", "mk_framework") ,
            "desc" => __("Upload a custom logo for full screen menu only when it is opened on Mobile devices (small screens). Notice that this responsive logo is different from site's general 'Mobile version logo' which affect the site's header logo.", "mk_framework") ,
            "id" => "fullscreen_nav_mobile_logo_custom",
            "default" => "",
            "type" => "upload",
            "dependency" => array(
                   "element" => "fullscreen_nav_mobile_logo",
                   "value" => array(
                       "custom"
                   )
            ),  
        ) ,
        array(
            "name" => __('Background Color', "mk_framework") ,
            "id" => "fullscreen_nav_bg_color",
            "default" => "#444",
            "type" => "color"
        ) ,
        array(
            "name" => __('Link Color', "mk_framework") ,
            "id" => "fullscreen_nav_link_color",
            "default" => "#fff",
            "type" => "color"
        ) ,
        array(
            "name" => __('Link Hover Color', "mk_framework") ,
            "id" => "fullscreen_nav_link_hov_color",
            "default" => "#444",
            "type" => "color"
        ) ,
        array(
            "name" => __('Link Hover Background Color', "mk_framework") ,
            "id" => "fullscreen_nav_link_hov_bg_color",
            "default" => "#fff",
            "type" => "color"
        ) ,
        array(
            "name" => __('Close Button Skin', "mk_framework") ,
            "id" => "fullscreen_close_btn_skin",
            "default" => 'light',
            "options" => array(
                "light" => __('Light', "mk_framework"),
                "dark" => __('Dark', "mk_framework"),
            ) ,
            "type" => "dropdown"
        ) ,
    ) ,
);
