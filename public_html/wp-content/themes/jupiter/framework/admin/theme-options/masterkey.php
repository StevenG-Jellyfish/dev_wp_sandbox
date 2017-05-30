<?php

require_once THEME_ADMIN . '/theme-options/builder/framework.php';

$general_section = $styling_section = $portfolio_section = $blog_section = $advanced_section = $ecommerce_section = $typography_section = $options = array();

require_once THEME_ADMIN . '/theme-options/params/general_settings/global.php';
require_once THEME_ADMIN . '/theme-options/params/general_settings/logos.php';
require_once THEME_ADMIN . '/theme-options/params/general_settings/header_toolbar.php';
require_once THEME_ADMIN . '/theme-options/params/general_settings/header.php';
require_once THEME_ADMIN . '/theme-options/params/general_settings/social_networks.php';
require_once THEME_ADMIN . '/theme-options/params/general_settings/preloader.php';
require_once THEME_ADMIN . '/theme-options/params/general_settings/custom_sidebar.php';
require_once THEME_ADMIN . '/theme-options/params/general_settings/footer.php';
require_once THEME_ADMIN . '/theme-options/params/general_settings/quick_contact.php';

$options[] = array(
    "type" => "group",
    "id" => "mk_options_general",
    "menu" => array(
        "mk_options_global_settings" => __("Global Settings", "mk_framework") ,
        "mk_options_logos_section" => __("Favicon & Logos", "mk_framework") ,
        "mk_options_header_toolbar_section" => __("Header Toolbar", "mk_framework") ,
        "mk_options_header_section" => __("Header", "mk_framework") ,
        "mk_options_social_networks_section" => __("Social Networks", "mk_framework") ,
        "mk_options_preloader_section" => __("Site Preloader", "mk_framework") ,
        "mk_options_sidebar" => __("Custom Sidebars", "mk_framework") ,
        "mk_options_footer" => __("Footer", "mk_framework") ,
        "mk_options_quick_contact" => __("Quick Contact Form", "mk_framework") ,
    ) ,
    "fields" => $general_section
);

require_once THEME_ADMIN . '/theme-options/params/styling/general_colors.php';
require_once THEME_ADMIN . '/theme-options/params/styling/backgrounds.php';
require_once THEME_ADMIN . '/theme-options/params/styling/header.php';
require_once THEME_ADMIN . '/theme-options/params/styling/main_navigation.php';
require_once THEME_ADMIN . '/theme-options/params/styling/header_toolbar.php';
require_once THEME_ADMIN . '/theme-options/params/styling/header_mobile.php';
require_once THEME_ADMIN . '/theme-options/params/styling/page_title.php';
require_once THEME_ADMIN . '/theme-options/params/styling/dashboard.php';
require_once THEME_ADMIN . '/theme-options/params/styling/fullscreen_nav.php';
require_once THEME_ADMIN . '/theme-options/params/styling/sidebar.php';
require_once THEME_ADMIN . '/theme-options/params/styling/footer.php';
require_once THEME_ADMIN . '/theme-options/params/styling/blog.php';

$options[] = array(
    "type" => "group",
    "id" => "mk_options_skining",
    "menu" => array(
        "mk_options_general_skin" => __("General Colors", "mk_framework") ,
        "mk_options_backgrounds_skin" => __("Backgrounds", "mk_framework") ,
        "mk_options_backgrounds_header" => __("Header", "mk_framework") ,
        "mk_options_main_navigation_skin" => __("Main Navigation", "mk_framework") ,
        "mk_options_header_toolbar_skin" => __("Header Toolbar", "mk_framework") ,
        "mk_options_header_mobile_skin" => __("Header Mobile", "mk_framework") ,
        "mk_options_header_banner_skin" => __("Page Title", "mk_framework") ,
        "mk_options_dashboard_skin" => __("Side Dashboard", "mk_framework") ,
        "mk_options_fullscreen_nav_skin" => __("Full Screen Navigation", "mk_framework") ,
        "mk_options_sidebar_skin" => __("Sidebar", "mk_framework") ,
        "mk_options_footer_skin" => __("Footer", "mk_framework"),
        "mk_options_blog_skin" => __("Blog", "mk_framework")
    ) ,
    "fields" => $styling_section
);

