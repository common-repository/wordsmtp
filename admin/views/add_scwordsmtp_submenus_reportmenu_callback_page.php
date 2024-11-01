<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap sc-wordsmtp-mail-report-tables-wrapper" style="margin: 1%; display: none;">
    <h4 class="sc-wordsmtp-mail-report-label"><span class="sc-wordsmtp-email-icon dashicons dashicons-email-alt2"></span> WordSMTP Mail Report</h4>
    
	<div id="tabs">
		  <ul>			
			<li><a href="#mailDeliveryReport" style="font-weight: bold;"><i class="dashicons dashicons-yes" style="color: green;"></i>&nbsp;<?php esc_html_e('Delivery', 'wordsmtp-wordpress-simple-smtp');?></a></li>			
			<li><a href="#mailErrorReport" style="font-weight: bold;"><i class="dashicons dashicons-no" style="color: red;"></i>&nbsp;<?php esc_html_e('Error', 'wordsmtp-wordpress-simple-smtp');?></a></li>
		  </ul>
		  
		  <div id="mailErrorReport">
				<h3><i class="dashicons dashicons-editor-table" style="color: red;"></i>&nbsp;<?php esc_html_e('Mail Error Log Report', 'wordsmtp-wordpress-simple-smtp');?></h3>
				<table id="mailErrorReportTable" class="display hover stripe">
					<thead>
						<tr>
							<th><?php esc_html_e('To', 'wordsmtp-wordpress-simple-smtp');?></th>
							<th><?php esc_html_e('Subject', 'wordsmtp-wordpress-simple-smtp');?></th>
							<th><?php esc_html_e('Error', 'wordsmtp-wordpress-simple-smtp');?></th>
							<th><?php esc_html_e('Status', 'wordsmtp-wordpress-simple-smtp');?></th>
							<th><?php esc_html_e('Created', 'wordsmtp-wordpress-simple-smtp');?></th>
						</tr>
					</thead>
					<tbody>
					   <?php 
						if ( isset( $error_logdata ) && array_filter( $error_logdata ) ) {
							foreach ( $error_logdata as $data ) {
						?>
						<tr>
							<td><?php echo esc_html($data['toemail']);?></td>
							<td><?php echo esc_html( $data['subject']);?></td>
							<td><?php echo wp_kses_post($data['log']);?></td>
							<td><i class="dashicons dashicons-no" style="color: red; font-weight: bolder;"></i></td>
							<td><?php echo esc_html($data['created_at']);?></td>
						</tr>
						<?php 
							}
						}
						?>
					</tbody>
				</table>			
		  </div><!-- #mailErrorReport -->
		  
		  <div id="mailDeliveryReport">
				<h3><i class="dashicons dashicons-editor-table" style="color: green;"></i>&nbsp;<?php esc_html_e('Mail Delivery Log Report');?></h3>
				<table id="mailDeliveryReportTable" class="display hover stripe">
					<thead>
						<tr>
							<th><?php esc_html_e('To', 'wordsmtp-wordpress-simple-smtp');?></th>
							<th><?php esc_html_e('Subject', 'wordsmtp-wordpress-simple-smtp');?></th>
							<th><?php esc_html_e('Status', 'wordsmtp-wordpress-simple-smtp');?></th>
							<th><?php esc_html_e('Created', 'wordsmtp-wordpress-simple-smtp');?></th>
						</tr>
					</thead>
					<tbody>
					   <?php 
						if ( isset( $delivery_logdata ) && array_filter( $delivery_logdata ) ) {
							foreach ( $delivery_logdata as $data ) {
						?>
						<tr>
							<td><?php echo esc_html($data['toemail']);?></td>
							<td><?php echo esc_html($data['subject']);?></td>
							<td><i class="dashicons dashicons-yes" style="color: green; font-weight: bolder;"></i></td>
							<td><?php echo esc_html($data['created_at']);?></td>
						</tr>
						<?php 
							}
						}
						?>
					</tbody>
				</table>						
		  </div><!-- #mailDeliveryReport -->
		  
	</div> <!-- /.tabs -->
</div><!-- /.wrap -->


<script type="text/javascript">
	jQuery(document).ready(function($){
		
		//$('#tabs').tabs();
		$('#tabs').tabs().promise().done(function() {
			$('#mailErrorReportTable, #mailDeliveryReportTable').DataTable();
			$('.sc-wordsmtp-mail-report-tables-wrapper').fadeIn(500);
			// Fix Show Entries select box UI
			$('body').removeClass('wp-core-ui');										
		});
	});
</script>