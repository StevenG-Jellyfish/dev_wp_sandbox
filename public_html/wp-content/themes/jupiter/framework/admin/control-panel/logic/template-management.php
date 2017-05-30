<?php
// error_reporting( E_ALL );
// ini_set( 'display_errors', 1 );

/**
 * This class is responsible manage all jupiter templates
 * it will communicate with artbees API and get list of templates , install them or remove them.
 *
 * @author       Reza Marandi <ross@artbees.net>
 * @copyright    Artbees LTD (c)
 *
 * @link         http://artbees.net
 * @since        5.4
 *
 * @version      1.0
 */

class mk_template_managememnt {

	private $layer_slider_slug = 'layerslider';
	private $logger;
	private $theme_name;

	public function setThemeName( $theme_name ) {
		$this->theme_name = $theme_name;
	}

	public function getThemeName() {
		return $this->theme_name;
	}

	private $api_url;

	public function setApiURL( $api_url ) {
		$this->api_url = $api_url;
	}

	public function getApiURL() {
		return $this->api_url;
	}

	private $template_name;

	public function setTemplateName( $template_name ) {
		$this->template_name = $template_name;
	}

	public function getTemplateName() {
		return strtolower( $this->template_name );
	}

	private $template_file_name;

	public function setTemplateFileName( $template_file_name ) {
		$this->template_file_name = $template_file_name;
	}

	public function getTemplateFileName() {
		return $this->template_file_name;
	}

	private $template_remote_address;

	public function setTemplateRemoteAddress( $template_remote_address ) {
		$this->template_remote_address = $template_remote_address;
	}

	public function getTemplateRemoteAddress() {
		return $this->template_remote_address;
	}

	private $template_content_file_name;

	public function setTemplateContentFileName( $template_content_file_name ) {
		$this->template_content_file_name = $template_content_file_name;
	}

	public function getTemplateContentFileName() {
		return $this->template_content_file_name;
	}

	private $widget_file_name;

	public function setWidgetFileName( $widget_file_name ) {
		$this->widget_file_name = $widget_file_name;
	}

	public function getWidgetFileName() {
		return $this->widget_file_name;
	}

	private $options_file_name;

	public function setOptionsFileName( $options_file_name ) {
		$this->options_file_name = $options_file_name;
	}

	public function getOptionsFileName() {
		return $this->options_file_name;
	}

	private $json_file_name;

	public function setJsonFileName( $json_file_name ) {
		$this->json_file_name = $json_file_name;
	}

	public function getJsonFileName() {
		return $this->json_file_name;
	}

	private $upload_dir;

	public function setUploadDir( $upload_dir ) {
		$this->upload_dir = $upload_dir;
	}

	public function getUploadDir() {
		return $this->upload_dir;
	}

	private $base_path;

	public function setBasePath( $base_path ) {
		$this->base_path = $base_path;
	}

	public function getBasePath() {
		return $this->base_path;
	}

	private $base_url;

	public function setBaseUrl( $base_url ) {
		$this->base_url = $base_url;
	}

	public function getBaseUrl() {
		return $this->base_url;
	}

	private $backup_path;

	public function setBackupPath( $backup_path ) {
		$this->backup_path = $backup_path;
	}

	public function getBackupPath() {
		return $this->backup_path;
	}

	private $backup_url;
	public function setBackupUrl( $backup_url ) {
		$this->backup_url = $backup_url;
	}
	public function getBackupUrl() {
		return $this->backup_url;
	}

	private $message;

	public function setMessage( $message ) {
		$this->message = $message;
	}

	public function getMessage() {
		return $this->message;
	}

	private $system_test_env;

	public function setSystemTestEnv( $system_test_env ) {
		$this->system_test_env = $system_test_env;
	}

	public function getSystemTestEnv() {
		return $this->system_test_env;
	}

	private $ajax_mode;

	public function setAjaxMode( $ajax_mode ) {
		$this->ajax_mode = $ajax_mode;
	}

