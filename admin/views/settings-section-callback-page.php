<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="sc-wordsmtp-logo-image-wrapper">
<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/WordSMTP-icon.jpg');?>" alt="<?php esc_attr('SMTP Mailer');?>" />
</div>
<h4 style="color: #2271b1;"><i class="fa-solid fa-circle-info"></i>&nbsp;<?php esc_html_e('WordSMTP - Simple SMTP Mailer Solution Plugin will work with any SMTP support mailer including almost all popular mailer (Amazon SES / SendGrid / mailgun/ Mandrill / Brevo / Postmark / MessageBird ) in the market.', 'wordsmtp-wordpress-simple-smtp');?></h4>