jQuery(document).ready(function($) {				
	console.log('Localized WordSMTP Data');	
	console.log( sc_wordsmtp_metabox_script_obj );	
	// Test Email Click
	$('.sc-wordsmtp-test-email').click(function() {
		$(this).attr("disabled", "disabled" );
		$('.sc-wordsmtp-test-email-message').html('<h4>'+ sc_wordsmtp_metabox_script_obj.test_sending_email_msg + sc_wordsmtp_metabox_script_obj.lazy_loadimage +'</h4>');
		let params = {};
		params.recipient		= $('.test-email-send-to').val();
		params.fromname			= $('.test-email-from-name').val();
		
		$.ajax({
		  type:"POST",
		  cache: false,
		  url: sc_wordsmtp_metabox_script_obj.adminajax_url,
		  data : {			    
                action 	 : 'sc_wordsmtp_test_email',
                security : sc_wordsmtp_metabox_script_obj.nonce,
			    params   : params
                },		  
		  success: function(data) { 
		  	 console.log(data); 
			 let jsonData	= JSON.parse( data );
			  if ( jsonData.status == 'success' ) {
				$('.sc-wordsmtp-test-email-message').html('<div class="notice notice-success">'+ sc_wordsmtp_metabox_script_obj.test_email_success_msg +'</div>');				   
				$('.sc-wordsmtp-debug-info').html( jsonData.debugInfo ).css({ 'border': '1px solid #209E0D', 'color': '#0C771F' });  
				$('.sc-wordsmtp-debug-info').show();
				$('.sc-wordsmtp-test-email').removeAttr('disabled');
			  }
			  else {
				  $('.sc-wordsmtp-test-email-message').html('');
				  $('.sc-wordsmtp-debug-info').html( jsonData.reason + jsonData.debugInfo ).css({ 'border': '1px solid #DD1A1D', 'color': 'crimson' });  				  
				  $('.sc-wordsmtp-debug-info').show();
				  $('.sc-wordsmtp-test-email').removeAttr('disabled');
			  }
			},
		  error: function( xhr, status, error ) { 
		  	 console.log(xhr); 
			 console.log(status); 
			 console.log(error); 
			 $('.sc-wordsmtp-test-email-message').html('<div class="notice notice-error">'+ sc_wordsmtp_metabox_script_obj.test_email_failed_msg+'</div>');	
			 $('.sc-wordsmtp-test-email').removeAttr('disabled');
			}
		})
				
	});
	
	// SMTP Encryption Selection
	$('.encryption-radio').on('change', function() {
		let smtpPortEle	= $('.smtp-port');
		 switch( $(this).val() ) {
			 case 'none':
				 smtpPortEle.val(25);
				 break;
			 case 'ssl':
				 smtpPortEle.val(465);
				 break;
			 case 'tls':
				 smtpPortEle.val(587);
				 break;
		 }
	});
					
}); // End jQuery(document).ready(function($)