<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


// check if the user have submitted the settings
if ( isset($_GET['settings-updated'] ) ) {
   add_settings_error('scwordsmtp-settings-messages', 'scwordsmtp-settings-messages', __('Settings Saved', 'wordsmtp-wordpress-simple-smtp'), 'updated');
}
// show error / update messages
settings_errors('scwordsmtp-settings-messages');
?>
<div class="wrap" style="margin: 1%;">
  <form action="options.php" method="post">
      <?php
      settings_fields('scwordsmtp-settings');
      do_settings_sections('scwordsmtp-settings');
      submit_button('Save Settings');
      ?>
  </form>
   
   <?php
        $test_email 			= true;
   		$options 				= get_option('scwordsmtp-settings');		
	    if ( $options ) {
			$smtphost 			= $options['scwordsmtp-settings-field-smtphost'];
			$smtpuser 			= $options['scwordsmtp-settings-field-smtpuser'];
			$smtppassword 		= $options['scwordsmtp-settings-field-smtppassword'];		    
			if ( ! isset( $smtphost ) || empty( $smtphost ) ) {
				$test_email = false;
			}
			if ( ! isset( $smtpuser ) || empty( $smtpuser ) ) {
				$test_email = false;
			}
			if ( ! isset( $smtppassword ) || empty( $smtppassword ) ) {
				$test_email = false;
			}
			
			if ( $test_email ) {
			?>
			   <hr />
			   <table style="width:100%">
					<thead></thead>
					<tbody>
						<tr>
							<td class="tbl-td-label-name">
								<a href="Javascript:void(0);" class="sc-wordsmtp-test-email button button-primary"><?php esc_html_e('Test Email', 'wordsmtp-wordpress-simple-smtp');?></a>
							</td>							
							<td class="sc-wordsmtp-test-email-message"></td>
						</tr>
						
						<tr>
							<td class="tbl-td-label-name"><?php esc_html_e('Test Email Recipient:', 'wordsmtp-wordpress-simple-smtp');?></td>
							<td><input type="text" name="test-email-send-to" class="test-email-send-to" value="<?php echo esc_html( get_option('admin_email') );?>" size="50" /></td>
						</tr>
						
						<tr>
							<td class="tbl-td-label-name"><?php esc_html_e('Test Email From Name (optional):', 'wordsmtp-wordpress-simple-smtp');?></td>
							<td><input type="text" name="test-email-from-name" class="test-email-from-name" value="<?php echo esc_html( get_bloginfo('name') );?>" size="50" /></td>
						</tr>
												
					</tbody>
			   </table>
			   <hr />
			   
			   <div class="sc-wordsmtp-debug-info"></div>
	   <?php }
		}	
	?>
    
</div><!-- /.wrap -->  
