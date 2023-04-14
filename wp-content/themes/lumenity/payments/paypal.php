<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Do payments */
add_action("askme_do_payments","askme_do_payments");
if (!function_exists('askme_do_payments')):
	function askme_do_payments() {
		require_once get_template_directory().'/payments/paypal.class.php';
		$p = new paypal_class;
		$paypal_sandbox = askme_options('paypal_sandbox');
		if ($paypal_sandbox == 1) {
			$p->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}else {
			$p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
		}
		$protocol    = is_ssl() ? 'https' : 'http';
		$this_script = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$user_id     = get_current_user_id();

		switch ((isset($_REQUEST['action'])?$_REQUEST['action']:"")) {
			case 'process':
				if (isset($_POST["go"]) && $_POST["go"] == "paypal") {
					$question_sticky = (isset($_REQUEST['question_sticky']) && $_REQUEST['question_sticky'] != ""?(int)$_REQUEST['question_sticky']:"");
					$CatDescription  = (isset($_REQUEST['CatDescription']) && $_REQUEST['CatDescription'] != ""?esc_attr($_REQUEST['CatDescription']):"");
					$item_no         = (isset($_REQUEST['item_number']) && $_REQUEST['item_number'] != ""?esc_attr($_REQUEST['item_number']):"");
					$payment         = (isset($_REQUEST['payment']) && $_REQUEST['payment'] != ""?esc_attr($_REQUEST['payment']):"");
					$key             = (isset($_REQUEST['key']) && $_REQUEST['key'] != ""?esc_attr($_REQUEST['key']):"");
					$quantity        = (isset($_REQUEST['quantity']) && $_REQUEST['quantity'] != ""?esc_attr($_REQUEST['quantity']):"");
					$coupon          = (isset($_REQUEST['coupon']) && $_REQUEST['coupon'] != ""?esc_attr($_REQUEST['coupon']):"");
					$currency_code   = (isset($_REQUEST['currency_code']) && $_REQUEST['currency_code'] != ""?esc_attr($_REQUEST['currency_code']):"");
					
					echo '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Go to PayPal now","vbegy").'</span><br>'.__("Please wait will go to the PayPal now to pay a new payment.","vbegy").'</p></div>';
					
					if ($question_sticky != "") {
						update_user_meta($user_id,$user_id."_question_sticky",$question_sticky);
					}
					
					$p->add_field('business', askme_options('paypal_email'));
					$p->add_field('return', $this_script.'?action=success');
					$p->add_field('cancel_return', $this_script.'?action=cancel');
					$p->add_field('notify_url', $this_script.'?action=ipn');
					$p->add_field('item_name', $CatDescription);
					$p->add_field('item_number', $item_no);
					$p->add_field('amount', $payment);
					$p->add_field('key', $key);
					$p->add_field('quantity', $quantity);
					$p->add_field('currency_code', $currency_code);
					
					$p->submit_paypal_post();
					//$p->dump_fields();
				}else {
					wp_safe_redirect(esc_url(home_url('/')));
				}
				get_footer();
				die();
			break;
			case 'success':
				if ((isset($_REQUEST['txn_id']) && $_REQUEST['txn_id'] != "") || isset($_REQUEST['tx']) && $_REQUEST['tx'] != "") {
					$data = wp_remote_post($p->paypal_url.'?cmd=_notify-synch&tx='.(isset($_REQUEST['tx'])?$_REQUEST['tx']:(isset($_REQUEST['txn_id'])?$_REQUEST['txn_id']:'')).'&at='.askme_options("identity_token"));
					if (!is_wp_error($data)) {
						$data = $data['body'];
						$response = substr($data, 7);
						$response = urldecode($response);
						
						preg_match_all('/^([^=\s]++)=(.*+)/m', $response, $m, PREG_PATTERN_ORDER);
						$response = array_combine($m[1], $m[2]);
						
						if (isset($response['charset']) && strtoupper($response['charset']) !== 'UTF-8') {
							foreach ($response as $key => &$value) {
								$value = mb_convert_encoding($value, 'UTF-8', $response['charset']);
							}
							$response['charset_original'] = $response['charset'];
							$response['charset'] = 'UTF-8';
						}
						
						ksort($response);
					}else {
						wp_safe_redirect(esc_url(home_url('/')));
						die();
					}
					
					$item_transaction = (isset($response['txn_id'])?esc_attr($response['txn_id']):"");
					$last_payments    = get_user_meta($user_id,$user_id."_last_payments",true);
					
					if (isset($item_transaction)) {
						if (isset($last_payments) && $last_payments == $item_transaction) {
							wp_safe_redirect(esc_url(home_url('/')));
							die();
						}else {
							askme_payment_succeeded($user_id,$response,"redirect");
							die();
						}
					}else {
						echo '<div class="alert-message error"><i class="icon-ok"></i><p><span>'.__("Payment Failed","vbegy").'</span><br>'.__("The payment was failed!","vbegy").'</p></div>';
					}
				}else {
					wp_safe_redirect(esc_url(home_url('/')));
					die();
				}
			break;
			case 'cancel':
				echo '<div class="alert-message error"><i class="icon-ok"></i><p><span>'.__("Payment Canceled","vbegy").'</span><br>'.__("The payment was canceled!","vbegy").'</p></div>';
			break;
			case 'ipn':
				if ($p->validate_ipn()) {
					$askme_payment_data = apply_filters("askme_filter_payment_data",true);
					if ($askme_payment_data == true) {
						$dated = date("D, d M Y H:i:s", time()); 
						$subject  = "Instant Payment Notification - Received Payment";
						$body     = "An instant payment notification was successfully recieved\n";
						$body    .= "from ".esc_attr($p->ipn_data['payer_email'])." on ".date('m/d/Y');
						$body    .= " at ".date('g:i A')."\n\nDetails:\n";
						$headers  = "";
						$headers .= "From: Paypal \r\n";
						$headers .= "Date: $dated \r\n";
						
						$PaymentStatus =  esc_attr($p->ipn_data['payment_status']);
						$Email         =  esc_attr($p->ipn_data['payer_email']);
						$id            =  esc_attr($p->ipn_data['item_number']);
						
						if($PaymentStatus == 'Completed' or $PaymentStatus == 'Pending') {
							$PaymentStatus = '2';
						}else {
							$PaymentStatus = '1';
						}
						wp_mail(get_bloginfo("admin_email"),$subject,$body,$headers);
					}else {
						do_action("askme_payment_data_ipn",$p->ipn_data);
					}
				}else {
					wp_safe_redirect(esc_url(home_url('/')));
					die();
				}
			break;
		}
	}
endif;