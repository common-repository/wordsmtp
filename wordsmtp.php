<?php
/**
 * Plugin Name: WordSMTP - Simple SMTP Solution  
 * Plugin URI:  http://softcoy.com/wordsmtp/
 * Description: WordSMTP Simple SMTP Solution mailer. Easy & simple setup. All your WordPress / WooCommerce sending emails will be used your verified domain name. Know the status of your all emails through mail report.
 * Version:     1.1.0
 * Author:      softcoy
 * Author URI:  https://softcoy.com/
 * License:     GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordsmtp-wordpress-simple-smtp
 * Domain Path: /languages
 */
 
 /*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
  
  Copyright 2023 softcoy.com
 */ 

  if ( ! defined( 'ABSPATH' ) ) {
	  exit; // Exit if accessed directly.
  }
  
  define( 'SCWORDSMTP_VERSION', '1.1.0' );
  define( 'SCWORDSMTP_MINIMUM_PHP_VERSION', '7.3' );
  define( 'SCWORDSMTP_MINIMUM_WP_VERSION', '5.5' );
  define( 'SCWORDSMTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );       

  function sc_wordsmtp_autoloader( $sc_class ) {	  	  
	  $classfile = get_include_path() . strtolower( str_replace( '_', '-' , $sc_class ) ).'.class.php';
	  if ( file_exists( $classfile ) ) {
		  require_once( $classfile );
	  }
  }  
  set_include_path( dirname(__FILE__) . '/includes/');    
  spl_autoload_register('sc_wordsmtp_autoloader');  
  new SC_Wordsmtp_Autoloader();
  register_activation_hook( __FILE__, array( 'SC_Wordsmtp','activate') );      