	public function getAjaxMode() {
		return $this->ajax_mode;
	}
	/**
	 * Construct.
	 * it will add_actions if class created on ajax mode.
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param bool $system_text_env if you want to create an instance of this method for phpunit it should be true
	 * @param bool $ajax_mode       if you need this method as ajax mode set true
	 */
	public function __construct( $system_test_env = false, $ajax_mode = true ) {

		@ini_set( 'max_execution_time', 0 );
		@ini_set( 'memory_limit', '256M' );
		@set_time_limit( 0 );

		$this->setThemeName( 'Jupiter' );
		$this->setSystemTestEnv( $system_test_env );
		$this->setAjaxMode( $ajax_mode );
		
		// Init logger to system
		$this->logger = new Devbees\BeesLog\logger();

		// Set API Server URL
		$this->setApiURL( V2ARTBEESAPI );

		// Set API Calls template
		$template = \Httpful\Request::init()
		->method( \Httpful\Http::GET )
		->withoutStrictSsl()
		->expectsJson()
		->addHeaders(array(
			'api-key' => get_option( 'artbees_api_key' ),
	        'domain'  => $_SERVER['SERVER_NAME'],
		));
		\Httpful\Request::ini( $template );

		// Set Addresses
		$this->setUploadDir( wp_upload_dir() );
		$this->setBasePath( $this->getUploadDir()['basedir'] . '/mk_templates/' );
		$this->setBaseUrl( $this->getUploadDir()['baseurl'] . '/mk_templates/' );

		// Set Database Backup Address
		$this->setBackupPath( $this->getUploadDir()['basedir'] . '/mk_db_backup/' );
		$this->setBackupUrl( $this->getUploadDir()['baseurl'] . '/mk_db_backup/' );

		// Set File Names
		$this->setTemplateContentFileName( 'theme_content.xml' );
		$this->setWidgetFileName( 'widget_data.wie' );
		$this->setOptionsFileName( 'options.txt' );
		$this->setJsonFileName( 'package.json' );

		// Include WP_Importer
		global $wpdb;

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true );
		}

		// Add Actions
		if ( $this->getAjaxMode() == true ) {
			add_action( 'wp_ajax_abb_template_lazy_load', array( &$this, 'loadTemplatesFromApi' ) );
			add_action( 'wp_ajax_abb_install_template_procedure', array( &$this, 'installTemplateProcedure' ) );
			add_action( 'wp_ajax_abb_get_templates_categories', array( &$this, 'getTemplateCategoryListFromApi' ) );

			add_action( 'wp_ajax_abb_uninstall_template', array( &$this, 'uninstallTemplate' ) );
		}
	}
	// Begin Install Template Procedure
	public function installTemplateProcedure() {
		$template_name = (isset( $_POST['template_name'] ) ? $_POST['template_name'] : null);
		$import_media = (isset( $_POST['import_media'] ) ? $_POST['import_media'] : false);
		$type = (isset( $_POST['type'] ) ? $_POST['type'] : null);
		if ( is_null( $template_name ) || is_null( $type ) ) {
			$this->message( 'System problem at installing , please contact support', false );
			return false;
		}

		switch ( $type ) {
			case 'reset_db':
				$this->resetDB();
			break;
			case 'upload':
				$this->uploadTemplateToServer( $template_name );
			break;
			case 'unzip':
				$this->unzipTemplateInServer( $template_name );
			break;
			case 'validate':
				$this->validateTemplateFiles( $template_name );
			break;
			case 'plugin':
				$this->installRequiredPlugins( $template_name );
			break;
			case 'theme_content':
				$this->importThemeContent( $template_name , $import_media );
			break;
			case 'menu_locations':
				$this->importMenuLocations( $template_name );
			break;
			case 'setup_pages':
				$this->setUpPages( $template_name );
			break;
			case 'theme_options':
				$this->importThemeOptions( $template_name );
			break;
			case 'theme_widget':
				$this->importThemeWidgets( $template_name );
			break;
			case 'finilize':
				$this->finilizeImporting( $template_name );
			break;
		}
	}
	public function reinitializeData( $template_name ) {
		try {
			if ( empty( $template_name ) ) {
				throw new Exception( 'Choose template first' );
			}
			$this->setTemplateName( $template_name );
			if (
				file_exists( $this->getAssetsAddress( 'template_content_path', $this->getTemplateName() ) ) == false or
				file_exists( $this->getAssetsAddress( 'widget_path', $this->getTemplateName() ) ) == false or
				file_exists( $this->getAssetsAddress( 'options_path', $this->getTemplateName() ) ) == false or
				file_exists( $this->getAssetsAddress( 'json_path', $this->getTemplateName() ) ) == false
			) {
				throw new Exception( 'Template assets are not completely exist - p1, Contact support.' );
			} else {
				return true;
			}
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );
			return false;
		}
	}
	/**
	 * method that is resposible to pass plugin list to UI base on lazy load condition.
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str $_POST[from]  from number
	 * @param str $_POST[count] how many ?
	 *
	 * @return bool will return boolean status of action , all message is setted to $this->message()
	 */
	public function loadTemplatesFromApi() {
		try {
			$from = (isset( $_POST['from'] ) ? $_POST['from'] : null);
			$count = (isset( $_POST['count'] ) ? $_POST['count'] : null);
			$template_name = (isset( $_POST['template_name'] ) ? $_POST['template_name'] : null);
			$template_category = (isset( $_POST['template_category'] ) ? $_POST['template_category'] : null);
			if ( is_null( $from ) || is_null( $count ) ) {
				throw new Exception( 'System problem , please contact support', 1001 );
				return false;
			}
			$list_of_templates = $this->getTemplateListFromApi( [ 'pagination_start' => $from, 'pagination_count' => $count, 'template_name' => $template_name, 'template_category' => $template_category ] );
			$installed = get_option( 'jupiter_template_installed' );
			foreach ( $list_of_templates as $key => $template_data ) {
				if ( $template_data->slug === $installed ) {
					$list_of_templates[ $key ]->installed = true;
				} else {
					$list_of_templates[ $key ]->installed = false;
				}
			}
			if ( is_array( $list_of_templates ) ) {
				$this->message( 'Successfull', true, $list_of_templates );
				return true;
			} else {
				throw new Exception( 'Template list is not what we expected' );
			}
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );
			return false;
		}
	}
	public function resetDB() {
		try {
			$tables = array(
				'comments',
				'commentmeta',
				'links',
				'options',
				'postmeta',
				'posts',
				'term_relationships',
				'termmeta',
				'terms',
				'term_taxonomy',
			);
			$this->resetWordpressDatabase( $tables, array(), true );
			$this->message( 'Database reseted', true );

			return true;
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );

			return false;
		}
	}
	public function uploadTemplateToServer( $template_name ) {
		try {
			$this->setTemplateName( $template_name );
			$getTemplateName = $this->getTemplateName();
			if ( empty( $getTemplateName ) ) {
				throw new Exception( 'Choose one template first' );
			}
			$url = $this->getTemplateDownloadLink( $this->getTemplateName() , 'download' );
			$template_file_name = $this->getTemplateDownloadLink( $this->getTemplateName() , 'filename' );
			$this->setTemplateRemoteAddress( $url );
			if ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
				throw new Exception( 'Template source URL is not validate' );
			}
			Abb_Logic_Helpers::uploadFromURL( $this->getTemplateRemoteAddress(), $template_file_name ,$this->getBasePath() );
			$this->message( 'Uploaded to server', true );
			return true;
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );
			return false;
		}
	}
	public function unzipTemplateInServer( $template_name ) {
		try {
			$this->setTemplateName( $template_name );
			$getTemplateName = $this->getTemplateName();
			if ( empty( $getTemplateName ) ) {
				throw new Exception( 'Choose one template first' );
			}

			$response = $this->getTemplateDownloadLink( $this->getTemplateName() , 'filename' );
			$this->setTemplateFileName( $response );

			Abb_Logic_Helpers::unZip( $this->getBasePath() . $this->getTemplateFileName(), $this->getBasePath() );
			if ( Abb_Logic_Helpers::deleteFileNDir( $this->getBasePath() . $this->getTemplateFileName() ) == false ) {
				throw new Exception( 'Cannot delete template zip file' );
			}

			$this->message( 'Compeleted', true );

			return true;
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );

			return false;
		}
	}
	public function validateTemplateFiles( $template_name ) {
		try {
			if ( empty( $template_name ) ) {
				throw new Exception( 'Choose template first' );
			}
			$this->setTemplateName( $template_name );
			if (
				file_exists( $this->getAssetsAddress( 'template_content_path', $this->getTemplateName() ) ) == false ||
				file_exists( $this->getAssetsAddress( 'widget_path', $this->getTemplateName() ) ) == false ||
				file_exists( $this->getAssetsAddress( 'options_path', $this->getTemplateName() ) ) == false ||
				file_exists( $this->getAssetsAddress( 'json_path', $this->getTemplateName() ) ) == false
			) {
				throw new Exception( 'Template assets are not completely exist - p2, Contact support.' );
			} else {
				$this->message( 'Compeleted', true );
				return true;
			}
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );

			return false;
		}
	}
	public function installRequiredPlugins( $template_name ) {
		$this->reinitializeData( $template_name );
		try {
			$json_url = $this->getAssetsAddress( 'json_url', $this->getTemplateName() );
			$json_path = $this->getAssetsAddress( 'json_path', $this->getTemplateName() );
			$response = Abb_Logic_Helpers::getFileBody( $json_url, $json_path );
			$plugins = json_decode( $response, true );

			if ( is_array( $plugins['required_plugins'] ) == false || count( $plugins['required_plugins'] ) <= 0 ) {
				throw new Exception( 'Plugin set have wrong structure' );
			}
			$mk_plugin_management = new mk_plugin_management( false, false );
			$pm_response = $mk_plugin_management->install_batch( $plugins['required_plugins'] );
			if ( $pm_response == false ) {
				$pm_response = $mk_plugin_management->get_response_message();
				throw new Exception( $pm_response );
			}

			$message = '{param} plugins are installed.' ;
			$this->message( array( $message, count( $plugins['required_plugins'] ) ), true );
			return true;
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );
			return false;
		}
	}
	public function importThemeContent( $template_name, $fetch_attachments = false ) {
		try {

			// Include wordpress-importer class
			Abb_Logic_Helpers::include_wordpress_importer();

			$this->reinitializeData( $template_name );

			$importer = new WP_Import();
			$importer->fetch_attachments = filter_var( $fetch_attachments, FILTER_VALIDATE_BOOLEAN );

			ob_start();
			$importer->import( $this->getAssetsAddress( 'template_content_path', $this->getTemplateName() ) );
			ob_end_clean();

			$this->message( 'Template contents were imported.', true );
			return true;

		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );
			return false;
		}
	}
	public function importMenuLocations( $template_name ) {
		try {
			$locations = get_theme_mod( 'nav_menu_locations' );
			$menus = wp_get_nav_menus();
			if ( $menus ) {
				foreach ( $menus as $menu ) {
					if (
						$menu->name == 'Main Navigation' ||
						$menu->name == 'Main' ||
						$menu->name == 'Main Menu' ||
						$menu->name == 'main'
					) {
						$locations['primary-menu'] = $menu->term_id;
					}
				}
			}
			set_theme_mod( 'nav_menu_locations', $locations );
			$this->message( 'Navigation locations is configured.', true );

			return true;
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );

			return false;
		}
	}
	public function setUpPages( $template_name ) {
		try {
			$homepage = get_page_by_title( 'Homepage 1' );
			if ( empty( $homepage->ID ) ) {
				$homepage = get_page_by_title( 'Homepage' );
				if ( empty( $homepage->ID ) ) {
					$homepage = get_page_by_title( 'Home' );
				}
			}

			if ( ! empty( $homepage->ID ) ) {
				update_option( 'page_on_front', $homepage->ID );
				update_option( 'show_on_front', 'page' );
				$home_page_response = true;
			}

			$shop_page = get_page_by_title( 'Shop' );
			if ( ! empty( $shop_page->ID ) ) {
				update_option( 'woocommerce_shop_page_id', $shop_page->ID );
				$shop_page_response = true;
			}
			// 'Default homepage is configured.'; 'Shop Page is configured.';
			if ( isset( $home_page_response ) and isset( $shop_page_response ) ) {
				$response = 'Default homepage and Shop Page is configured.';
			} elseif ( ! isset( $home_page_response ) and isset( $shop_page_response ) ) {
				$response = 'Shop Page is configured.';
			} elseif ( isset( $home_page_response ) and ! isset( $shop_page_response ) ) {
				$response = 'Default homepage is configured.';
			} else {
				$response = 'Setup pages completed.';
			}
			$this->message( $response, true );

			return true;
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );

			return false;
		}// End try().
	}
	public function importThemeOptions( $template_name ) {
		try {
			$this->reinitializeData( $template_name );
			$import_data = Abb_Logic_Helpers::getFileBody(
				$this->getAssetsAddress( 'options_url', $this->getTemplateName() ),
				$this->getAssetsAddress( 'options_path', $this->getTemplateName() )
			);
			$data = unserialize( base64_decode( $import_data ) );
			if ( empty( $data ) == false ) {
				unset( $data['custom_js'], $data['twitter_consumer_key'], $data['google_maps_api_key'], $data['twitter_consumer_secret'], $data['twitter_access_token'], $data['twitter_access_token_secret'], $data['typekit_id'], $data['analytics'] );
				update_option( THEME_OPTIONS, $data );

				$this->message( 'Theme options are imported.', true );
				return true;
			} else {
				throw new Exception( 'Template options is empty' );
				return false;
			}
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );

			return false;
		}
	}
	public function importThemeWidgets( $template_name ) {
		$this->reinitializeData( $template_name );
		try {
			$data = Abb_Logic_Helpers::getFileBody(
				$this->getAssetsAddress( 'widget_url', $this->getTemplateName() ),
				$this->getAssetsAddress( 'widget_path', $this->getTemplateName() )
			);
			$data = json_decode( $data );
			$this->importWidgetData( $data );

			$this->message( 'Widgets are imported.', true );

			return true;
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );

			return false;
		}
	}
	public function finilizeImporting( $template_name ) {
		$this->reinitializeData( $template_name );
		// Check if it had something to import
		try {
			$json_url = $this->getAssetsAddress( 'json_url', $this->getTemplateName() );
			$json_path = $this->getAssetsAddress( 'json_path', $this->getTemplateName() );
			$response = Abb_Logic_Helpers::getFileBody( $json_url, $json_path );
			$plugins = json_decode( $response, true );
			if (
				empty( $plugins['importing_data'] ) == false &&
				is_array( $plugins['importing_data'] ) &&
				count( $plugins['importing_data'] ) > 0 ) {
				foreach ( $plugins['importing_data'] as $key => $value ) {
					switch ( $value['name'] ) {
						case 'layer-slider':
							$ls_content_path = $this->getBasePath() . strtolower( $template_name ) . '/' . $value['content_path'];
							if ( file_exists( $ls_content_path ) ) {
								$this->importLayerSliderContent( $ls_content_path );
							}
						break;
					}
				}
			}

			// Deleting Template Source
			if ( file_exists( $this->getBasePath() . $template_name ) ) {
				$delete_response = Abb_Logic_Helpers::deleteFileNDir( $this->getBasePath() . $template_name );
			}
			if ( file_exists( $this->getBasePath() . $template_name . '.zip' ) ) {
				$delete_response = Abb_Logic_Helpers::deleteFileNDir( $this->getBasePath() . $template_name . '.zip' );
			}
			if ( $delete_response = false ) {
				throw new Exception( 'Can not remove source files' );
				return false;
			}

			update_option( 'jupiter_template_installed', $this->getTemplateName() );
			update_option( THEME_OPTIONS . '_imported', 'true' );

			$this->updateThemeOptions();

			$this->message( 'Data imported successfully', true );
			return true;

		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );

			return false;
		}// End try().
	}
	public function uninstallTemplate() {
		$this->resetDB();
	}
	// End    Install Template Procedure
	public function availableWidgets() {
		global $wp_registered_widget_controls;
		$widget_controls = $wp_registered_widget_controls;
		$available_widgets = array();
		foreach ( $widget_controls as $widget ) {
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
				$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
				$available_widgets[ $widget['id_base'] ]['name'] = $widget['name'];
			}
		}

		return apply_filters( 'available_widgets', $available_widgets );
	}
	private function importWidgetData( $data ) {
		global $wp_registered_sidebars;

		$available_widgets = $this->availableWidgets();
		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
		}
		if ( empty( $data ) || ! is_object( $data ) ) {
			throw new Exception( 'Widget data could not be read. Please try a different file.' );
		}
		$results = array();
		foreach ( $data as $sidebar_id => $widgets ) {
			if ( 'wp_inactive_widgets' == $sidebar_id ) {
				continue;
			}
			if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
				$sidebar_available = true;
				$use_sidebar_id = $sidebar_id;
				$sidebar_message_type = 'success';
				$sidebar_message = '';
			} else {
				$sidebar_available = false;
				$use_sidebar_id = 'wp_inactive_widgets';
				$sidebar_message_type = 'error';
				$sidebar_message = 'Sidebar does not exist in theme (using Inactive)';
			}
			$results[ $sidebar_id ]['name'] = ! empty( $wp_registered_sidebars[ $sidebar_id ]['name'] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : $sidebar_id;
			$results[ $sidebar_id ]['message_type'] = $sidebar_message_type;
			$results[ $sidebar_id ]['message'] = $sidebar_message;
			$results[ $sidebar_id ]['widgets'] = array();
			foreach ( $widgets as $widget_instance_id => $widget ) {
				$fail = false;
				$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
				$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );
				if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
					$fail = true;
					$widget_message_type = 'error';
					$widget_message = 'Site does not support widget';
				}
				$widget = apply_filters( 'mk_widget_settings', $widget );
				if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$sidebar_widgets = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array();
					$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
					foreach ( $single_widget_instances as $check_id => $check_widget ) {
						if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
							$fail = true;
							$widget_message_type = 'warning';
							$widget_message = 'Widget already exists';
							break;
						}
					}
				}
				if ( ! $fail ) {
					$single_widget_instances = get_option( 'widget_' . $id_base );
					$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array(
						'_multiwidget' => 1,
					);
					$single_widget_instances[] = (array) $widget;
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number = 1;
						$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}
					update_option( 'widget_' . $id_base, $single_widget_instances );
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$new_instance_id = $id_base . '-' . $new_instance_id_number;
					$sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id;
					update_option( 'sidebars_widgets', $sidebars_widgets );
					if ( $sidebar_available ) {
						$widget_message_type = 'success';
						$widget_message = 'Imported';
					} else {
						$widget_message_type = 'warning';
						$widget_message = 'Imported to Inactive';
					}
				}
				$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['name'] = isset( $available_widgets[ $id_base ]['name'] ) ? $available_widgets[ $id_base ]['name'] : $id_base;
				$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['title'] = $widget->title ? $widget->title : 'No Title';
				$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['message_type'] = $widget_message_type;
				$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['message'] = $widget_message;
			}// End foreach().
		}// End foreach().

		return true;
	}
	/**
	 * It will empty all or custom database tables of wordpress and install wordpress again if needed.
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param array $table          which table need to be empty ? example : array('user' , 'usermeta')
	 *                              table names should be without any prefix
	 * @param bool  $install_needed if wordpress need to be installed after reseting database
	 *                              it should be false or true
	 *
	 * @return bool return if everything looks good and throwing errors on problems
	 */
	public function resetWordpressDatabase( $tables = array(), $exclude_tables = array(), $install_needed = false ) {
		global $wpdb, $reactivate_wp_reset_additional, $current_user;

		// If the process need to install wordpress after removing data
		// It will store some information for later
		if ( $install_needed == true ) {
			require_once ABSPATH . '/wp-admin/includes/upgrade.php';
			
			$blogname = get_option( 'blogname' );
			$admin_email = get_option( 'admin_email' );
			$blog_public = get_option( 'blog_public' );
			$allow_jupiter_tracking = get_option( 'jupiter-data-tracking' );
			$artbees_api_key = get_option( 'artbees_api_key' );
			$site_url = site_url();
			$current_theme_name = wp_get_theme();

			if ( $current_user->user_login != 'admin' ) {
				$user = get_user_by( 'login', 'admin' );
			}

			if ( empty( $user->user_level ) || $user->user_level < 10 ) {
				$user = $current_user;
				$session_tokens = get_user_meta( $user->ID, 'session_tokens', true );
			}
		}

		// Check if we need all the tables or specific table
		if ( is_array( $tables ) && count( $tables ) > 0 ) {
			array_walk($tables, function ( &$value, $key ) use ( $wpdb ) {
				$value = $wpdb->prefix . $value;
			});
		} else {
			$prefix = str_replace( '_', '\_', $wpdb->prefix );
			$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" );
		}

		// exclude table if its valued
		if ( is_array( $exclude_tables ) && count( $exclude_tables ) > 0 ) {
			array_walk($exclude_tables, function ( &$ex_value, $key ) use ( $wpdb ) {
				$ex_value = $wpdb->prefix . $ex_value;
			});
			$tables = array_diff( $tables, $exclude_tables );
		}
		// Removing data from wordpress tables.
		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE $table" );
		}
		// Install Wordpress from base data
		if ( $install_needed == true ) {
			$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public );
			switch_theme( strtolower( $current_theme_name->get( 'Name' ) ) );

			/* GoDaddy Patch => GD have a problem of cleaning siteurl option value after reseting database */
			if(site_url() == '') {
				$this->logger->debug('Fresh Installed WP' , 'Sounds like GDServers : ' . $site_url);
				$wpdb->update( $wpdb->options, array( 'option_value' => $site_url),array('option_name'=>'siteurl'));
				$this->logger->debug('Update Query' , $wpdb->last_query);
				$this->logger->debug('After Executing GD Patch' , site_url());
			}
			extract( $result, EXTR_SKIP );

			$query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user_id );
			$wpdb->query( $query );

			$get_user_meta = function_exists( 'get_user_meta' ) ? 'get_user_meta' : 'get_usermeta';
			$update_user_meta = function_exists( 'update_user_meta' ) ? 'update_user_meta' : 'update_usermeta';

			if ( $get_user_meta($user_id, 'default_password_nag') ) {
				$update_user_meta($user_id, 'default_password_nag', false);
			}

			if ( $get_user_meta($user_id, $wpdb->prefix . 'default_password_nag') ) {
				$update_user_meta($user_id, $wpdb->prefix . 'default_password_nag', false);
			}

			// if ( ! empty( $reactivate_wp_reset_additional ) ) {
			// foreach ( $reactivate_wp_reset_additional as $plugin ) {
			// $plugin = plugin_basename( $plugin );
			// if ( ! is_wp_error( validate_plugin( $plugin ) ) ) {
			// activate_plugin( $plugin );
			// } else {
			// throw new Exception( $plugin->get_error_message() );
			// }
			// }
			// }
			wp_clear_auth_cookie();
			wp_set_current_user( $user_id, $user->user_login );
			if ( $session_tokens ) {
				delete_user_meta( $user->ID, 'session_tokens' );
				update_user_meta( $user->ID, 'session_tokens', $session_tokens );
			}

			wp_set_auth_cookie( $user_id, true );
			do_action( 'wp_login', $user->user_login );

			// Set Jupiter tracking option as before
			if ( $allow_jupiter_tracking == true ) {
				update_option( 'jupiter-data-tracking', $allow_jupiter_tracking );
			}

			update_option( 'artbees_api_key', $artbees_api_key );

			return true;
		} else {
			return true;
		}// End if().
	}
	/**
	 * This method is resposible to get template list from api and create download link if template need to extract from wordpress repo.
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str $template_name if template name is valued it will return array of information about the this template
	 *                           but if template is valued as false it will return all templates information
	 *
	 * @return array will return array of templates
	 */
	public function getTemplateListFromApi( $configs = array() ) {

		$url = $this->getApiURL() . 'theme/templates';
		$response = \Httpful\Request::get( $url )
		->addHeaders(array(
				'theme-name' => $this->getThemeName(),
				'pagination-start' => isset( $configs['pagination_start'] ) ? $configs['pagination_start'] : 0,
				'pagination-count' => isset( $configs['pagination_count'] ) ? $configs['pagination_count'] : 1,
		));
		if ( isset( $configs['template_name'] ) && is_null( $configs['template_name'] ) == false ) {
			$response->addHeaders(array(
				'template-name' => $configs['template_name'],
			));
		}
		if ( isset( $configs['template_category'] ) && is_null( $configs['template_category'] ) == false ) {
			$response->addHeaders(array(
				'template-category' => $configs['template_category'],
			));
		}
		$response = $response->send();
		if ( isset( $response->body->bool ) == false || $response->body->bool == false ) {
			throw new Exception( $response->body->sys_msg );
		}
		return $response->body->data;
	}
	public function getTemplateDownloadLink( $template_name = '', $type = 'download' ) {
		$url = $this->getApiURL() . 'theme/download-template';
		$response = \Httpful\Request::get( $url )
		->addHeaders(array(
				'template-name' => $template_name,
				'type' => $type,
		))
		->send();
		if ( isset( $response->body->bool ) == false || $response->body->bool == false ) {
			throw new Exception( $response->body->user_msg );
		}
		return $response->body->data;
	}
	/**
	 * This method is resposible to get templates categories list from api
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str $template_name if template name is valued it will return array of information about the this template
	 *                           but if template is valued as false it will return all templates information
	 *
	 * @return array will return array of plugins
	 */
	public function getTemplateCategoryListFromApi() {
		try {
			$url = $this->getApiURL() . 'theme/template-categories';
			$response = \Httpful\Request::get( $url )->send();
			if ( isset( $response->body->bool ) == false || $response->body->bool == false ) {
				throw new Exception( $response->body->user_msg );
			}
			$this->message( 'Successfull', true, $response->body->data );
			return true;
		} catch (Exception $e) {
			$this->message( $e->getMessage(), false );
			return false;
		}
	}
	/**
	 * we need to make assets addresses dynamic and fully proccess
	 * in one method for future development
	 * it will get the type of address and will return full address in string
	 * example :
	 * for (options_url) type , it will return something like this
	 * (http://localhost/jupiter/wp-content/uploads/mk_templates/dia/options.txt).
	 *
	 * for (options_path) type , it will return something like this
	 * (/usr/apache/www/wp-content/uploads/mk_templates/dia/options.txt)
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str $which_one     Which address do you need ?
	 * @param str $template_name such as :
	 */
	public function getAssetsAddress( $which_one, $template_name ) {
		$template_name = sanitize_title( $template_name );
		switch ( $which_one ) {
			case 'template_content_url':
			return $this->getBaseUrl() . $template_name . '/' . $this->getTemplateContentFileName();
			break;
			case 'template_content_path':
			return $this->getBasePath() . $template_name . '/' . $this->getTemplateContentFileName();
			break;
			case 'widget_url':
			return $this->getBaseUrl() . $template_name . '/' . $this->getWidgetFileName();
			break;
			case 'widget_path':
			return $this->getBasePath() . $template_name . '/' . $this->getWidgetFileName();
			break;
			case 'options_url':
			return $this->getBaseUrl() . $template_name . '/' . $this->getOptionsFileName();
			break;
			case 'options_path':
			return $this->getBasePath() . $template_name . '/' . $this->getOptionsFileName();
			break;
			case 'json_url':
			return $this->getBaseUrl() . $template_name . '/' . $this->getJsonFileName();
			break;
			case 'json_path':
			return $this->getBasePath() . $template_name . '/' . $this->getJsonFileName();
			break;
			default:
			throw new Exception( 'File name you are looking for is not introduced.' );

			return false;
			break;
		}
	}
	public function importLayerSliderContent( $content_path ) {
		global $wpdb;
		$plugin = new mk_plugin_management();
		$ls_path = $plugin->find_plugin_path( $this->layer_slider_slug );

		if ( $ls_path == false ) {
			throw new Exception( 'LayerSlider is not installed , install it first' );
			return false;
		}
		if ( $plugin->check_active_plugin( $this->layer_slider_slug ) == false ) {
			throw new Exception( 'LayerSlider is installed but not activated , activate it first' );
			return false;
		}
		// Empty layerslider table first
		$table = $wpdb->prefix . 'layerslider';
		$wpdb->query( "TRUNCATE TABLE $table" );

		// Try to import configs.
		$ls_plugin_root_path = pathinfo( $plugin->get_plugins_dir() . $ls_path );
		include $ls_plugin_root_path['dirname'] . '/classes/class.ls.importutil.php';
		$import = new LS_ImportUtil( $content_path );
		return true;
	}
	/*====================== Helpers ============================*/
	/**
	 * this method is resposible to manage all the classes messages and act different on ajax mode or test mode.
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str   $message for example ("Successfull")
	 * @param bool  $status  true or false
	 * @param mixed $data    its for when ever you want to result back an array of data or anything else
	 */
	public function message( $message, $status, $data = null ) {
		$response = array(
				'status' => $status,
				'message' => mk_logic_message_helper( 'template-management' , $message ),
				'data' => $data,
			);
		if ( $this->getAjaxMode() == true ) {
			header( 'Content-Type: application/json' );
			wp_die( json_encode( $response ) );
		} else {
			$this->setMessage( $response );
		}
	}

	/**
	 * @author Bob Ulusoy
	 */
	public function updateThemeOptions() {
		$theme_options = array();
			$page = include( THEME_ADMIN . '/theme-options/masterkey.php' );
			$theme_options[ $page['name'] ] = array();

		foreach ( $page['options'] as $group ) {

			foreach ( $group['fields'] as $subgroup ) {
				foreach ( $subgroup['fields'] as $option ) {
					if ( $option['type'] == 'groupset' ) {
						foreach ( $option['fields'] as $option ) {
							if ( isset( $option['default'] ) ) {
								$theme_options[ $page['name'] ][ $option['id'] ] = $option['default'];
							}
						}
					} else {
						if ( isset( $option['default'] ) ) {
							$theme_options[ $page['name'] ][ $option['id'] ] = $option['default'];
						}
					}
				}
			}
		}

		$theme_options[ $page['name'] ] = array_merge( (array) $theme_options[ $page['name'] ], (array) get_option( $page['name'] ) );

		$GLOBALS['mk_options'] = $theme_options[ $page['name'] ];
		update_option( THEME_OPTIONS, $theme_options[ $page['name'] ] );
		update_option( THEME_OPTIONS_BUILD, uniqid() );
		mk_purge_cache_actions();
	}
}
/* Disable woocommerce redirection */
add_action( 'admin_init', 'disable_woocommerce', 5 );
function disable_woocommerce() {
	delete_transient( '_wc_activation_redirect' );
	add_filter( 'woocommerce_prevent_automatic_wizard_redirect', function(){return true;
	} );
}
global $abb_phpunit;
if ( empty( $abb_phpunit ) || $abb_phpunit == false ) {
	new mk_template_managememnt();
}
