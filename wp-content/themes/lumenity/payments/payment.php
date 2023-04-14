<?php /* Payment form */
function askme_payment_form($payment_item_name,$payment_type,$data,$item_id = "",$days_sticky = "") {
	$output = '';
	$rand = rand(1,1000);
	$payment_methods = askme_options("payment_methodes");
	if ($payment_type == "payments_points") {
		$payment_methods["points"] = array("sort" => esc_html__("points","vbegy"),"value" => "points");
	}
	foreach ($payment_methods as $key => $value) {
		if ($value["value"] !== '0') {
			$payment_methods_activated = true;
		}
	}
	$user_get_current_user_id = get_current_user_id();
	if ($payment_item_name == "pay_sticky") {
		$points_price = (int)askme_options("sticky_payment_points");
		$message = sprintf(esc_html__("Please pay by points to allow to be able to sticky the question %s For %s days.","vbegy"),' "'.$points_price." ".esc_html__("points","vbegy").'"',$days_sticky);
		$second_message = __("Pay to sticky","vbegy").'</span><br>'.__("Please make a payment to allow to be able to sticky the question.","vbegy");
		$third_message = sprintf(__("For %s days.","vbegy"),$days_sticky);
		$fourth_message = __("Sticky question free? Add this coupon.","vbegy");
		$action = get_the_permalink($item_id);
		$process_input = "sticky";
		$item_price = "pay_sticky_payment";
		$item_name = __("Pay to make question sticky","vbegy");
		$custom_input = '<input type="hidden" name="question_sticky" value="'.$item_id.'">';
	}else {
		$points_price = (int)askme_options("ask_payment_points");
		$message = sprintf(esc_html__("Please pay by points to allow to be able to add a question %s.","vbegy"),' "'.$points_price." ".esc_html__("points","vbegy").'"');
		$second_message = __("Pay to ask","vbegy").'</span><br>'.__("Please make a payment to allow to be able to add a question.","vbegy");
		$third_message = "";
		$fourth_message = __("Ask a free question? Add this coupon.","vbegy");
		$action = get_page_link(askme_options('add_question'));
		$process_input = "ask";
		$item_price = "pay_ask_payment";
		$item_name = __("Ask a new question","vbegy");
		$custom_input = '<input type="hidden" name="action" value="process">';
	}
	if ($payment_type == "points") {
		$output .= '<div class="alert-message success"><p>'.$message.'</p></div>';
		if ($user_get_current_user_id > 0) {
			$points_user = get_user_meta($user_get_current_user_id,"points",true);
			if ($points_price <= $points_user) {
				$output .= '<div class="process_area">
					<form method="post" action="'.esc_url($action).'">
						<input type="submit" class="button" value="'.esc_attr__("Process","vbegy").'">
						<input type="hidden" name="process" value="'.$process_input.'">
						<input type="hidden" name="points" value="'.$points_price.'">
					</form>
				</div>';
			}else {
				$output .= '<div class="alert-message error"><p>'.esc_html__("Sorry, you haven't enough points","vbegy").'</p></div>';
			}
		}
	}else {
		$active_coupons = askme_options("active_coupons");
		$coupons = get_option("coupons");
		$free_coupons = askme_options("free_coupons");
		$currency_code = askme_options("currency_code");
		$currency_code = (isset($currency_code) && $currency_code != ""?$currency_code:"USD");
		$item_price = $last_payment = floatval(askme_options($item_price));
		if ($active_coupons == 1) {
			if (isset($data["add_coupon"]) && $data["add_coupon"] == "submit") {
				$coupon_name = esc_attr($data["coupon_name"]);
				$coupons_not_exist = "no";
				
				if (isset($coupons) && is_array($coupons)) {
					foreach ($coupons as $coupons_k => $coupons_v) {
						if (is_array($coupons_v) && in_array($coupon_name,$coupons_v)) {
							$coupons_not_exist = "yes";
							
							if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] != "") {
								$coupons_v["coupon_date"] = !is_numeric($coupons_v["coupon_date"]) ? strtotime($coupons_v["coupon_date"]):$coupons_v["coupon_date"];
							}
							
							if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] != "" && current_time( 'timestamp' ) > $coupons_v["coupon_date"]) {
								$output .= '<div class="alert-message error"><p>'.__("This coupon has expired.","vbegy").'</p></div>';
							}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent" && (int)$coupons_v["coupon_amount"] > 100) {
								$output .= '<div class="alert-message error"><p>'.__("This coupon is not valid.","vbegy").'</p></div>';
							}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount" && (int)$coupons_v["coupon_amount"] > $item_price) {
								$output .= '<div class="alert-message error"><p>'.__("This coupon is not valid.","vbegy").'</p></div>';
							}else {
								if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent") {
									$the_discount = ($item_price*$coupons_v["coupon_amount"])/100;
									$last_payment = $item_price-$the_discount;
								}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount") {
									$last_payment = $item_price-$coupons_v["coupon_amount"];
								}
								$output .= '<div class="alert-message success"><p>'.sprintf(__("Coupon ".'"%s"'." applied successfully.","vbegy"),$coupon_name).'</p></div>';
								
								update_user_meta($user_get_current_user_id,$user_get_current_user_id."_coupon",esc_attr($coupons_v["coupon_name"]));
								update_user_meta($user_get_current_user_id,$user_get_current_user_id."_coupon_value",($last_payment <= 0?"free":$last_payment));
							}
						}
					}
				}
				
				if ($coupons_not_exist == "no" && $coupon_name == "") {
					$output .= '<div class="alert-message error"><p>'.__("Coupon does not exist!.","vbegy").'</p></div>';
				}else if ($coupons_not_exist == "no") {
					$output .= '<div class="alert-message error"><p>'.sprintf(__("Coupon ".'"%s"'." does not exist!.","vbegy"),$coupon_name).'</p></div>';
				}
			}else {
				delete_user_meta($user_get_current_user_id,$user_get_current_user_id."_coupon");
				delete_user_meta($user_get_current_user_id,$user_get_current_user_id."_coupon_value");
			}
		}
		
		$output .= '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.$second_message.' "'.$last_payment." ".$currency_code.'" '.$third_message.'</p></div>';
		
		if (isset($coupons) && is_array($coupons) && $free_coupons == 1 && $active_coupons == 1) {
			foreach ($coupons as $coupons_k => $coupons_v) {
				$item_prices = $last_payments = floatval(askme_options($item_price));
				if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent") {
					$the_discount = ($item_prices*$coupons_v["coupon_amount"])/100;
					$last_payments = $item_prices-$the_discount;
				}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount") {
					$last_payments = $item_prices-$coupons_v["coupon_amount"];
				}
				
				if ($last_payments <= 0) {
					if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] != "") {
						$coupons_v["coupon_date"] = !is_numeric($coupons_v["coupon_date"]) ? strtotime($coupons_v["coupon_date"]):$coupons_v["coupon_date"];
					}
					
					if ((isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] != "" && current_time( 'timestamp' ) > $coupons_v["coupon_date"]) && (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent" && (int)$coupons_v["coupon_amount"] > 100) && (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount" && (int)$coupons_v["coupon_amount"] > $item_prices)) {
					}else {
						$output .= '<div class="alert-message warning"><i class="icon-flag"></i><p><span>'.__("Free","vbegy").'</span><br>'.$fourth_message.' "'.$coupons_v["coupon_name"].'"</p></div>';
					}
				}
			}
		}
		
		if ($active_coupons == 1) {
			$output .= '<div class="coupon_area">
				<form method="post" action="">
					<input type="text" name="coupon_name" id="coupon_name" value="" placeholder="Coupon code">
					<input type="submit" class="button" value="'.__("Apply Coupon","vbegy").'">
					<input type="hidden" name="add_coupon" value="submit">
				</form>
			</div>';
		}
		
		$output .= '<div class="clearfix"></div>';

		if (is_array($payment_methods) && !empty($payment_methods)) {
			$output .= '<div class="payment-methods">
				<h3 class="post-title-3"><i class="icon-credit-card"></i>Select Payment Method</h3>
				<div class="payment-wrap  payment-wrap-2 payment-wrap-'.$payment_item_name.'-'.$item_id.'">
					<div class="payment-tabs">
						<ul>';
							$k = 0;
							foreach ($payment_methods as $key => $value) {
								if ($value["value"] !== '0') {
									$k++;
									$tab_image = askme_image_url_id(askme_options($key."_tab_image"));
									$tab_image_width = askme_options($key."_tab_image_width");
									if ($key == "paypal") {
										if ($tab_image == "") {
											$image = '<img alt="'.$value["sort"].'" width="200" height="54" src="'.get_template_directory_uri().'/images/paypal.svg">';
										}
									}else if ($key == "stripe") {
										if ($tab_image == "") {
											$image = '<img width="100" height="54" alt="'.$value["sort"].'" src="'.get_template_directory_uri().'/images/mastercard.svg"><img width="100" height="54" alt="'.$value["sort"].'" src="'.get_template_directory_uri().'/images/visa.svg">';
										}
									}else if ($key == "bank") {
										if ($tab_image == "") {
											$image = '<img width="32" height="32" alt="'.$value["sort"].'" src="'.get_template_directory_uri().'/images/bank.png">';
										}
									}else if ($key == "custom") {
										if ($tab_image == "") {
											$image = '<img width="100" height="54" alt="'.($key == "custom"?askme_options("custom_payment_tab"):$value["sort"]).'" src="'.get_template_directory_uri().'/images/mastercard.svg">';
										}
									}else if ($key == "custom2") {
										if ($tab_image == "") {
											$image = '<img width="100" height="54" alt="'.($key == "custom2"?askme_options("custom_payment_tab2"):$value["sort"]).'" src="'.get_template_directory_uri().'/images/mastercard.svg">';
										}
									}else if ($key == "points") {
										if ($tab_image == "") {
											$image = '<img width="100" height="54" alt="'.$value["sort"].'" src="'.get_template_directory_uri().'/images/points.png">';
										}
									}else {
										$image = '<img alt="'.$value["sort"].'" src="'.get_template_directory_uri().'/images/mastercard.svg"><img alt="'.$value["sort"].'" src="'.get_template_directory_uri().'/images/visa.svg">';
									}
									if ($tab_image != "") {
										$image = '<img alt="'.$value["sort"].'" width="'.($tab_image_width != ""?$tab_image_width:50).'" height="54" src="'.$tab_image.'">';
									}
									$class = ($k == 1?"payment-style-activate":"");
									$output .= '<li class="payment-link-'.$key.'"><a href="payment-'.$key.'" class="'.$class." payment-".$key.'">'.(isset($image)?$image:"".($key == "custom"?askme_options("custom_payment_tab"):($key == "custom2"?askme_options("custom_payment_tab2"):$value["sort"]))).'</a></li>';
								}
							}
						$output .= '</ul>
						<div class="clearfix"></div>
					</div>';

					$k = 0;
					foreach ($payment_methods as $key => $value) {
						if ($value["value"] !== '0') {
							$k++;
						}
						if ($payment_methods[$key]["value"] == $key) {
							$output .= '<div class="payment-method payment-method-wrap payment-'.$key.($k == 1?"":" ask-hide").'" data-hide="payment-'.$key.'">';
						}
						if ($key == "points" && $payment_methods["points"]["value"] == "points") {
							$output .= '<div class="alert-message success"><p>'.$message.'</p></div>';
							if ($user_get_current_user_id > 0) {
								$points_user = get_user_meta($user_get_current_user_id,"points",true);
								if ($points_price <= $points_user) {
									$output .= '<div class="process_area">
										<form method="post" action="'.esc_url($action).'">
											<input type="submit" class="button" value="'.esc_attr__("Process","vbegy").'">
											<input type="hidden" name="process" value="'.$process_input.'">
											<input type="hidden" name="points" value="'.$points_price.'">
										</form>
									</div>';
								}else {
									$output .= '<div class="alert-message error"><p>'.esc_html__("Sorry, you haven't enough points","vbegy").'</p></div>';
								}
							}
						}else if ($key == "paypal" && $payment_methods["paypal"]["value"] == "paypal") {
							if ($last_payment > 0) {
								$paypal_logo = askme_image_url_id(askme_options("paypal_logo"));
								$output .= '<div class="payment_area">
									<form method="post" action="?action=process">
										<input type="hidden" name="custom" value="askme_'.$payment_item_name.'-'.$item_id.'">
										<input type="hidden" name="CatDescription" value="'.$item_name.'">
										<input type="hidden" name="item_number" value="'.$payment_item_name.'">
										<input type="hidden" name="payment" value="'.$last_payment.'">
										<input type="hidden" name="quantity" value="1">
										<input type="hidden" name="key" value="'.md5(date("Y-m-d:").rand()).'">
										<input type="hidden" name="go" value="paypal">
										'.$custom_input.'
										<input type="hidden" name="currency_code" value="'.$currency_code.'">
										'.(isset($coupon_name) && $coupon_name != ''?'<input type="hidden" name="coupon" value="'.$coupon_name.'">':'').'
										<input type="hidden" name="business" value="'.askme_options('paypal_email').'">
										<input type="hidden" name="return" value="'.esc_url(home_url('/')).'?action=success">
										<input type="hidden" name="cancel_return" value="'.esc_url(home_url('/')).'?action=cancel">
										<input type="hidden" name="notify_url" value="'.esc_url(home_url('/')).'?action=ipn">
										<input type="hidden" name="cpp_header_image" value="'.$paypal_logo.'">
										<input type="hidden" name="image_url" value="'.$paypal_logo.'">
										<input type="hidden" name="cpp_logo_image" value="'.$paypal_logo.'">
										<div class="form-submit">'.
											apply_filters('askme_filter_payment_button','<input type="submit" class="button-default btn btn__primary pay-button" value="'.esc_html__("Pay","vbegy").' '.$last_payment.' '.$currency_code.'">',$payment_item_name,$last_payment,$currency_code).'
										</div>
									</form>
								</div>';
							}else {
								$ask_find_coupons = ask_find_coupons($coupons,$data["coupon_name"]);
								$output .= '<div class="process_area">
									<form method="post" action="'.esc_url($action).'">
										<input type="submit" class="button" value="'.__("Process","vbegy").'">
										<input type="hidden" name="process" value="'.$process_input.'"">';
										if (isset($ask_find_coupons) && $ask_find_coupons != "" && $active_coupons == 1) {
											$output .= '<input type="hidden" name="coupon" value="'.esc_attr($data["coupon_name"]).'">';
										}
									$output .= '</form>
								</div>';
							}
						}else if ($key == "stripe" && $payment_methods["stripe"]["value"] == "stripe") {
							if ($last_payment > 0) {
								$stripe_address = askme_options("stripe_address");
								$stripe_inputs = '';
								if ($stripe_address == 1) {
									$get_countries = vpanel_get_countries();
									$line1 = get_the_author_meta("line1",$user_get_current_user_id);
									$postal_code = get_the_author_meta("postal_code",$user_get_current_user_id);
									$country = get_the_author_meta("country",$user_get_current_user_id);
									$city = get_the_author_meta("city",$user_get_current_user_id);
									$state = get_the_author_meta("state",$user_get_current_user_id);
									$stripe_inputs = '
									<div class="row row-boot row-warp">
										<div class="col-sm-8">
											<p class="line1_field">
												<label for="line1_'.$rand.'">'.esc_html__("Address","vbegy").'</label>
												<input type="text" class="form-control" value="'.esc_attr($line1).'" id="line1_'.$rand.'" name="line1">
											</p>
										</div>
										<div class="col-sm-4">
											<p class="postal_code_field">
												<label for="postal_code_'.$rand.'">'.esc_html__("ZIP","vbegy").'</label>
												<input type="text" class="form-control" value="'.esc_attr($postal_code).'" id="postal_code_'.$rand.'" name="postal_code">
											</p>
										</div>
									</div>
									<div class="row row-boot row-warp">
										<div class="col-sm-4">
											<p class="country_field">
												<label for="country_'.$rand.'">'.esc_html__("Country","vbegy").'</label>
												<span class="styled-select select-custom">
													<select class="form-control" name="country" id="country_'.$rand.'">
														<option value="">'.esc_html__( 'Select a country&hellip;', 'vbegy' ).'</option>';
															foreach( $get_countries as $key_country => $value_country ) {
																$stripe_inputs .= '<option value="' . esc_attr( $key_country ) . '"' . selected(esc_attr($country), esc_attr( $key_country ), false ) . '>' . esc_html( $value_country ) . '</option>';
															}
													$stripe_inputs .= '</select>
												</span>
											</p>
										</div>
										<div class="col-sm-4">
											<p class="city_field">
												<label for="city_'.$rand.'">'.esc_html__("City","vbegy").'</label>
												<input type="text" class="form-control" value="'.esc_attr($city).'" id="city_'.$rand.'" name="city">
											</p>
										</div>
										<div class="col-sm-4">
											<p class="state_field">
												<label for="state_'.$rand.'">'.esc_html__("State","vbegy").'</label>
												<input type="text" class="form-control" value="'.esc_attr($state).'" id="state_'.$rand.'" name="state">
											</p>
										</div>
									</div>';
								}

								$inputs = (isset($coupon_name) && $coupon_name != ''?'<input type="hidden" name="coupon" value="'.$coupon_name.'">':'').'
								<input type="hidden" name="custom" value="askme_'.$payment_item_name.'-'.$item_id.'">
								<input type="hidden" name="item_name" value="'.$item_name.'">
								<input type="hidden" name="item_number" value="'.$payment_item_name.'">'.
								apply_filters('askme_filter_payment_button','<input type="submit" class="button-default btn btn__primary pay-button" value="'.esc_html__("Pay","vbegy").' '.$last_payment.' '.$currency_code.'">',$payment_item_name,$last_payment,$currency_code);

								$output .= '<form action="" method="post" class="askme-stripe-payment-form vpanel_form" data-id="payment-stripe'.$rand.'">
									<div class="ask_error"></div>
									'.$stripe_inputs.'
									<div class="askme-stripe-payment" id="payment-stripe'.$rand.'" data-id="payment-stripe'.$rand.'"></div>
									<div class="form-submit">
										<span class="load_span"><span class="loader_2 search_loader"></span></span>
										'.$inputs.'
										<input type="hidden" value="'.get_the_author_meta("display_name",$user_get_current_user_id).'" name="name" class="name" required="" autofocus="">
										<input type="hidden" value="'.get_the_author_meta("user_email",$user_get_current_user_id).'" name="email" class="email" required="">
										<input type="hidden" name="payment" value="'.$last_payment.'">
										<input type="hidden" name="action" value="askme_stripe_payment">
										<input type="hidden" name="askme_stripe_nonce" value="'.wp_create_nonce("askme_stripe_nonce").'">
									</div>
								</form>';
							}else {
								$ask_find_coupons = ask_find_coupons($coupons,$data["coupon_name"]);
								$output .= '<div class="process_area">
									<form method="post" action="'.esc_url($action).'">
										<input type="submit" class="button" value="'.__("Process","vbegy").'">
										<input type="hidden" name="process" value="'.$process_input.'"">';
										if (isset($ask_find_coupons) && $ask_find_coupons != "" && $active_coupons == 1) {
											$output .= '<input type="hidden" name="coupon" value="'.esc_attr($data["coupon_name"]).'">';
										}
									$output .= '</form>
								</div>';
							}
						}else if ($key == "bank" && $payment_methods["bank"]["value"] == "bank") {
							$output .= do_shortcode(nl2br(stripslashes(askme_options("bank_transfer_details"))));
						}else if ($key == "custom" && $payment_methods["custom"]["value"] == "custom") {
							$output .= do_shortcode(nl2br(stripslashes(askme_options("custom_payment_details"))));
						}else if ($key == "custom2" && $payment_methods["custom2"]["value"] == "custom2") {
							$output .= do_shortcode(nl2br(stripslashes(askme_options("custom_payment_details2"))));
						}
						if ($payment_methods[$key]["value"] == $key) {
							$payment_image = askme_image_url_id(askme_options($key."_payment_image"));
							$output .= '</div><div class="payment_methods payment-method payment-'.$key.($k == 1?"":" ask-hide").'" data-hide="payment-'.$key.'"><img width="546" height="50" src="'.($payment_image != ""?$payment_image:get_template_directory_uri().'/images/payment_methods.png.').'" alt="payment_methods"></div>';
						}
					}
				$output .= '</div>';
				$custom_text_payment = askme_options("custom_text_payment");
				if ($custom_text_payment != "") {
					$output .= "<div class='custom-payment-div'>".do_shortcode(askme_kses_stip(nl2br(stripslashes($custom_text_payment))))."</div>";
				}
			$output .= '</div>';
		}
	}
	return $output;
}
/* Payment succeed */
function askme_payment_succeeded($user_id,$response,$redirect = "") {
	$paypal_sandbox   = askme_options('paypal_sandbox');
	$item_transaction = (isset($response['txn_id'])?esc_attr($response['txn_id']):"");
	$item_transaction = ($item_transaction != ""?$item_transaction:(isset($response['item_transaction'])?esc_attr($response['item_transaction']):""));
	$item_no          = (isset($response['item_number'])?esc_attr($response['item_number']):"");
	$item_no          = ($item_no != ""?$item_no:(isset($response['item_no'])?esc_attr($response['item_no']):""));
	$item_price       = (isset($response['mc_gross'])?esc_attr($response['mc_gross']):"");
	$item_price       = ($item_price != ""?$item_price:(isset($response['item_price'])?esc_attr($response['item_price']):""));
	$item_currency    = (isset($response['mc_currency'])?esc_attr($response['mc_currency']):"");
	$payer_email      = (isset($response['payer_email'])?esc_attr($response['payer_email']):"");
	$first_name       = (isset($response['first_name'])?esc_attr($response['first_name']):"");
	$last_name        = (isset($response['last_name'])?esc_attr($response['last_name']):"");
	$item_name        = (isset($response['item_name'])?esc_attr($response['item_name']):"");
	$payment          = (isset($response['payment'])?esc_attr($response['payment']):"");
	$custom           = (isset($response['custom'])?esc_attr($response['custom']):"");
	
	/* Coupon */
	$_coupon = get_user_meta($user_id,$user_id."_coupon",true);
	$_coupon_value = get_user_meta($user_id,$user_id."_coupon_value",true);
	
	/* Number of my payments */
	$_payments = get_user_meta($user_id,$user_id."_payments",true);
	if ($_payments == "") {
		$_payments = 0;
	}
	$_payments++;
	update_user_meta($user_id,$user_id."_payments",$_payments);
	
	add_user_meta($user_id,$user_id."_payments_".$_payments,
		array(
			"date_years" => date_i18n('Y/m/d',current_time('timestamp')),
			"date_hours" => date_i18n('g:i a',current_time('timestamp')),
			"item_number" => $item_no,
			"item_name" => $item_name,
			"item_price" => $item_price,
			"item_currency" => $item_currency,
			"item_transaction" => $item_transaction,
			"payer_email" => $payer_email,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"user_id" => $user_id,
			"sandbox" => ($payment != "Stripe" && $paypal_sandbox == 1?"sandbox":""),
			"time" => current_time('timestamp'),
			"coupon" => $_coupon,
			"coupon_value" => $_coupon_value
		)
	);
	
	/* New */
	$new_payments = get_option("new_payments");
	if ($new_payments == "") {
		$new_payments = 0;
	}
	$new_payments++;
	$update = update_option('new_payments',$new_payments);
	
	/* Money i'm paid */
	$_all_my_payment = get_user_meta($user_id,$user_id."_all_my_payment_".$item_currency,true);
	if($_all_my_payment == "" || $_all_my_payment == 0) {
		$_all_my_payment = 0;
	}
	update_user_meta($user_id,$user_id."_all_my_payment_".$item_currency,$_all_my_payment+$item_price);
	
	update_user_meta($user_id,$user_id."_last_payments",$item_transaction);
	
	/* All the payments */
	$payments_option = get_option("payments_option");
	if ($payments_option == "") {
		$payments_option = 0;
	}
	$payments_option++;
	update_option("payments_option",$payments_option);
	
	add_option("payments_".$payments_option,
		array(
			"date_years" => date_i18n('Y/m/d',current_time('timestamp')),
			"date_hours" => date_i18n('g:i a',current_time('timestamp')),
			"item_number" => $item_no,
			"item_name" => $item_name,
			"item_price" => $item_price,
			"item_currency" => $item_currency,
			"item_transaction" => $item_transaction,
			"payer_email" => $payer_email,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"user_id" => $user_id,
			"sandbox" => ($payment != "Stripe" && $paypal_sandbox == 1?"sandbox":""),
			"time" => current_time('timestamp'),
			"coupon" => $_coupon,
			"coupon_value" => $_coupon_value
		)
	);
	
	delete_user_meta($user_id,$user_id."_coupon",true);
	delete_user_meta($user_id,$user_id."_coupon_value",true);
	
	/* All money */
	$all_money = (int)get_option("all_money_".$item_currency);
	if($all_money == "" || $all_money == 0) {
		$all_money = 0;
	}
	update_option("all_money_".$item_currency,$all_money+$item_price);
	
	/* The currency */
	$the_currency = get_option("the_currency");
	if ((isset($the_currency) || !isset($the_currency)) && !is_array($the_currency)) {
		delete_option("the_currency");
		add_option("the_currency",array("USD"));
		$the_currency = get_option("the_currency");
	}
	$the_currency = (is_array($the_currency)?$the_currency:array());
	if (!in_array($item_currency,$the_currency)) {
		array_push($the_currency,$item_currency);
	}
	update_option("the_currency",$the_currency);
	
	$askme_payment_data = apply_filters("askme_filter_payment_data",true);
	if ($askme_payment_data == true) {
		if ($item_no == "pay_sticky") {
			$_question_sticky = str_replace("askme_pay_sticky-","",$custom);
			if ($_question_sticky == "") {
				$_question_sticky = get_user_meta($user_id,$user_id."_question_sticky",true);
			}
			update_post_meta($_question_sticky,"sticky",1);
			$sticky_questions = get_option('sticky_questions');
			if (is_array($sticky_questions)) {
				if (!in_array($_question_sticky,$sticky_questions)) {
					$array_merge = array_merge($sticky_questions,array($_question_sticky));
					update_option("sticky_questions",$array_merge);
				}
			}else {
				update_option("sticky_questions",array($_question_sticky));
			}
			$sticky_posts = get_option('sticky_posts');
			if (is_array($sticky_posts)) {
				if (!in_array($_question_sticky,$sticky_posts)) {
					$array_merge = array_merge($sticky_posts,array($_question_sticky));
					update_option("sticky_posts",$array_merge);
				}
			}else {
				update_option("sticky_posts",array($_question_sticky));
			}
			$days_sticky = (int)askme_options("days_sticky");
			$days_sticky = ($days_sticky > 0?$days_sticky:7);
			update_post_meta($_question_sticky,"start_sticky_time",strtotime(date("Y-m-d")));
			update_post_meta($_question_sticky,"end_sticky_time",strtotime(date("Y-m-d",strtotime(date("Y-m-d")." +$days_sticky days"))));
			delete_user_meta($user_id,$user_id."_question_sticky");
		}else {
			/* Number allow to ask question */
			$_allow_to_ask = get_user_meta($user_id,$user_id."_allow_to_ask",true);
			if ($_allow_to_ask == "" || $_allow_to_ask < 0) {
				$_allow_to_ask = 0;
			}
			$_allow_to_ask++;
			update_user_meta($user_id,$user_id."_allow_to_ask",$_allow_to_ask);
			
			/* Paid question */
			update_user_meta($user_id,"_paid_question","paid");
		}
	}
	
	update_user_meta($user_id,"item_transaction",$item_transaction);
	if ($payment != "Stripe" && $paypal_sandbox == 1) {
		update_user_meta($user_id,"paypal_sandbox","sandbox");
	}
	
	if ($askme_payment_data == true) {
		if ($item_no == "pay_sticky") {
			update_post_meta($_question_sticky, 'item_transaction_sticky', $item_transaction);
			if ($payment != "Stripe" && $paypal_sandbox == 1) {
				update_post_meta($_question_sticky, 'paypal_sandbox_sticky', 'sandbox');
			}
		}
		
		if ($redirect == "redirect") {
			echo '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Successfully payment","vbegy").'</span><br>'.($item_no == "pay_sticky"?__("Thank you for your payment, Your question now is sticky.","vbegy"):__("Thank you for your payment you now can make a new question.","vbegy")).'</p></div>';
		}

		$send_text = askme_send_mail(
			array(
				'content'          => askme_options("email_new_payment"),
				'item_price'       => $item_price,
				'item_name'        => $item_name,
				'item_currency'    => $item_currency,
				'payer_email'      => $payer_email,
				'first_name'       => $first_name,
				'last_name'        => $last_name,
				'item_transaction' => $item_transaction,
				'date'             => date('m/d/Y'),
				'time'             => date('g:i A'),
			)
		);
		$email_title = askme_options("title_new_payment");
		$email_title = ($email_title != ""?$email_title:esc_html__("Instant Payment Notification - Received Payment","vbegy"));
		$email_title = askme_send_mail(array(
				'content'          => $email_title,
				'title'            => true,
				'break'            => '',
				'item_price'       => $item_price,
				'item_name'        => $item_name,
				'item_currency'    => $item_currency,
				'payer_email'      => $payer_email,
				'first_name'       => $first_name,
				'last_name'        => $last_name,
				'item_transaction' => $item_transaction,
				'date'             => date('m/d/Y'),
				'time'             => date('g:i A'),
			)
		);
		askme_send_mails(
			array(
				'title'   => $email_title,
				'message' => $send_text,
			)
		);
		$email_template = askme_options("email_template");
		if ($payer_email != $email_template) {
			askme_send_mails(
				array(
					'toEmail'     => $payer_email,
					'toEmailName' => $first_name,
					'title'       => $email_title,
					'message'     => $send_text,
				)
			);
		}
		if(!session_id()) session_start();
		$_SESSION['vbegy_session_p'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Successfully payment","vbegy").'</span><br>'.($item_no == "pay_sticky"?__("Thank you for your payment, Your question now is sticky,","vbegy"):__("Thank you for your payment you now can make a new question.","vbegy")).' '.__("Your transaction id ".$item_transaction.", Please copy it.","vbegy").'</p></div>';
		if ($redirect == "redirect") {
			if ($item_no == "" || $item_no == "pay_ask" || $item_no == "ask_question") {
				wp_safe_redirect(esc_url(get_page_link(askme_options('add_question'))));
			}else if (isset($_question_sticky) && $_question_sticky != "") {
				wp_safe_redirect(esc_url(get_the_permalink($_question_sticky)));
			}else {
				wp_safe_redirect(esc_url(home_url('/')));
			}
		}
	}else if ($redirect == "redirect") {
		do_action("askme_payment_data_redirect",$response);
	}
}?>