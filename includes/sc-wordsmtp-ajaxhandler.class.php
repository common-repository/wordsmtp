<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class SC_Wordsmtp_Ajaxhandler {
	
	private static $initiated             	  = false;
	
	public static $smtphost					  = null;
	public static $smtpuser					  = null;
	public static $smtppassword				  = null;
	public static $from_email				  = null;
	
	public static $smtp_recipient			  = '';
	public static $smtp_encryption			  = 'tls';	
	public static $smtpport	                  = 587;		
	public static $smtp_from_name			  = null;
	public static $email_subject			  = null;
	public static $email_body				  = null;
	
	public static $email_delivered			  = false;
	public static $email_errorinfo			  = null;	
	public static $smtp_debug_status		  = false;	
	
	public static $output					  = [];	
				
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}								
	}
	
	/**
	 * Initiate hooks
	 * @since 1.0.0
	 */
	private static function initiate_hooks() {		
		  add_action('admin_enqueue_scripts', array( __CLASS__, 'admin_required_scripts') );	
		  add_action('wp_ajax_sc_wordsmtp_test_email', array( __CLASS__, 'sc_wordsmtp_test_email') );	
		  add_action('phpmailer_init', array( __CLASS__, 'sc_wordsmtp_phpmailer_init_callback'), 10, 1 );			  
          add_action('wp_mail_failed', array( __CLASS__, 'sc_wordsmtp_wpmailer_failed_callback'), 10 , 1 );	 
		  add_action('wp_mail_succeeded', array( __CLASS__, 'sc_wordsmtp_wpmailer_succeeded_callback'), 10 , 1 );
		
		  add_filter('wp_mail_from', array( __CLASS__, 'sc_wordsmtp_wp_mail_from_filter_callback'), 10 , 1 );
		  add_filter('wp_mail_from_name', array( __CLASS__, 'sc_wordsmtp_wp_mail_from_name_filter_callback'), 10 , 1 );
		  add_filter('wp_mail_content_type', array( __CLASS__, 'sc_wordsmtp_wp_mail_content_type_filter_callback'), 10 , 1 ); 		
		 		 		  
		  self::$initiated = true;
	}	
			
	/**
	 * Admin required scripts load
	 * @since 1.0.0
	 */
	public static function admin_required_scripts() {
		// get current admin screen
		global $pagenow;
		$screen 	 	= get_current_screen();
		// Loaded only on Plugin admin pages
		$allowed_pages	=	[ 'toplevel_page_word-smtp-topmenu', 'wordsmtp_page_word-smtp-reportmenu' ];
		if ( in_array( $screen->id, $allowed_pages) ) {
			$loading_img = '<img src="' . esc_url( plugins_url( 'public/images/test-email.gif', dirname(__FILE__) ) ) . '" alt="' . esc_attr('SendingMail') . '" /> ';			
			wp_enqueue_style('sc-wordsmtp-datatable-style', plugins_url( '../admin/css/dataTables.css', __FILE__ ), array(), SCWORDSMTP_VERSION, 'all' );
			wp_enqueue_style('sc-wordsmtp-fontawesome', plugins_url( '../admin/css/fontawesome-all.min.css', __FILE__ ), array(),  SCWORDSMTP_VERSION, 'all');
			wp_enqueue_style('sc-wordsmtp-jquery-ui-style', plugins_url( '../admin/css/jquery-ui.css', __FILE__ ) , array(), SCWORDSMTP_VERSION, 'all' );
			wp_enqueue_style('sc-wordsmtp-style', plugins_url( '../admin/css/sc-wordsmtp-misc-styles.css', __FILE__ ) , array('sc-wordsmtp-jquery-ui-style'), SCWORDSMTP_VERSION, 'all' );
			
			wp_enqueue_script('sc-wordsmtp-datatable-script',  plugins_url( '../admin/js/dataTables.js', __FILE__ ), array('jquery', 'jquery-ui-tabs' ), SCWORDSMTP_VERSION, true);
			wp_enqueue_script('sc-wordsmtp-misc-script', plugins_url( '../admin/js/sc-wordsmtp-misc-script.js', __FILE__ ) , array('jquery', 'jquery-ui-tabs' ), SCWORDSMTP_VERSION, true);						
		
			// localize script
			$nonce = wp_create_nonce( 'scwordsmtp_wpnonce' );
			wp_localize_script(
				'sc-wordsmtp-misc-script',
				'sc_wordsmtp_metabox_script_obj',
				array(
					'adminajax_url'                  => admin_url('admin-ajax.php'),
					'nonce'                          => $nonce, 
					'current_screenid'               => $screen->id,
					'current_posttype'               => $screen->post_type,
					'current_pagenow'                => $pagenow,																								
					'test_email_success_msg'         => __( 'Test Email success! Check your email!', 'wordsmtp-wordpress-simple-smtp'),
					'test_email_failed_msg'          => __( 'Test Email failed!', 'wordsmtp-wordpress-simple-smtp'),
					'test_sending_email_msg'         => __( 'Please wait...Sending Test Email with your SMTP settings...', 'wordsmtp-wordpress-simple-smtp'),						
					'lazy_loadimage'    			 => $loading_img
				)
			);
		} // if ( in_array( $screen->id, $allowed_pages) )
	}
	
	/**
	 * SMTP config data
	 * @since 1.0.0
	 */	
	public static function sc_wordsmtp_setup_smtp_config( $phpmailer ) {		
		$options 					= get_option('scwordsmtp-settings');				
		self::$smtphost 			= $options['scwordsmtp-settings-field-smtphost'];
		self::$smtpuser 			= $options['scwordsmtp-settings-field-smtpuser'];
		self::$smtppassword 		= $options['scwordsmtp-settings-field-smtppassword'];	
		self::$smtpport 			= $options['scwordsmtp-settings-field-smtpport'];
		self::$smtp_encryption 		= $options['scwordsmtp-settings-field-encryption'];					   		
												
		if ( isset( self::$smtp_debug_status) && self::$smtp_debug_status ) {
			$phpmailer->SMTPDebug  	= 2;    				
			$phpmailer->Debugoutput = function($str, $level) { self::$output['debugInfo'] .= $str . "<br/>"; };
		}
		
		$phpmailer->isSMTP();                                            			
		$phpmailer->Host       		= self::$smtphost;
		$phpmailer->SMTPAuth   		= true;                                   			
		$phpmailer->Username   		= self::$smtpuser;			
		$phpmailer->Password   		= self::$smtppassword;
		if ( self::$smtp_encryption == 'ssl') {			
			$phpmailer->SMTPSecure  = 'ssl';
		}
		elseif ( self::$smtp_encryption == 'tls') {			
			$phpmailer->SMTPSecure  = 'tls';
		}
		else {
			// Encryption none - default port 25
		}

		$phpmailer->Port       		= self::$smtpport;                                   			

	}
	
	/**
	 * Filter hook - from email
	 * return from email
	 */
	
	public static function sc_wordsmtp_wp_mail_from_filter_callback( $from_email ) {
		$options 					= get_option('scwordsmtp-settings');				
		self::$smtphost 			= $options['scwordsmtp-settings-field-smtphost'];
		self::$smtpuser 			= $options['scwordsmtp-settings-field-smtpuser'];
		self::$smtppassword 		= $options['scwordsmtp-settings-field-smtppassword'];
		if ( isset( self::$smtphost ) && ! empty( self::$smtphost ) 
			 && isset( self::$smtpuser ) && ! empty( self::$smtpuser )
			 && isset( self::$smtppassword ) && ! empty( self::$smtppassword )
			) {
			return self::$smtpuser;
		}
	    return $from_email;	
	}
	
	/**
	 * filter hook - from name
	 * return from name
	 */
	public static function sc_wordsmtp_wp_mail_from_name_filter_callback( $from_name ) {
		$options 					= get_option('scwordsmtp-settings');				
		self::$smtphost 			= $options['scwordsmtp-settings-field-smtphost'];
		self::$smtpuser 			= $options['scwordsmtp-settings-field-smtpuser'];
		self::$smtppassword 		= $options['scwordsmtp-settings-field-smtppassword'];
		if ( isset( self::$smtphost ) && ! empty( self::$smtphost ) 
			 && isset( self::$smtpuser ) && ! empty( self::$smtpuser )
			 && isset( self::$smtppassword ) && ! empty( self::$smtppassword )
			) {
			return get_bloginfo('name');
		}
	    return $from_name;	
	}
	
	/**
	 * Content type filter hook
	 * return content type
	 */
	public static function sc_wordsmtp_wp_mail_content_type_filter_callback( $content_type ) {
		$options 					= get_option('scwordsmtp-settings');				
		self::$smtphost 			= $options['scwordsmtp-settings-field-smtphost'];
		self::$smtpuser 			= $options['scwordsmtp-settings-field-smtpuser'];
		self::$smtppassword 		= $options['scwordsmtp-settings-field-smtppassword'];
		if ( isset( self::$smtphost ) && ! empty( self::$smtphost ) 
			 && isset( self::$smtpuser ) && ! empty( self::$smtpuser )
			 && isset( self::$smtppassword ) && ! empty( self::$smtppassword )
			) {
			return 'text/html';
		}
	    return $content_type;	
	}
		
	
	public static function sc_wordsmtp_phpmailer_init_callback( $phpmailer ) {		
		self::sc_wordsmtp_setup_smtp_config( $phpmailer );						
	}
	
	/**
	 * Mail fail hook callback
	 * Store mail fail data in db
	 */
	public static function sc_wordsmtp_wpmailer_failed_callback( $error ) {						
		// Write Error log report in DB
		global $wpdb;								
		$log		 		=	$error->get_error_message();
		$insert_data['log'] = $log;				
		foreach( $error->error_data as $error_data ) {								
			$insert_data['subject']	 	= $error_data['subject'];
			foreach( $error_data['to'] as $toemail ) {
				$insert_data['toemail']	= $toemail;
			}
			$wpdb->insert( $wpdb->prefix . SC_Wordsmtp::$scwordsmtplog_error_tbl, $insert_data );								
		}
						
		self::$output['status']	= 'fail';
		self::$output['reason']	= $error->get_error_message();
	}
	
	/**
	 * Mail success hook callback
	 * Store success report data in db
	 */
	public static function sc_wordsmtp_wpmailer_succeeded_callback( $mail_data ) {		
		// Write Success delivery log in DB
		global $wpdb;
		$subject					= isset( $mail_data['subject'] )? $mail_data['subject'] : '';
		$insert_data['subject']		= $subject;
		foreach ( $mail_data['to'] as $toemail ) {
			$insert_data['toemail']	= $toemail;
			$wpdb->insert( $wpdb->prefix . SC_Wordsmtp::$scwordsmtplog_delivery_tbl, $insert_data );
		}
				
		// Output status
		self::$output['status']	= 'success';
		self::$output['reason']	= 'wp_mailer_succeeded_callback triggered';
	}
	
	/**
	 * Test Email - Check SMTP email
	 * return json
	 */
	public static function sc_wordsmtp_test_email() {
		check_ajax_referer( 'scwordsmtp_wpnonce', 'security' );	
		
		$options 						= get_option('scwordsmtp-settings');
		self::$output					= [];		
		self::$smtp_debug_status        = true;  
				
		$recipient						= isset( $_POST['params']['recipient'] ) && ! empty( $_POST['params']['recipient'] )? sanitize_email( $_POST['params']['recipient'] ) : '';	$from_name						= isset( $_POST['params']['fromname'] ) && ! empty( $_POST['params']['fromname'] )? sanitize_text_field( $_POST['params']['fromname'] ) : '';
		self::$from_email 				= $options['scwordsmtp-settings-field-smtpuser'];
		$headers[]						= 'From: '. $from_name .' <'. self::$from_email .'>';					
				
		if ( empty( $recipient) || ! is_email( $recipient ) ) {
			 self::$output['status']	= 'fail';
			 self::$output['reason']	= __( 'Valid Test Email Recipient address required.', 'wordsmtp-wordpress-simple-smtp' );
			 self::$output['debugInfo'] = '';
		}			
		else {
			self::$smtp_recipient		= $recipient;
			self::$smtp_from_name		= $from_name;
			self::$email_subject		= sanitize_text_field('WordSMTP - A Simple Test Email');			
			self::$email_body			= wp_kses("<h4>Hello</h4><h5>Congrats! Your SMTP setup working fine.</h5><p>This is a test email using with WordSMTP.<br/><br/>With Thanks<br/>WordSMTP Dev Team</p>", [ 'h4' => [], 'h5' => [], 'br' => [], 'p' => [] ] );
			self::$output['debugInfo']	= wp_kses("<h4>DEBUG INFORMATION</h4><br/><br/>", [ 'h4' => [], 'br' => [] ] );								 
			$mail_send_status			= wp_mail( $recipient, self::$email_subject, self::$email_body, $headers, array() );
		}
		
		echo wp_json_encode( self::$output, JSON_HEX_APOS );
		wp_die();
	}										
	
				
} // End Class
?>