<?php
if ( ! defined( 'THEME_FRAMEWORK' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Helper functions for logic part of control panel
 *
 * @author         Reza Marandi <ross@artbees.net>
 * @copyright   Artbees LTD (c)
 * @link        http://artbees.net
 * @package     artbees
 */
class Abb_Logic_Helpers {

	/**
	 * method that is resposible to unzip compress files .
	 * it used native wordpress functions.
	 *
	 * @since       1.0.0
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str $zip_path compress file absolute path.
	 * @param str $dest_path Where should it be uncompressed.
	 *
	 * @return bool will return boolean status of action
	 */
	static function unZip( $zip_path, $dest_path ) {
		
		$zip_path  = realpath( $zip_path );
		$dest_path = realpath( $dest_path );

		if ( file_exists( $zip_path ) == false ) {
			throw new Exception( __( 'Zip file that you are looking for is not exist' , 'mk_framework' ) );
			return false;
		}
		if ( is_writable( $dest_path ) == false ) {
			throw new Exception( __( 'Destination path is not writable , Please resolve this issue first.' , 'mk_framework' ) );
			return false;
		}

		require_once ABSPATH . '/wp-admin/includes/file.php';
		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			WP_Filesystem();
			if ( ! $wp_filesystem ) {
				throw new Exception( __( 'Uncompress source file , System Error 100x001' , 'mk_framework' ) );
				return false;
			}
		}

		$unzipfile = unzip_file( $zip_path, $dest_path );
		if ( is_wp_error( $unzipfile ) ) {
			throw new Exception( $unzipfile->get_error_message(), 1 );
			return false;
		} else {
			return true;
		}
	}
	/**
	 * You can create a directory using this helper , it will check the dest directory for if its writable or not then
	 * try to create new one
	 *
	 * @since       1.0.0
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str $path path of directory that need to be created
	 * @param int $perm permission of new directory , default is : 0775
	 *
	 *     @return bool will return boolean status of action , all message is setted to $this->message()
	 */
	static function checkPermAndCreate( $path, $perm = 0775 ) {
		// $path = realpath($path);
		if ( file_exists( $path ) == true ) {
			if ( is_writable( $path ) == false ) {
				throw new Exception( sprintf( __( '%s directory is not writable', 'mk_framework' ) , $path ) );
				return false;

			} else {
				return true;
			}
		} else {
			if ( @mkdir( $path, $perm, true ) == false ) {
				throw new Exception( sprintf( __( 'Can\'t create %s directory', 'mk_framework' ) , $path ) );
				return false;
			} else {
				return true;
			}
		}
	}
	/**
	 * this method is resposible to download file from url and save it on server.
	 * it will check if curl is available or not and then decide to use curl or file_get_content
	 *
	 * @since       1.0.0
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param string $url url of file (http://yahoo.com/test-plugin.zip).
	 * @param string $file_name name of the fire that should be create at destination directory.
	 * @param string $dest_directory absolute path of directory that file save on it.
	 *
	 * @return bool will return action status
	 */
	static function uploadFromURL( $url, $file_name, $dest_directory ) {
		// $dest_directory = realpath($dest_directory);
		if ( self::RemoteURLHeaderCheck( $url ) === false ) {
			throw new Exception( __( 'Can\'t download source file.' , 'mk_framework' ) );
			return false;
		}

		set_time_limit( 0 );

		try {
			self::CheckPermAndCreate( $dest_directory );
		} catch (Exception $e) {
			throw new Exception( sprintf( __( 'Destination directory is not ready for upload . {%s}',  'mk_framework' ) , $dest_directory ) );
			return false;
		}

		if ( function_exists( 'curl_version' ) ) {
			$fp = @fopen( $dest_directory . $file_name, 'w+' );
			if ( $fp == false ) {
				throw new Exception( sprintf( __( "Can't open destination file {%s}", 'mk_framework' ) , $dest_directory . $file_name ) );
				return false;
			}

			$ch = curl_init( $url );

			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        	curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
        	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt( $ch, CURLOPT_FILE, $fp );

			$data = curl_exec( $ch );
			if ( curl_error( $ch ) ) {
				throw new Exception( curl_error( $ch ) );
				return false;
			} else {
				curl_close( $ch );
				fclose( $fp );
				return $dest_directory . $file_name;
			}
		} else {
			$response = @file_put_contents( $dest_directory . $file_name, file_get_contents( $url ) );
			if ( $response == false ) {
				throw new Exception( __( 'Can\'t download file using put contents , Contact webmaster.' , 'mk_framework' ) );
				return false;
			} else {
				return $dest_directory . $file_name;
			}
		}// End if().
	}
	/**
	 * this method is resposible to check a directory for see if its writebale or not
	 *
	 * @since       1.0.0
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str $path for example (/var/www/jupiter/wp-content/plugins)
	 *
	 * @return bool true or false
	 */
	static function writableOwnerCheck( $path ) {
		$is_writable = is_writable( $path );
		@file_put_contents( realpath( $path ) . '/testFile', 'test' );
		if ( file_exists( realpath( $path ) . '/testFile' ) == false ) {
			return false;
		}
		$created_file_owner = fileowner( realpath( $path ) . '/testFile' );
		unlink( preg_replace( '/([^:])(\/{2,})/', '$1/', $path . '/testFile' ) );
		$path_owner = fileowner( $path );
		if ( $is_writable == true && $created_file_owner == $path_owner ) {
			return true;
		}
		return false;
	}
	/**
	 * this method is resposible to delete a directory or file
	 * if the path is pointing to a directory it will remove all the includes file recursivly and then remove directory at last step
	 * if the path is pointing to a file it will remove it
	 *
	 * @since       1.0.0
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param str $dir for example (/var/www/jupiter/wp-content/plugins)
	 *
	 * @return bool true or false
	 */
	static function deleteFileNDir( $dir ) {
		if ( empty( $dir ) == true || strlen( $dir ) < 2 ) {
			return false;
		}

		$dir = realpath( $dir );
		if ( ! file_exists( $dir ) ) {
			return true;
		}

		if ( ! is_dir( $dir ) ) {
			return unlink( $dir );
		}
		foreach ( scandir( $dir ) as $item ) {
			if ( $item == '.' || $item == '..' ) {
				continue;
			}

			if ( ! self::deleteFileNDir( $dir . DIRECTORY_SEPARATOR . $item ) ) {
				return false;
			}
		}
		return rmdir( $dir );
	}
	/**
	 * Safely and securely get file from server.
	 * It attempts to read file using Wordpress native file read functions
	 * If it fails, we use wp_remote_get. if the site is ssl enabled, we try to convert it http as some servers may fail to get file
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param $file_url         string    its directory URL
	 * @param $file_dir         string    its directory Path
	 *
	 * @return $wp_file_body    string
	 */
	static function getFileBody( $file_url, $file_dir ) {
		$file_dir = realpath( $file_dir );

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}
		$wp_get_file_body = $wp_filesystem->get_contents( $file_dir );
		if ( $wp_get_file_body == false ) {
			$wp_remote_get_file = wp_remote_get( $file_uri );

			if ( is_array( $wp_remote_get_file ) and array_key_exists( 'body', $wp_remote_get_file ) ) {
				$wp_remote_get_file_body = $wp_remote_get_file['body'];

			} else if ( is_numeric( strpos( $file_uri, 'https://' ) ) ) {

				$file_uri           = str_replace( 'https://', 'http://', $file_uri );
				$wp_remote_get_file = wp_remote_get( $file_uri );

				if ( ! is_array( $wp_remote_get_file ) or ! array_key_exists( 'body', $wp_remote_get_file ) ) {
					throw new Exception( __( 'SSL connection error. Code: template-assets-get','mk_framework' ) );
					return false;
				}

				$wp_remote_get_file_body = $wp_remote_get_file['body'];
			}

			$wp_file_body = $wp_remote_get_file_body;

		} else {
			$wp_file_body = $wp_get_file_body;
		}
		return $wp_file_body;
	}
	/**
	 * It will check the header of URL and return boolean.
	 * False if header is 404
	 * True if header is something else
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param string $url string of url for checking
	 *
	 * @return boolean true if header is not 404
	 */
	static function remoteURLHeaderCheck( $url ) {
		if ( strpos( @get_headers( $url )[0] , '404 Not Found' ) == false ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * It will create a compress file from list of files
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param array   $files for example : array('preload-images/5.jpg','kwicks/ringo.gif','rod.jpg','reddit.gif');
	 * @param string  $destination name of the file or full address of destination for example : my-archive.zip
	 * @param boolean $overwrite if destionation exist , should it overwrite the compress file ?
	 *
	 * @return boolean true if completed and false if something goes wrong
	 */
	static function zip( $files = array(), $destination = '', $overwrite = false ) {
		// if the zip file already exists and overwrite is false, return false
		if ( file_exists( $destination ) && ! $overwrite ) {
			return false;
		}

		// vars
		$valid_files = array();

		// if files were passed in...
		if ( is_array( $files ) ) {
			// cycle through each file
			foreach ( $files as $file ) {
				// make sure the file exists
				if ( file_exists( $file ) ) {
					$valid_files[] = $file;
				}
			}
		}
		// if we have good files...
		if ( count( $valid_files ) ) {
			// create the archive
			$zip = new ZipArchive();
			if ( $zip->open( $destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE ) !== true ) {
				return false;
			}
			// add the files
			foreach ( $valid_files as $file ) {
				$zip->addFile( $file, $file );
			}

			// debug
			// echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			// close the zip -- done!
			$zip->close();

			// check to make sure the file exists
			return file_exists( $destination );
		} else {
			return false;
		}
	}
	static function search_multdim( $array, $key, $value ) {
		return (array_search( $value, array_column( $array, $key ) ));
	}
	/**
	 * It will check wether wordpress-importer plugin is exist in plugin directory or not.
	 * if exist it will return the wordpress importer file
	 * if not it will use jupiter version
	 *
	 * @author      Reza Marandi <ross@artbees.net>
	 * @copyright   Artbees LTD (c)
	 * @link        http://artbees.net
	 * @since       Version 5.5
	 */

	static function include_wordpress_importer() {

		if ( class_exists( 'WP_Import' ) === true ) {
			return true;
		}

		if ( is_plugin_active( 'wordpress-importer' ) ) {
			$plugins_data = get_plugins();
			$result = preg_grep( "/\bwordpress-importer\b/i", array_keys( $plugins_data ) );
			if ( is_array( $result ) && count( $result ) > 0 ) {
				$keys = array_keys( $plugins_data );
				include WP_PLUGIN_DIR . '/' . $keys[ key( $result ) ];
				return true;
			}
		}

		include THEME_CONTROL_PANEL . '/logic/wordpress-importer.php';
		return true;
	}
	/**
	 * It will return permission of directory
	 *
	 * @author Reza Marandi <ross@artbees.net>
	 *
	 * @param string $path Full path of directory
	 *
	 * @return int
	 */
	static function get_perm( $path ) {
		return substr( sprintf( '%o', fileperms( ABSPATH . $path ) ), -4 );
	}
}
