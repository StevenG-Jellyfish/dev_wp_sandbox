<?php
wp_enqueue_style('control-panel-modal-plugin', THEME_CONTROL_PANEL_ASSETS . '/css/sweetalert.css');
wp_enqueue_script('control-panel-sweet-alert', THEME_CONTROL_PANEL_ASSETS . '/js/sweetalert.min.js', array('jquery'));
wp_enqueue_script('control-panel-addon-management', THEME_CONTROL_PANEL_ASSETS . '/js/addon-management.js', array('jquery'));
wp_localize_script( 'control-panel-addon-management', 'mk_cp_textdomain', mk_adminpanel_textdomain('addon-management'));
?>
<div class="control-panel-holder">
    <?php
        $mk_artbees_products = new mk_artbees_products();
        $compatibility = new Compatibility();
        echo mk_get_control_panel_view('header', true, array('page_slug' => 'theme-addons'));
    ?>
    <div class="abb-premium-addons">
        <div class="mk-addon-container">
            <div class="mk-addon-header">
                <h3 class="mk-addon-header-title"><?php _e( 'Installed Add-ons', 'mk_framework'); ?></h3>
            </div>
            <div class="mk-addon-tbl mk-installed-addons"></div>
        </div>

        <div class="mk-addon-container">
            <div class="mk-addon-header">
                <div class="mk-addon-action-elements">
                <h3 class="mk-addon-header-title mk-float-left"><?php _e( 'New Add-ons', 'mk_framework'); ?></h3>
                	<div class="mk-addon-category-holder mk-float-right">
                		<select name="mk-addon-category" class="mk-addon-category-select"></select>
                	</div>
                    <div class="mk-addon-search-holder mk-float-right">
                        <input type="text" name="mk-addon-search" class="mk-addon-search-txt" placeholder="Search for Add-ons ...">
                    </div>
                	<div class="spacer" style="clear: both;"></div>
                </div>
            </div>
            <!-- API Response addon -->
            <div class="mk-addon-tbl mk-api-addons-list"></div>
            <div class="mk-addon-load-more" data-from="0">
                <div class="mk-addon-load-more-btn"><?php _e( 'Load More', 'mk_framework'); ?></div>
                <div class="mk-addon-load-more-spinner"></div>
            </div>
        </div>
    </div>
</div>
