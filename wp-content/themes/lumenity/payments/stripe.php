<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Stripe payment */
add_action('wp_ajax_askme_stripe_payment','askme_stripe_payment');
add_action('wp_ajax_nopriv_askme_stripe_payment','askme_stripe_payment');
function askme_stripe_payment() {
	$result        = array();
	$user_id       = get_current_user_id();
	$custom        = (isset($_POST['custom'])?esc_html($_POST['custom']):'');
	$item_name     = esc_html($_POST['item_name']);
	$item_number   = esc_html($_POST['item_number']);
	$name          = esc_html($_POST['name']);
	$payer_email   = esc_html($_POST['email']);
	$line1         = (isset($_POST['line1'])?esc_html($_POST['line1']):'');
	$line1         = (isset($_POST['line1'])?esc_html($_POST['line1']):'');
	$postal_code   = (isset($_POST['postal_code'])?esc_html($_POST['postal_code']):'');
	$country       = (isset($_POST['country'])?esc_html($_POST['country']):'');
	$city          = (isset($_POST['city'])?esc_html($_POST['city']):'');
	$state         = (isset($_POST['state'])?esc_html($_POST['state']):'');
	$payment       = floatval($_POST['payment']);
	$item_price    = floatval($payment*100);
	$str_replace   = str_replace('askme_'.$item_number.'-','',$custom);
	$currency_code = askme_options("currency_code");
	$currency_code = (isset($currency_code) && $currency_code != ""?$currency_code:"USD");

	if ($line1 != '') {
		update_user_meta($user_id,'line1',$line1);
	}
	if ($line1 != '') {
		update_user_meta($user_id,'line1',$line1);
	}
	if ($postal_code != '') {
		update_user_meta($user_id,'postal_code',$postal_code);
	}
	if ($country != '') {
		update_user_meta($user_id,'country',$country);
	}
	if ($city != '') {
		update_user_meta($user_id,'city',$city);
	}
	if ($state != '') {
		update_user_meta($user_id,'state',$state);
	}
	
	require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
	\Stripe\Stripe::setApiKey(askme_options('secret_key'));
	try {
		if (isset($_POST['payment-intent-id']) && $_POST['payment-intent-id'] != '') {
			$charge = \Stripe\PaymentIntent::retrieve(esc_html($_POST['payment-intent-id']));
			askme_finish_stripe_payment($charge->payment_method,$charge->customer);
			if (isset($charge->status) && ($charge->status == 'active' || $charge->status == 'paid' || $charge->status == 'succeeded')) {
				$success = true;
			}else {
				$result['success'] = 0;
				$result['error']   = esc_html__('Transaction has been failed.','vbegy');
			}
		}else if (isset($_POST['payment-method-id']) && $_POST['payment-method-id'] != '') {
			if (!isset($_POST['askme_stripe_nonce']) || !wp_verify_nonce($_POST['askme_stripe_nonce'],'askme_stripe_nonce')) {
				$result['success'] = 0;
				$result['error']   = esc_html__('There is an error, Please reload the page and try again.','vbegy');
			}else {
				$payment_method_id = esc_html($_POST['payment-method-id']);
				$args = array(
					'payment_method'   => $payment_method_id,
					'name'             => $name,
					'email'            => $payer_email,
					'invoice_settings' => array(
						'default_payment_method' => $payment_method_id
					)
				);
				$customer_address = array();
				if ($line1 != '') {
					$customer_address['line1'] = $line1;
				}
				if ($country != '') {
					$customer_address['country'] = $country;
				}
				if ($city != '') {
					$customer_address['city'] = $city;
				}
				if ($state != '') {
					$customer_address['state'] = $state;
				}
				if ($postal_code != '') {
					$customer_address['postal_code'] = $postal_code;
				}
				if (isset($customer_address) && !empty($customer_address)) {
					$args['address'] = $customer_address;
				}
				$customer_description = $item_name;
				if (isset($customer_description) && $customer_description != '') {
					$args['description'] = $customer_description;
				}
				if (isset($customer_metadata)) {
					$args['metadata'] = $customer_metadata;
				}
				$customer = \Stripe\Customer::create($args);
				update_user_meta($user_id,'askme_stripe_customer',$customer->id);
				$askme_stripe_customer = get_user_meta($user_id,'askme_stripe_customer',true);
				$args = array(
					'amount'              => $item_price,
					'currency'            => $currency_code,
					'confirmation_method' => 'automatic',
					'confirm'             => true,
					'customer'            => $askme_stripe_customer,
					'payment_method'      => $payment_method_id,
				);
				if (isset($payment_metadata) && !empty($payment_metadata)) {
					$args['metadata'] = $payment_metadata;
				}
				$payment_description = $item_name;
				if (isset($payment_description) && $payment_description != '') {
					$args['description'] = $payment_description;
				}
				$charge = \Stripe\PaymentIntent::create($args);
				if (isset($charge->status) && (($charge->status == 'requires_action' && $charge->next_action->type == 'use_stripe_sdk') || $charge->status == 'incomplete')) {
					$result['confirm_card']   = 1;
					$result['success']        = 0;
					$result['client_secret']  = (isset($charge->client_secret)?esc_html($charge->client_secret):(isset($charge->latest_invoice->payment_intent->client_secret)?esc_html($charge->latest_invoice->payment_intent->client_secret):''));
					$result['payment_method'] = $charge->id;
				}else if ($charge->status == 'active' || $charge->status == 'paid' || $charge->status == 'succeeded') {
					$success = true;
				}else {
					$result['success'] = 0;
					$result['error']   = esc_html__('Transaction has been failed.','vbegy');
				}
			}
		}else {
			$result['success'] = 0;
			$result['error']   = esc_html__('Transaction has been failed.','vbegy');
		}
		if (isset($success) && $success == true) {
			$askme_subscr_id = get_user_meta($user_id,"askme_subscr_id",true);
			$response = $charge->jsonSerialize();
			$subscr_id = ($askme_subscr_id != ""?$v_subscr_id:(isset($charge->id)?$charge->id:$charge->id));
			if ($item_number == "pay_sticky") {
				$_question_sticky = str_replace("askme_pay_sticky-","",$custom);
			}
			if ($item_number == "" || $item_number == "pay_ask" || $item_number == "ask_question") {
				$redirect_to = esc_url(get_page_link(askme_options('add_question')));
			}else if ($item_number == "pay_sticky" && isset($_question_sticky) && $_question_sticky != "") {
				$redirect_to = esc_url(get_the_permalink($_question_sticky));
			}else {
				$redirect_to = esc_url(home_url('/'));
			}
			$result['success']  = 1;
			$result['redirect'] = $redirect_to;
			$array = array (
				'item_no'          => $item_number,
				'item_name'        => $item_name,
				'item_price'       => $payment,
				'item_currency'    => $currency_code,
				'item_transaction' => (isset($response['charges']['data'][0]['balance_transaction'])?$response['charges']['data'][0]['balance_transaction']:(isset($response['latest_invoice']['payment_intent']['charges']['data'][0]['balance_transaction'])?$response['latest_invoice']['payment_intent']['charges']['data'][0]['balance_transaction']:'')),
				'custom'           => $custom,
				'sandbox'          => '',
				'payment'          => 'Stripe',
				'id'               => ($subscr_id == $response['id']?(isset($response['latest_invoice']['payment_intent']['id'])?$response['latest_invoice']['payment_intent']['id']:$response['id']):$response['id']),
				'customer'         => ($askme_subscr_id == ""?$response['customer']:""),
				'subscr_id'        => $subscr_id,
			);
			askme_payment_succeeded($user_id,$array);
		}else if (!isset($result['confirm_card'])) {
			$result['success'] = 0;
			$result['error']   = esc_html__('Transaction has been failed.','vbegy');
		}
	}catch ( \Stripe\Exception\CardException $e ) {
		$result['success'] = 0;
		$result['error']   = $e->getError()->message;
	}catch ( Exception $e ) {
		$error_message = $e->getMessage();
		$result['success'] = 0;
		if (!isset($result['resubmit_again'])) {
			$result['error'] = $error_message;
		}
	}
	echo json_encode(apply_filters('askme_json_stripe_payment',$result));
	die();
}
/* Finish stripe payment */
function askme_finish_stripe_payment($payment_method_id,$get_the_customer_id) {
	require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
	\Stripe\Stripe::setApiKey(askme_options("secret_key"));
	$payment_method = \Stripe\PaymentMethod::retrieve($payment_method_id);
	$payment_method->attach(['customer' => $get_the_customer_id]);
	$update_customer = \Stripe\Customer::update(
		$get_the_customer_id,[
			'invoice_settings' => [
				'default_payment_method' => $payment_method_id,
			],
		]
	);
}
/* Stripe webhooks */
function askme_stripe_data_webhooks() {
	if (isset($_GET["action"]) && $_GET["action"] == "stripe") {
		$input = @file_get_contents('php://input');
		$response = json_decode($input);
		if (isset($response->data->object)) {
			$request = $response->data->object;
			if (isset($response->type) && $response->type == "charge.refunded") {
				$item_transaction = $request->balance_transaction;
				$args = array(
					'meta_key'       => 'payment_item_transaction',
					'meta_value'     => $item_transaction,
					'post_type'      => 'statement',
					'posts_per_page' => -1
				);
				$query = new WP_Query($args);
				if ($query->have_posts()) {
					$post_id = (isset($query->posts[0]->ID)?$query->posts[0]->ID:0);
					if ($post_id > 0) {
						$item_transaction_refund = $request->refunds->data[0]->id;
						$user_id = $query->posts[0]->post_author;
						if (!askme_find_refund($item_transaction_refund)) {
							$item_currency = get_post_meta($post_id,"payment_item_currency",true);
							$item_number = get_post_meta($post_id,"payment_item_number",true);
							$item_price = (isset($request->amount)?floatval($request->amount/100):get_post_meta($post_id,"payment_item_price",true));
							askme_refund_canceled_payment($user_id,$post_id,$item_number);
							$response = array(
								"item_name"            => get_the_title($post_id),
								"item_price"           => $item_price,
								"item_currency"        => $item_currency,
								"item_transaction"     => $item_transaction_refund,
								"original_transaction" => $item_transaction,
							);
							askme_insert_refund($response,$user_id,"refund");
							askme_site_user_money($item_price,"-",$item_currency,$user_id);
							update_post_meta($post_id,"payment_refund","refund");
							update_post_meta($post_id,"payment_original_transaction",$item_transaction_refund);
						}
					}
				}
			}
		}
	}
}?>