require_once THEME_ADMIN . '/theme-options/params/typography/fonts.php';
require_once THEME_ADMIN . '/theme-options/params/typography/general.php';
require_once THEME_ADMIN . '/theme-options/params/typography/main_navigation.php';
require_once THEME_ADMIN . '/theme-options/params/typography/page_title.php';
require_once THEME_ADMIN . '/theme-options/params/typography/side_dashboard.php';
require_once THEME_ADMIN . '/theme-options/params/typography/fullscreen_nav.php';
require_once THEME_ADMIN . '/theme-options/params/typography/sidebar.php';
require_once THEME_ADMIN . '/theme-options/params/typography/footer.php';
require_once THEME_ADMIN . '/theme-options/params/typography/blog.php';

$options[] = array(
    "type" => "group",
    "id" => "mk_options_typography",
    "menu" => array(
        "mk_options_fonts" => __("Fonts", "mk_framework") ,
        "mk_options_general_typography" => __("General Typography", "mk_framework") ,
        "mk_options_main_navigation_typography" => __("Main Navigation", "mk_framework") ,
        "mk_options_page_introduce_typography" => __("Page Title", "mk_framework") ,
        "mk_options_dashboard_typography" => __("Side Dashboard", "mk_framework") ,
        "mk_options_fullscreen_nav_typography" => __("Full Screen Navigation", "mk_framework") ,
        "mk_options_sidebar_typography" => __("Sidebar", "mk_framework") ,
        "mk_options_footer_typography" => __("Footer", "mk_framework"),
        "mk_options_blog_typography" => __("Blog", "mk_framework")
    ) ,
    "fields" => $typography_section
);

require_once THEME_ADMIN . '/theme-options/params/portfolio/single.php';
require_once THEME_ADMIN . '/theme-options/params/portfolio/archive.php';

$options[] = array(
    "type" => "group",
    "id" => "mk_options_portfolio",
    "menu" => array(
        "mk_options_portfolio_single" => __("Portfolio Single Post", "mk_framework") ,
        "mk_options_portfolio_archive" => __("Portfolio Archive", "mk_framework")
    ) ,
    "fields" => $portfolio_section
);

require_once THEME_ADMIN . '/theme-options/params/blog/single.php';
require_once THEME_ADMIN . '/theme-options/params/blog/archive.php';
require_once THEME_ADMIN . '/theme-options/params/blog/search.php';
require_once THEME_ADMIN . '/theme-options/params/blog/news.php';

$options[] = array(
    "type" => "group",
    "id" => "mk_options_blog",
    "menu" => array(
        "mk_options_blog_single_post" => __("Blog Single Post", "mk_framework") ,
        "mk_options_archive_posts" => __("Archive", "mk_framework") ,
        "mk_options_search_posts" => __("Search", "mk_framework") ,
        "mk_options_news_single" => __("News", "mk_framework")
    ) ,
    "fields" => $blog_section
);

require_once THEME_ADMIN . '/theme-options/params/ecommerce/general.php';
require_once THEME_ADMIN . '/theme-options/params/ecommerce/single.php';

$options[] = array(
    "type" => "group",
    "id" => "mk_options_woocommrce",
    "menu" => array(
        "mk_options_woo_general" => __("General Settings", "mk_framework") ,
        "mk_options_woo_single" => __("Single Product", "mk_framework") ,
    ) ,
    "fields" => $ecommerce_section
);

require_once THEME_ADMIN . '/theme-options/params/advanced/manage_theme.php';
require_once THEME_ADMIN . '/theme-options/params/advanced/twitter_api.php';
require_once THEME_ADMIN . '/theme-options/params/advanced/custom_css.php';
require_once THEME_ADMIN . '/theme-options/params/advanced/custom_js.php';
require_once THEME_ADMIN . '/theme-options/params/advanced/export.php';
require_once THEME_ADMIN . '/theme-options/params/advanced/import.php';

$options[] = array(
    "type" => "group",
    "id" => "mk_options_advanced",
    "menu" => array(
        "mk_options_manage_theme" => __("Manage Theme", "mk_framework") ,
        "mk_options_twitter_api" => __("Twitter API", "mk_framework") ,
        "mk_options_custom_js" => __("Custom JS", "mk_framework") ,
        "mk_options_custom_css" => __("Custom CSS", "mk_framework") ,
        "mk_options_export_options" => __("Export Theme Options", "mk_framework") ,
        "mk_options_import_options" => __("Import Theme Options", "mk_framework") ,
    ) ,
    "fields" => $advanced_section
);

/**
 * Filters settings in Theme Options.
 *
 * @since 5.5
 *
 * @param array $options
 */
$options = apply_filters( 'mk_jupiter_theme_options_settings', $options );

return array(
    'name' => THEME_OPTIONS,
    'options' => $options
);
