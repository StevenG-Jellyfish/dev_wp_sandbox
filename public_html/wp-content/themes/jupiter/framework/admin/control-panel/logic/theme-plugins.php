<?php
wp_enqueue_style('control-panel-modal-plugin', THEME_CONTROL_PANEL_ASSETS . '/css/sweetalert.css');
wp_enqueue_script('control-panel-sweet-alert', THEME_CONTROL_PANEL_ASSETS . '/js/sweetalert.min.js', array('jquery'));
wp_enqueue_script('control-panel-plugin-management', THEME_CONTROL_PANEL_ASSETS . '/js/plugin-management.js', array('jquery'));
wp_localize_script( 'control-panel-plugin-management', 'mk_cp_textdomain', mk_adminpanel_textdomain('plugin-management'));
?>
 <div class="control-panel-holder">
 	<?php
        $mk_artbees_products = new mk_artbees_products();
        $compatibility = new Compatibility();
        echo mk_get_control_panel_view('header', true, array('page_slug' => 'theme-plugins'));
    ?>
	<div class="abb-premium-plugins">
	<?php
            if($compatibility->checkErrorExistence() == false)
            {
            ?>
                <div class="mk-plugin-container">
                    <div class="mk-plugin-header">
                        <h3 class="mk-plugin-header-title"><?php _e( 'Installed Plugins', 'mk_framework'); ?></h3>
                    </div>
                    <div class="mk-plugin-tbl mk-installed-plugins"></div>
                </div>

                <div class="mk-plugin-container">
                    <div class="mk-plugin-header">
                        <div class="mk-plugin-action-elements">
                        <h3 class="mk-plugin-header-title mk-float-left"><?php _e( 'New Plugins', 'mk_framework'); ?></h3>
                            <div class="spacer" style="clear: both;"></div>
                        </div>
                    </div>
                    <!-- API Response plugin -->
                    <div class="mk-plugin-tbl mk-api-plugins-list"></div>
                    <div class="mk-plugin-load-more" data-from="0">
                        <div class="mk-plugin-load-more-btn"><?php _e( 'Load More', 'mk_framework'); ?></div>
                        <div class="mk-plugin-load-more-spinner"></div>
                    </div>
                </div>
			<?php
            }
            else
            {
                echo mk_get_control_panel_view('register-product-popup', true, array('message' => __('In order to install new plugins you must resolve compatibility issues first.' , 'mk_framework')));
            }
    ?>
	</div>
</div>
