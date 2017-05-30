<?php
wp_enqueue_style('control-panel-modal-plugin', THEME_CONTROL_PANEL_ASSETS . '/css/sweetalert.css');
wp_enqueue_script('control-panel-sweet-alert', THEME_CONTROL_PANEL_ASSETS . '/js/sweetalert.min.js', array('jquery'));
wp_enqueue_script('control-panel-template-management', THEME_CONTROL_PANEL_ASSETS . '/js/template-management.js', array('jquery' , 'wp-util'));
wp_localize_script( 'control-panel-template-management', 'mk_cp_textdomain', mk_adminpanel_textdomain('template-management'));
?>
<div class="control-panel-holder">
    <?php
        $mk_artbees_products = new mk_artbees_products();
        $compatibility = new Compatibility();
        echo mk_get_control_panel_view('header', true, array('page_slug' => 'theme-templates'));
        $error_flag = false;
    ?>
    <div class="abb-premium-templates cp-pane">
        <?php
        if ( $mk_artbees_products->is_api_key_exists() === false ) {
            echo mk_get_control_panel_view('register-product-popup', true, array('message' => sprintf(__('In order to install new templates you must register theme. %s' , 'mk_framework') , '<br><a target="_blank" href="https://artbees.net/themes/docs/how-to-register-theme/">Learn how to register</a>')));
            $error_flag = true;
        } 
        if ( $compatibility->checkErrorExistence() === true ) {
            echo mk_get_control_panel_view('register-product-popup', true, array('message' => sprintf(__('In order to install new templates you must resolve compatibility issues first. %s' , 'mk_framework') , '<br><a target="_blank" href="https://artbees.net/themes/docs/server-requirements-jupiter/">Learn more</a>')));
            $error_flag = true;
        }
        if (
            get_filesystem_method() !== 'direct' || (isset($GLOBALS['_wp_filesystem_direct_method']) && $GLOBALS['_wp_filesystem_direct_method'] != 'file_owner')) {
            echo mk_get_control_panel_view('register-product-popup', true, array('message' => sprintf(__('Whoops! There seems to be an inconsistency in your server. Don’t panic though, <br> Here we’ve listed some possible solutions for you to get back on track. %s' , 'mk_framework') , ' <a target="_blank" href="https://artbees.net/themes/docs/installing-template/">Learn more</a>')));
            $error_flag = true;
        }
        if ( $error_flag ) {
            ?>
            </div>
        </div>
        <?php
            } else {
        ?>
                <div class="mk-templates clearfix">
                    <div class="mk-installed-template">
                        <div class="mk-installed-template-header clearfix">
                            <h3 class="mk-installed-template-title"><?php _e( 'Installed Template', 'mk_framework' ); ?></h3>
                        </div>
                        <div class="mk-installed-template-list clearfix" id="installed-template-list"></div>
                    </div>
                    <div class="mk-templates-header clearfix">
                        <h3 class="mk-templates-title"><?php _e( 'New Templates', 'mk_framework' ); ?></h3>
                        <div class="mk-templates-categories-holder">
                            <select class="mk-templates-categories"></select>
                        </div>
                        <div class="mk-search-template-holder">
                            <input type="text" name="mk_seach_template" class="mk-search-template" placeholder="<?php _e('Search by name', 'mk_framework') ?>">
                        </div>
                    </div>
                    <div class="mk-template-list clearfix" id="template-list"></div>
                </div>
                <div class="abb-template-page-load-more" data-from="0"></div>
            </div>
        </div>
        <?php
            }
        ?>
