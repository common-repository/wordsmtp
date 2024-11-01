<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class SC_Wordsmtp_Autoloader {
	
	public function __construct() {		
		new SC_Wordsmtp();
		new SC_Wordsmtp_Ajaxhandler();
	}
}
?>