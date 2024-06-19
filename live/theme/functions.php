<?php

/**
 * Logs output to console
 *
 * @return string
 */
function msr_cl( $output, $with_script_tags = true ) {
	$js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
	           ');';
	if ($with_script_tags) {
		$js_code = '<script>' . $js_code . '</script>';
	}
	echo $js_code;
}

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles');
function salient_child_enqueue_styles() {
	
		$nectar_theme_version = nectar_get_theme_version();
		
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('font-awesome'), $nectar_theme_version);
	wp_enqueue_style( 'modal-css', get_stylesheet_directory_uri() . '/css/jquery.modal.min.css', '', '', false );
	//wp_enqueue_style( 'parent-woo-style', get_template_directory_uri() . '/css/woocommerce.css', array('font-awesome'), $nectar_theme_version);

	if ( is_checkout() ) {
	}

	if (is_page_template('template_booking.php')) {
		wp_enqueue_style( 'custom-bootstrap-css', get_stylesheet_directory_uri() . '/css/bootstrap.min.css', '', '', false );
		wp_enqueue_style( 'custom-select2-css', get_stylesheet_directory_uri() . '/css/select2.min.css', '', '', false );
	}
	
    if ( is_rtl() ) 
   		wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
}

// Include meta box for the single story
require_once("Tax-meta-class/Tax-meta-class.php");
if (is_admin()) {
	$config = array(
	    'id' => 'demo_meta_box',
	    'title' => 'Demo Meta Box',
	    'pages' => array('product_cat'),
	    'context' => 'normal',
	    'fields' => array(),
	    'local_images' => false,
	    'use_with_theme' => true
	);
  
	$my_meta = new Tax_Meta_Class($config);
  	$my_meta->addText('group_id', array('name'=> __('Group ID ', 'salient')));
  	$my_meta->addText('triptype_id', array('name'=> __('Trip Type ID ', 'salient')));
	$my_meta->Finish();
}

// Include meta box for the single story
require_once("meta-box-class/my-meta-box-class.php");
if (is_admin()) {

	$config = array(
		'id'             => 'Extra Fields',
		'title'          => 'Extra Fields',
		'pages'          => array('portfolio'),
		'context'        => 'normal',
		'priority'       => 'high',
		'fields'         => array(),
		'local_images'   => false,
		'use_with_theme' => true
	);

	$my_meta =  new AT_Meta_Box($config);
	$my_meta->addText('category', array('name'=> __('Category', 'salient')));
	$my_meta->Finish();
}

// FIX SEO incompatibility with visual composer
/*add_filter( 'wpseo_replacements', function( $replacements ){
	global $post;

    if( ! sizeof($replacements) && ! empty($post->post_content) ){
        $replacements['%%excerpt%%'] = wp_html_excerpt( do_shortcode( $post->post_content ), 155 );
    }

    return $replacements;
});*/

// Make zip/postcode field optional
add_filter( 'woocommerce_default_address_fields' , 'QuadLayers_optional_postcode_checkout' );
function QuadLayers_optional_postcode_checkout( $p_fields ) {
	$p_fields['postcode']['required'] = false;
	$p_fields['address_1']['required'] = false;
	return $p_fields;
}

//
add_action( 'woocommerce_checkout_fields', 'woo_add_conditional_checkout_fields' );
function woo_add_conditional_checkout_fields( $fields ) {
	foreach( WC()->cart->get_cart() as $cart_item ) {
		$product_id = $cart_item['product_id'];

		foreach ($fields as $category => $value) {
			foreach ($fields[$category] as $field => $property) {
				unset($fields[$category][$field]['label']);
			}
		}

		unset($fields['billing']['billing_address_2']);

		$fields['billing']['billing_first_name']['placeholder'] = __( 'FIRST NAME*', 'salient' );
		$fields['billing']['billing_last_name']['placeholder'] = __( 'LAST NAME*', 'salient' );

		$fields['billing']['billing_company']['placeholder'] = __( 'COMPANY NAME', 'salient' );
		$fields['billing']['billing_company']['class'] = array('form-row-first');
		$fields['billing']['billing_country']['placeholder'] = __( 'COUNTRY*', 'salient' );
		$fields['billing']['billing_country']['class'] = array('form-row-last');

		$fields['billing']['billing_address_1']['placeholder'] = __( 'ADDRESS*', 'salient' );
		$fields['billing']['billing_address_1']['class'] = array('form-row-first');
		$fields['billing']['billing_city']['placeholder'] = __( 'CITY*', 'salient' );
		$fields['billing']['billing_city']['class'] = array('form-row-last');

		$fields['billing']['billing_state']['placeholder'] = __( 'STATE*', 'salient' );
		$fields['billing']['billing_state']['class'] = array('form-row-first');
		$fields['billing']['billing_state']['required'] = true;
		$fields['billing']['billing_postcode']['placeholder']  = __( 'POST CODE', 'salient' );
		$fields['billing']['billing_postcode']['class'] = array('form-row-last');

		$fields['billing']['billing_email']['placeholder'] = __( 'EMAIL*', 'salient' );
		$fields['billing']['billing_email']['class'] = array('form-row-first');
		$fields['billing']['billing_phone']['placeholder']  = __( 'PHONE*', 'salient' );
		$fields['billing']['billing_phone']['class'] = array('form-row-last');

		$fields['order']['order_comments']['placeholder'] = _x('Booking notes', 'placeholder', 'salient');
	}
	// Return checkout fields.
	return $fields;
}

//add_action('woocommerce_after_order_notes', 'custom_checkout_field');
function custom_checkout_field($checkout, $persons) {

	global $woocommerce;
	$index = 1;
	$isVariable = false;

	foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
		$product_id = $cart_item['product_id'];
		$_product = wc_get_product( $product_id );
	}
	
	$allVariations = array();
	$available_variations = $_product->get_children();
	$hotelvar = '';

	foreach ( WC()->cart->cart_contents as $prod_in_cart ) {
		foreach ($available_variations as $value) {
			if ($prod_in_cart['variation_id'] == $value) {
				$pr = new WC_Product_Variation($value);
				//$hotelType = $pr->get_attribute( 'pa_hotel-type' );
				//$meal = $pr->get_attribute( 'pa_meal' );
				//$extras = $_product->get_attribute( 'pa_extras' );

				/*if ($meal == 'BB') {
					$newMeal = 'πρωινό';
				}
				else if ($meal == 'HB') {
					$newMeal = 'ημιδιατροφή';
				}
				else
					$newMeal = 'ημιδ.+lift pass';*/
				
				$allVariations[$pr->get_id()] = $_product->get_name();
				$hotelvar = $pr->get_attribute( 'pa_hotel' );
			}
		}
	}

	if (sizeof($allVariations) == 0) {
		$allVariations[$product_id] = $_product->get_name();
	}
	else {
		$isVariable = true;

		$extras = wc_get_product_terms( $product_id, 'pa_extras', array( 'fields' => 'all' ) );
		$extrass = array();
		//
		foreach ($extras as $extra) {
			$extraArr = explode("-", $extra->name);
			$extrass[$extraArr[3]] = $extraArr[2];
		}
	}

	$keys = array_keys($allVariations);

	for ($i = 1; $i <= $persons; $i++) {

		$allPlaces = array();
		$allPlaces['0'] = __('DEPARTURE*', 'salient');
		$dtime1 = $_product->get_attribute( 'pa_departure-time-1' );
		$dtime2 = $_product->get_attribute( 'pa_departure-time-2' );
		$dtime3 = $_product->get_attribute( 'pa_departure-time-3' );
		$dtime4 = $_product->get_attribute( 'pa_departure-time-4' );
		$did1 = $_product->get_attribute( 'pa_departure-id-1' );
		$did2 = $_product->get_attribute( 'pa_departure-id-2' );
		$did3 = $_product->get_attribute( 'pa_departure-id-3' );
		$did4 = $_product->get_attribute( 'pa_departure-id-4' );

		if ($dtime1) {
			$allPlaces[$did1] = __('Kalamaria', 'salient') . ' ' . $dtime1; //array_push($allPlaces, __('Kalamaria', 'salient') . ' ' . $dtime1);
		}
		if ($dtime2) {
			$allPlaces[$did2] = __('City Center', 'salient') . ' ' . $dtime2;
		}
		if ($dtime3) {
			$allPlaces[$did3] = __('One Salonica', 'salient') . ' ' . $dtime3; //array_push($allPlaces, __('One Salonica', 'salient') . ' ' . $dtime3);
		}
		if ($dtime4) {
			$allPlaces[$did4] = __('Litochoro', 'salient') . ' ' . $dtime4;
		}
		
		echo '<div class="custom_checkout_field"><h3>' . __('Person', 'salient') . ' ' . $index . '</h3>';
		
		woocommerce_form_field('firstname_'.$index, array(
			'type' => 'text',
			'class' => array('form-row-first'),
			'placeholder' => __('FIRST NAME*', 'salient'),
			'required'      => true
		), $checkout->get_value('firstname_'.$index));

		woocommerce_form_field('lastname_'.$index, array(
			'type' => 'text',
			'class' => array('form-row-last'),
			'placeholder' => __('LAST NAME*', 'salient'),
			'required'      => true,
		), $checkout->get_value('lastname_'.$index));

		woocommerce_form_field('email_'.$index, array(
			'type' => 'text',
			'class' => array('form-row-first'),
			'placeholder' => __('EMAIL*', 'salient'),
			'required' => true,
		), $checkout->get_value('email_'.$index));

		woocommerce_form_field('departure_'.$index, array(
			'type' => 'select',
			'class' => array('form-row-last', 'custom-d'),
			'placeholder' => __('DEPARTURE*', 'salient'),
			'required' => true,
			'options' => $allPlaces,
		), $checkout->get_value('departure_'.$index));

		woocommerce_form_field('phone_'.$index, array(
			'type' => 'text',
			'class' => array('form-row-first'),
			'placeholder' => __('PHONE*', 'salient'),
			'required' => true,
		), $checkout->get_value('phone_'.$index));

		woocommerce_form_field('check_'.$index, array(
			'type' => 'checkbox',
			'class' => array('form-row-last'),
			'label' => '<span>' . __('I DECLARE THAT I AM OVER 18 YEARS OLD', 'salient') . '</span>',
			'required' => true,
		), $checkout->get_value('check_'.$index));

		woocommerce_form_field('variation_'.$index, array(
			'type' => 'select',
			'class' => $index < sizeof($allVariations) ? array('standard-var', 'form-row-first', 'custom-d', 'custom-var-person') : array('form-row-first', 'custom-d', 'custom-var-person'),
			'placeholder' => __('ROOM TYPE*', 'salient'),
			'required' => true,
			'options' => $allVariations,
			'default' => $keys[$i]
		), $checkout->get_value('variation_'.$index));

		//if (ICL_LANGUAGE_CODE == 'en') {
		if ($isVariable) {
			woocommerce_form_field('card_'.$index, array(
				'type' => 'text',
				'class' => array('form-row-last'),
				'placeholder' => __('CARD ID/ PASSPORT*', 'salient'),
				'required' => true,
			), $checkout->get_value('card_'.$index));
		}
		else {
			woocommerce_form_field('card_'.$index, array(
				'type' => 'text',
				'class' => array('form-row-last'),
				'placeholder' => __('CARD ID/ PASSPORT', 'salient'),
				'required' => false
			), $checkout->get_value('card_'.$index));
		}

		woocommerce_form_field('datebirth_'.$index, array(
			'type' => 'date',
			'class' => array('form-row-first', 'custom-datebirth'),
			'label' => __('DATE OF BIRTH', 'salient'),
			'required' => false,
		), $checkout->get_value('datebirth_'.$index));

		if ($isVariable) {
			for ($j=0; $j < sizeof($extras); $j++) {

				$extraArr = explode(";", $extras[$j]->name);
				if ($extraArr[0] != $hotelvar) 
					continue;

				if (sizeof($extraArr) > 4)
					$name = $extraArr[2] . $extraArr[3];
				else
					$name = $extraArr[2];

				woocommerce_form_field('check_extra_'.$j.$index, array(
					'type' => 'checkbox',
					'class' => array('form-row-last', 'extra-checkbox', str_replace(",","-",$extraArr[1]) ),
					'label' => '<span>' . $name . '</span>',
					'required' => false,
					'id' => $extraArr[sizeof($extraArr)-1]
				), $checkout->get_value('check_extra_'.$j.$index));
			}
			
		}

		woocommerce_form_field('comments_'.$index, array(
			'type' => 'textarea',
			'class' => array('form-row'),
			'placeholder' => __('COMMENTS', 'salient'),
			'required' => false,
		), $checkout->get_value('comments_'.$index));

		echo '</div><div class="clearfix"></div>';
		$index ++;
	}
}

add_action('woocommerce_before_order_notes', 'custom_checkout_field2');
function custom_checkout_field2($checkout) {
	global $woocommerce;
	
	//foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
		//$product_id = $cart_item['product_id'];
		//$_product = wc_get_product( $product_id );
		$allVariations = array();
		$allVariationsWithQuan = 0;
		foreach ( WC()->cart->cart_contents as $prod_in_cart ) {
			array_push($allVariations, $prod_in_cart['variation_id']);
			$allVariationsWithQuan += intval($prod_in_cart['quantity']);

			$product_id = $prod_in_cart['product_id'];
			$_product = wc_get_product( $product_id );
		}
		$available_variations = $_product->get_children();

		if (sizeof($available_variations) > 0) {
			/*foreach ($allVariations as $varid) {
				$pr = new WC_Product_Variation($varid);
				$adults = $pr->get_attribute( 'pa_max-persons' );
				$extraPerson = $pr->get_attribute( 'pa_children' );

				if ($extraPerson) {
					$total = intval($adults) + intval($extraPerson);
				}
				else {
					$total = intval($adults);
				}

				$personValues = array();
				for ($i = 1; $i <= $total; $i++) {
					$personValues[$i] = strval($i);
				}
			}*/
			//$testt = sizeof($allVariationsWithQuan);
			$personValues = array();
			$personValues[$allVariationsWithQuan] = strval($allVariationsWithQuan);
		}
		else {
			$cart_count = sizeof(array_unique($allVariations));
			$personValues = array();

			for ($i = $cart_count; $i <= 5; $i++) {
				$personValues[$i] = strval($i);
			}
		}

		woocommerce_form_field('persons', array(
			'type' => 'select',
			'class' => array('form-row-first', 'persons'),
			'label' => __('Persons*', 'salient'),
			'required' => true,
			'options' => $personValues,
			'default' => $personValues[$cart_count]//$cart_item['quantity']
		), $checkout->get_value('persons'));
	//}
} 

//
add_action('woocommerce_after_order_notes', 'custom_checkout_field3');
function custom_checkout_field3($checkout) {
	echo '<div class="info-message">**' . __('REMARKS', 'salient') . ':<br>' . __('Register your details in uppercase letters, like they are written on your ID or your passport. Be aware that you may have a problem to pass the borders or to check in your flight, in case your details are not written correctly. The travel agency is not responsible in case you register wrong details with result to have a problem to pass the borders or to have a penalty fee in order to correct your airplane ticket. The number of your ID or passport are required ONLY for the international trips/excursions.', 'salient') . '</div>';
	echo '<div class="custom_checkout_continue">' . __('Next', 'salient') . '</div>';
} 

//
add_action( 'woocommerce_checkout_update_user_meta', 'my_custom_checkout_field_update_user_meta' );
function my_custom_checkout_field_update_user_meta( $user_id ) {

	if ( empty($_POST['persons']) ) {
		return;
	}

	$persons = intval($_POST['persons']);
	update_user_meta( $user_id, 'persons', sanitize_text_field( $_POST['persons'] ));

	for ($i = 1; $i <= $persons; $i++) {

		if ( !empty( $_POST['firstname_' . $i] ) ) {
			update_user_meta( $user_id, 'firstname_' . $i, strtoupper(sanitize_text_field( $_POST['firstname_' . $i] )));
		}
		if ( !empty( $_POST['lastname_' . $i] ) ) {
			update_user_meta( $user_id, 'lastname_' . $i, strtoupper(sanitize_text_field( $_POST['lastname_' . $i] )));
		}
		if ( !empty( $_POST['email_' . $i] ) ) {
			update_user_meta( $user_id, 'email_' . $i, sanitize_text_field( $_POST['email_' . $i] ));
		}
		if ( !empty( $_POST['departure_' . $i] ) ) {
			update_user_meta( $user_id, 'departure_' . $i, sanitize_text_field( $_POST['departure_' . $i] ));
		}
		if ( !empty( $_POST['datebirth_' . $i] ) ) {
			update_user_meta( $user_id, 'datebirth_' . $i, sanitize_text_field( $_POST['datebirth_' . $i] ));
		}
		if ( !empty( $_POST['check_' . $i] ) ) {
			update_user_meta( $user_id, 'check_' . $i, sanitize_text_field( $_POST['check_' . $i] ));
		}
		if ( !empty( $_POST['phone_' . $i] ) ) {
			update_user_meta( $user_id, 'phone_' . $i, sanitize_text_field( $_POST['phone_' . $i] ));
		}
		if ( !empty( $_POST['card_' . $i] ) ) {
			update_user_meta( $user_id, 'card_' . $i, sanitize_text_field( $_POST['card_' . $i] ));
		}
		if ( !empty( $_POST['comments_' . $i] ) ) {
			update_user_meta( $user_id, 'comments_' . $i, sanitize_text_field( $_POST['comments_' . $i] ));
		}
		/*if ( !empty( $_POST['variation_' . $i] ) ) {
			update_user_meta( $user_id, 'variation_' . $i, sanitize_text_field( $_POST['variation_' . $i] ));
		}*/

	}
}

add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta2' );
function my_custom_checkout_field_update_order_meta2( $order_id ) {
	
    if ( empty($_POST['persons']) ) {
		return;
	}
	
	if (!empty( $_POST['billing_first_name'] ))
		update_post_meta( $order_id, '_billing_first_name', strtoupper(sanitize_text_field( $_POST['billing_first_name'] )) );
		
	if (!empty( $_POST['billing_last_name'] ))
		update_post_meta( $order_id, '_billing_last_name', strtoupper(sanitize_text_field( $_POST['billing_last_name'] )) );

	if (!empty( $_POST['order_comments'] )) {
		$order_data = array(
			'order_id' => $order_id,
			'customer_note' => str_replace( array('"', ':' , '{', '}'), ' ', sanitize_text_field( $_POST['order_comments'] ))
		);
		wc_update_order( $order_data );
	}

	$persons = intval($_POST['persons']);
	update_post_meta( $order_id, 'persons', sanitize_text_field( $_POST['persons'] ));

	for ($i = 1; $i <= $persons; $i++) {

		if ( !empty( $_POST['firstname_' . $i] ) ) {
			update_post_meta( $order_id, 'firstname_' . $i, strtoupper(sanitize_text_field( $_POST['firstname_' . $i] )));
		}
		if ( !empty( $_POST['lastname_' . $i] ) ) {
			update_post_meta( $order_id, 'lastname_' . $i, strtoupper(sanitize_text_field( $_POST['lastname_' . $i] )));
		}
		if ( !empty( $_POST['email_' . $i] ) ) {
			update_post_meta( $order_id, 'email_' . $i, sanitize_text_field( $_POST['email_' . $i] ));
		}
		if ( !empty( $_POST['departure_' . $i] ) ) {
			update_post_meta( $order_id, 'departure_' . $i, sanitize_text_field( $_POST['departure_' . $i] ));
		}
		if ( !empty( $_POST['datebirth_' . $i] ) ) {
			update_post_meta( $order_id, 'datebirth_' . $i, sanitize_text_field( $_POST['datebirth_' . $i] ));
		}
		if ( !empty( $_POST['check_' . $i] ) ) {
			update_post_meta( $order_id, 'check_' . $i, sanitize_text_field( $_POST['check_' . $i] ));
		}
		if ( !empty( $_POST['phone_' . $i] ) ) {
			update_post_meta( $order_id, 'phone_' . $i, sanitize_text_field( $_POST['phone_' . $i] ));
		}
		if ( !empty( $_POST['card_' . $i] ) ) {
			update_post_meta( $order_id, 'card_' . $i, sanitize_text_field( $_POST['card_' . $i] ));
		}
		if ( !empty( $_POST['comments_' . $i] ) ) {
			update_post_meta( $order_id, 'comments_' . $i, str_replace( array('"', ':' , '{', '}'), ' ', sanitize_text_field( $_POST['comments_' . $i] )) );
			
		}
		if ( !empty( $_POST['variation_' . $i] ) ) {
			update_post_meta( $order_id, 'variation_' . $i, sanitize_text_field( $_POST['variation_' . $i] ));
		}

	}
}

//
add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );
function change_default_checkout_country() {
  return 'GR';
}

//
add_filter( 'woocommerce_gateway_icon', 'custom_payment_gateway_icons', 10, 2 );
function custom_payment_gateway_icons( $icon, $gateway_id ){

    foreach ( WC()->payment_gateways->get_available_payment_gateways() as $gateway )
        if ( $gateway->id == $gateway_id ){
            $title = $gateway->get_title();
            break;
        }

    $path = get_stylesheet_directory_uri(). '/images';

    if ($gateway_id == 'bacs')
        $icon = '<img src="' . WC_HTTPS::force_https_url( "$path/bacs.png" ) . '" alt="' . esc_attr( $title ) . '" />';
    elseif ($gateway_id == 'payinstore' )
		$icon = '<img src="' . WC_HTTPS::force_https_url( "$path/payinstore.png" ) . '" alt="' . esc_attr( $title ) . '" />';
	elseif ($gateway_id == 'paypal' )
        $icon = '<img src="' . WC_HTTPS::force_https_url( "$path/paypal.png" ) . '" alt="' . esc_attr( $title ) . '" />';

    return $icon;
}

//
add_filter( 'woocommerce_add_to_cart_redirect', 'salient_skip_cart_redirect_checkout' );
function salient_skip_cart_redirect_checkout( $url ) {
    return wc_get_checkout_url();
}

//
function add_quanity_js(){
	wp_enqueue_script( 'custom-init-js', get_stylesheet_directory_uri() . '/js/custominit.js', '', '', false );
	$localize_script = array(
		'ajax_url' => admin_url( 'admin-ajax.php' )
	);
	wp_localize_script( 'custom-init-js', 'custominit', $localize_script );

	if ( is_checkout() ) {
		wp_enqueue_script( 'checkout_script', get_stylesheet_directory_uri() . '/js/add_quantity.js', '', '', false );
		$localize_script = array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'checkout_script', 'add_quantity', $localize_script );
		wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array(), $nectar_theme_version, true );
	}

	if (is_page_template('template_booking.php')) {
		wp_enqueue_script( 'booking_script', get_stylesheet_directory_uri() . '/js/booking.js', '', '', false );
		$localize_script = array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'booking_script', 'booking', $localize_script );
		wp_enqueue_script( 'booking-select2-js', get_stylesheet_directory_uri() . '/js/select2.min.js', array(), $nectar_theme_version, true );
		//wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array(), $nectar_theme_version, true );
	}

	wp_enqueue_script( 'modal-js', get_stylesheet_directory_uri() . '/js/jquery.modal.min.js', array(), $nectar_theme_version, true );
}
add_action( 'wp_footer', 'add_quanity_js', 10 );

//
add_action( 'wp_ajax_update_order_review', 'update_order_review' );
add_action( 'wp_ajax_nopriv_update_order_review', 'update_order_review' );
//
function update_order_review() {
	
	/*foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        WC()->cart->set_quantity( $cart_item_key, intval($_POST['post_data']) ); // Change quantity
	}
	
	WC_AJAX::get_refreshed_fragments();
	wp_die();*/
	custom_checkout_field(WC()->checkout, intval($_POST['post_data']));
	exit();
}
//
add_action( 'wp_ajax_update_order_test', 'update_order_test' );
add_action( 'wp_ajax_nopriv_update_order_test', 'update_order_test' );
//
function update_order_test() {
	
	foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        WC()->cart->set_quantity( $cart_item_key, intval($_POST['post_data']) ); // Change quantity
	}
	
	WC_AJAX::get_refreshed_fragments();
	wp_die();
}

//
add_action( 'wp_ajax_get_price', 'get_price' );
add_action( 'wp_ajax_nopriv_get_price', 'get_price' );
//
function get_price() {
	$data = $_POST['post_data'];

	/*
    children313
    children512
    children213
    children212
    children12150
    children13150
    */
	
	$product = wc_get_product( $data[0] );
	$variations = $product->get_available_variations();
	$ids = array();
	$types = array();
	$typeNames = array();
	$prices = array();
	$quantities = array();

	$typesPre = array();
	$typeNamesPre = array();
	$pricesPre = array();
	$quantitiesPre = array();

	//$skus = array();
	$limits = array();
	$adults = intval($data[2]);
	$children313 = intval($data[3]);
	$children512 = intval($data[4]);
	$children213 = intval($data[5]);
	$children212 = intval($data[6]);
	$children12150 = intval($data[7]);
	$children13150 = intval($data[8]);
	
	if ($children313) {
		array_push($limits, '313');
	}
	if ($children512) {
		array_push($limits, '512');
	}
	if ($children213) {
		array_push($limits, '213');
	}
	if ($children212) {
		array_push($limits, '212');
	}
	if ($children12150) {
		array_push($limits, '12150');
	}
	if ($children13150) {
		array_push($limits, '13150');
	}

	$total = $adults + $children313 + $children512 + $children213 + $children212 + $children12150 + $children13150;
	$totalchildren = 0;
	$totalchildren = $children313 + $children512 + $children213 + $children212 + $children12150 + $children13150;

	foreach ($variations as $variation) {
		$attrs = $variation['attributes'];
		$totalVar = intval($attrs['attribute_pa_total-persons']);
		$minPersonsVar = intval($attrs['attribute_pa_min-persons']);
		$maxPersonsVar = intval($attrs['attribute_pa_max-persons']);
		$children = $attrs['attribute_pa_children'];
		$priceVar = intval($variation['display_regular_price']);
		$totalPrice = 0;

		if ($attrs['attribute_pa_hotel'] == $data[1] && $totalVar >= $total && $minPersonsVar <= $adults && $maxPersonsVar >= $adults && $children == '0') {
			$term = get_term_by('slug', $attrs['attribute_pa_room-type'], 'pa_room-type');

			array_push($typesPre, $variation['variation_id']);
    		array_push($typeNamesPre, $term->name);

    		$totalPrice = $adults * $priceVar;
    		array_push($pricesPre, $totalPrice);
    		array_push($quantitiesPre, $adults);
		}
	}

	//
	if ($totalchildren) {
		foreach ($typeNamesPre as $key => $typename) {

			foreach ($variations as $variation) {
				$attrs = $variation['attributes'];
				$children = $attrs['attribute_pa_children'];
				$term = get_term_by('slug', $attrs['attribute_pa_room-type'], 'pa_room-type');

				if ($typename == $term->name && in_array($children, $limits) && $attrs['attribute_pa_hotel'] == $data[1] && !in_array($typename, $typeNames) ) {
					array_push($types, $typesPre[$key]);
					array_push($typeNames, $typename);
					array_push($prices, $pricesPre[$key]);
					array_push($quantities, $quantitiesPre[$key]);
				}
			}
		}
	}
	else {
		$types = $typesPre;
		$typeNames = $typeNamesPre;
		$prices = $pricesPre;
		$quantities = $quantitiesPre;
	}

	//
	foreach ($variations as $variation) {
		$attrs = $variation['attributes'];
		//$totalVar = intval($attrs['attribute_pa_total-persons']);
		$minPersonsVar = intval($attrs['attribute_pa_min-persons']);
		$maxPersonsVar = intval($attrs['attribute_pa_max-persons']);
		$children = $attrs['attribute_pa_children'];
		$priceVar = intval($variation['display_regular_price']);
		$totalPrice = 0;

		$allchildren = 0;
		foreach ($limits as $limit) {
			if ($limit == $children) {
				$allchildren = intval(${"children" . $limit});
			}
		}
		
		if ($attrs['attribute_pa_hotel'] == $data[1] && $maxPersonsVar >= $allchildren && $minPersonsVar <= $allchildren && in_array($children, $limits)) {
    		
    		$term = get_term_by('slug', $attrs['attribute_pa_room-type'], 'pa_room-type');
    		$keyterm = array_search($term->name, $typeNames, true);
    		//var_dump($keyterm);

    		if ( is_numeric($keyterm) ) {
    			$types[$keyterm] = $types[$keyterm] . '-' . $variation['variation_id'];

    			$totalPrice = $allchildren * $priceVar;
    			$prices[$keyterm] = $prices[$keyterm] + $totalPrice;
    			$quantities[$keyterm] = $quantities[$keyterm] . '-' . $allchildren;
    		}
    		
    	}
       
    }
    
    $returnData = array();
    $returnData['types'] = $types;
    $returnData['typeNames'] = $typeNames;
    $returnData['prices'] = $prices;
    $returnData['quantities'] = $quantities;
    echo json_encode($returnData);
    exit();
}

//
add_action( 'wp_ajax_get_children_limits', 'get_children_limits' );
add_action( 'wp_ajax_nopriv_get_children_limits', 'get_children_limits' );
//
function get_children_limits() {
	$data = $_POST['post_data'];
	
	$product = wc_get_product( $data[0] );
	$variations = $product->get_available_variations();
	$children = array();
	//$adults = intval($data[2]);

	foreach ($variations as $variation) {
		$attrs = $variation['attributes'];
		$minPersonsVar = intval($attrs['attribute_pa_min-persons']);
		$maxPersonsVar = intval($attrs['attribute_pa_max-persons']);

		if ($attrs['attribute_pa_hotel'] == $data[1] && !in_array($attrs['attribute_pa_children'], $children)) { //&& $maxPersonsVar >= $adults && $minPersonsVar <= $adults
    		array_push($children, $attrs['attribute_pa_children']);
    	}
    }
    
    $returnData = array();
    $returnData['children'] = $children;
    echo json_encode($returnData);
    exit();
}

//
//
add_action( 'wp_ajax_get_booking_cart', 'get_booking_cart' );
add_action( 'wp_ajax_nopriv_get_booking_cart', 'get_booking_cart' );
//
function get_booking_cart() {
	$data = $_POST['post_data'];

	$product_id = $data[0];
	$variations_ids = explode('-', $data[1]);//array('37630','37630');
	$quantities = explode('-', $data[2]);//array('1','1');
	//$add_cart = $post_data_arr[2];
	$product = wc_get_product($product_id);
	$variations = $product->get_available_variations();	

	WC()->cart->empty_cart();

	foreach ($variations as $variation) {

		for ($i = 0; $i < sizeof($variations_ids); $i++) {
			
			if ( $variations_ids[$i] == strval($variation['variation_id'])) {

				$variation_id = $variations_ids[$i];
				$attributes = $variation['attributes'];
				$arr = array();
		
				if ( !empty($attributes) ) {
					foreach ($attributes as $key => $value) {
						$arr[$key] = $value;
					}
				}
				
				WC()->cart->add_to_cart( $product_id, $quantities[$i], $variation_id, $arr);
			}
		}
	}

	$link = apply_filters( 'wpml_object_id', 21223, 'post', TRUE );
	echo get_permalink($link);

	exit();
}

//
add_filter('woocommerce_add_to_cart_fragments','set_ajax_fragments',10,1);
function set_ajax_fragments($fragments){
	//ob_start();
	//custom_checkout_field(WC()->checkout);
	//$fragments['div.custom_checkout_field'] = ob_get_clean();
	return $fragments;
}

//
add_action( 'wp_ajax_update_order_review_cart', 'update_order_review_cart' );
add_action( 'wp_ajax_nopriv_update_order_review_cart', 'update_order_review_cart' );
//
function update_order_review_cart() {
	$variations_ids = $_POST['post_data'];

	foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		
		$id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];	
		$quan = array_keys($variations_ids, strval($id));
		//var_dump($variations_ids);
		//var_dump(strval($id));
		//var_dump($quan);
		
		WC()->cart->set_quantity( $cart_item['key'], sizeof($quan) );
	}

	WC_AJAX::get_refreshed_fragments();
	wp_die();
}

//
add_action( 'wp_ajax_update_order_review_extra', 'update_order_review_extra' );
add_action( 'wp_ajax_nopriv_update_order_review_extra', 'update_order_review_extra' );
//
function update_order_review_extra() {
	$fee = $_POST['post_data'];

	WC()->session->set( 'extra_fee', $fee );
	//do_action( 'woocommerce_cart_calculate_fees' );

	//WC()->cart->add_fee(__('Extras', 'salient-child'), $fee);

	WC_AJAX::get_refreshed_fragments();
	wp_die();
}

//
add_filter( 'woocommerce_add_to_cart_validation', 'one_cart_item_at_the_time', 10, 3 );
function one_cart_item_at_the_time( $passed, $product_id, $quantity ) {
	$product = wc_get_product($product_id);
	$product_parent=$product->get_parent_id();

    if( ! WC()->cart->is_empty() && !$product_parent )
		WC()->cart->empty_cart();
		
    return $passed;
}

//
add_action( 'template_redirect', 'salient_redirect_empty_cart_checkout_to_home' );
function salient_redirect_empty_cart_checkout_to_home() {
   if ( is_cart() && is_checkout() && 0 == WC()->cart->get_cart_contents_count() && ! is_wc_endpoint_url( 'order-pay' ) && ! is_wc_endpoint_url( 'order-received' ) ) {
      wp_safe_redirect( home_url() );
      exit;
   }
}

//
function product_variations_shortcode( $atts ) {
	/*$props = shortcode_atts( array(
	   'destination' => null
	), $atts );*/

	if (!get_post_meta(get_the_ID(), 'category')) {
		return false;
	}

	if ( !sizeof(get_post_meta(get_the_ID(), 'category')) ) {
		return false;
	}

	$products = new WP_Query( array(
		'post_type'      => array('product'),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'tax_query'      => array( array(
			'taxonomy'        => 'product_cat',
			'field'           => 'slug',
			'terms'           =>  get_post_meta(get_the_ID(), 'category'),
			'operator'        => 'IN',
		))
	));
	
	if ( $products->have_posts() ): while ( $products->have_posts() ):
		$products->the_post();
		$product_ids[] = $products->post->ID;
	endwhile;
		wp_reset_postdata();
	endif;
	
	$html = '<div class="all-custom-tables">';

	foreach ($product_ids as $id) {
		$product = wc_get_product( (int)$id);
		if (!$product) {
			return false;
		}

		$hotels = explode(',', $x = preg_replace('/\s*,\s*/', ',', $product->get_attribute( 'pa_hotel' ) ));
		$available_variations = $product->get_children();
		
		foreach ($hotels as $hotel) {
			$html = $html . '<h3>'. $hotel .'</h3>';
			
			$types = array();
			$meals = array();
			$newMeals = array('', '', '');
			$tableValues = array();
			$ids = array();
			$rooms = array();
			
			foreach ($available_variations as $key => $value) {
				$pr = new WC_Product_Variation($value);

				if ($pr->get_attribute( 'pa_hotel' ) == $hotel) {
					$hotelType = $pr->get_attribute( 'pa_hotel-type' );
					$meal = $pr->get_attribute( 'pa_meal' );

					if (array_search($hotelType, $types) === false) {
						array_push($types, $hotelType);
					}

					if (array_search($meal, $meals) === false) {
						
						if ($meal == 'BB') {
							$newMeals[0] = __('Breakfast', 'salient');
							//array_push($newMeals, 'πρωινό');
						}
						else if ($meal == 'HB') {
							$newMeals[1] = __('Half board', 'salient');
							//array_push($newMeals, 'ημιδιατροφή');
						}
						else {
							$newMeals[2] = __('Half board + lift pass', 'salient');
							//array_push($newMeals, 'ημιδ.+lift pass');
						}

						if (array_search($meal, $meals) === false) {
							array_push($meals, $meal);
						}
					}
				}
			}

			$newMeals = array_values(array_filter($newMeals));
			sort($types);
			sort($meals);

			foreach ($available_variations as $key => $value) {
				$pr = new WC_Product_Variation($value);

				if ($pr->get_attribute( 'pa_hotel' ) == $hotel) {
					$hotelType = $pr->get_attribute( 'pa_hotel-type' );
					$meal = $pr->get_attribute( 'pa_meal' );

					$tableValues[array_search($hotelType, $types)][array_search($meal, $meals)] = $pr->get_price();
					$ids[array_search($hotelType, $types)][array_search($meal, $meals)] = $pr->get_id();
					$rooms[array_search($hotelType, $types)][array_search($meal, $meals)] = $pr->get_attribute( 'pa_max-rooms' );
				}
			}

			$html = $html . '<div class="custom-table-products-wrap"><table class="custom-table-products">';
			$colspanHead = ceil((sizeof($meals) + 2)/2);
			$html = $html . '<thead><tr><th colspan="' . $colspanHead . '">' . __('Prices | Departures', 'salient') . '</th><th colspan="' . (sizeof($meals) + 2 - $colspanHead) . '">' . 
				date('d M', strtotime($product->get_attribute( 'pa_start-date' ))) . ' - ' .
				date('d M', strtotime($product->get_attribute( 'pa_end-date' ))) . 
				'</th></tr></thead>';
			
			$html = $html . '<tbody><tr><td></td>';
			for ($i = 0; $i < sizeof($newMeals); $i++) {
				$html = $html . '<td>' . $newMeals[$i] . '</td>';
			}
			$html = $html . '<td>'.__('Available seats', 'salient').'</td>';
			$html = $html . '</tr>';

			$className = '';
			$first_id = 0;
			$first_checked_input = true;

			for ($row = 0; $row < sizeof($tableValues); $row++) {
				//Create line 1
				$createLine = false;
				for ($col = 0; $col < count($tableValues[0]); $col++) {
					if ($tableValues[$row][$col]) {
						$createLine = true;
					}
				}
				if (!$createLine) {
					continue;
				}

				//Create line 2
				$createLine = false;
				for ($col = 0; $col < count($tableValues[0]); $col++) {
					if ($rooms[$row][$col]) {
						$createLine = true;
					}
				}
				if (!$createLine) {
					continue;
				}
				//

				if ($className == 'even') {
					$className = '';
				}
				else {
					$className = 'even';
				}

				$html = $html . '<tr class="' . $className . '">';
				$html = $html . '<td class="td-type">' . $types[$row] . '</td>';

				//
				for ($col = 0; $col < count($tableValues[0]); $col++) {
					$checked = '';
					if ($first_checked_input) 
						$checked = 'checked';

					if ($tableValues[$row][$col] && $rooms[$row][$col]) {
						$html = $html . '<td>' . $tableValues[$row][$col] . '&euro;  <label class="input-wrapper"><input '. $checked .' type="checkbox" name="'. $product->get_id() .'" value="'. $ids[$row][$col]  .'" '.($product->get_stock_quantity() ? '' : 'disabled').'><span class="checkmark"></span></label></td>';
						$first_checked_input = false;
					}
					else {
						$html = $html . '<td></td>';
					}

					//
					if (!$first_id) {
						$first_id = $ids[$row][$col];
					}
				}

				//
				if ($product->get_stock_quantity() == 0) {
					$stockHtml = '<div class="stock-status"><div class="grey"></div><div class="grey"></div><div class="red"></div></div>';
				}
				else if ($product->get_stock_quantity() <= 10) {
					$stockHtml = '<div class="stock-status"><div class="grey"></div><div class="orange"></div><div class="grey"></div></div>';
				}
				else {
					$stockHtml = '<div class="stock-status"><div class="green"></div><div class="grey"></div><div class="grey"></div></div>';
				}
				$html = $html . '<td>' . $stockHtml . '</td>';

				$html = $html . '</tr>';
			}
			$html = $html . '</tbody></table></div>';

			//Departures
			$html = $html . '<div class="custom-table-products-wrap"><div class="custom-departures"><h2>_'. __('departures', 'salient') .'</h2>';//αναχωρήσεις
			$html = $html . '<table><thead><tr><th>'.__('Departure day', 'salient').':</th><th>'.__('Kalamaria (Chilis 12)', 'salient').'</th><th>'.__('City center (Tsimiski 115)', 'salient').'</th><th>'.__('One Salonica (Koleti)', 'salient').'</th></tr></thead><tbody>';
			$html = $html . '<tr>';
			$html = $html . '<td>'. date('l d F Y', strtotime($product->get_attribute( 'pa_start-date' ))) .'</td>';
			$html = $html . '<td>'. $product->get_attribute( 'pa_departure-time-1' ) .'</td>';
			$html = $html . '<td>'. $product->get_attribute( 'pa_departure-time-2' ) .'</td>';
			$html = $html . '<td>'. $product->get_attribute( 'pa_departure-time-3' ) .'</td>';
			$html = $html . '<td>'. $product->get_attribute( 'pa_departure-time-4' ) .'</td>';
			$html = $html . '</tr>';
			$html = $html . '</tbody></table><table class="table-custom-mob"><tbody>';

			$html = $html . '<tr><td>'.__('Departure day', 'salient').':</td><td>'. date('l d F Y', strtotime($product->get_attribute( 'pa_start-date' ))) .'</td></tr>';
			$html = $html . '<tr><td>'.__('Kalamaria (Chilis 12)', 'salient').':</td><td>'. $product->get_attribute( 'pa_departure-time-1' ) .'</td></tr>';
			$html = $html . '<tr><td>'.__('City center (Tsimiski 115)', 'salient').':</td><td>'. $product->get_attribute( 'pa_departure-time-2' ) .'</td></tr>';
			$html = $html . '<tr><td>'.__('One Salonica (Koleti)', 'salient').':</td><td>'. $product->get_attribute( 'pa_departure-time-3' ) .'</td></tr>';
			$html = $html . '<tr><td>'.__('Litochoro', 'salient').':</td><td>'. $product->get_attribute( 'pa_departure-time-4' ) .'</td></tr>';

			$html = $html . '</tbody></table></div></div>';
			//Extras
			$extras = explode(',', $product->get_attribute( 'pa_extras' ));
			$ths = array();
			$tds = array();

			foreach ($extras as $extra) {
				$parts = explode('-', $extra);
				$last = array_pop($parts);
				$parts = array(implode('-', $parts), $last);

				if (sizeof($parts) == 2) {
					array_push($ths, $parts[0]);
					array_push($tds, $parts[1]);
				}
			}

			$html = $html . '<div class="custom-extras"><h2>_'. __('extra services', 'salient') .'</h2>';
			$html = $html . '<div class="custom-table-products-wrap"><table><thead>';
			$html = $html . '<tr>';
			for ($j = 0; $j < sizeof($ths); $j++) {
				$html = $html . '<th>'. $ths[$j] .'</th>';
			}
			$html = $html . '</tr></thead><tbody>';

			for ($i = 0; $i < sizeof($parts)-1; $i++) {
				$html = $html . '<tr>';
				for ($j = 0; $j < sizeof($tds); $j++) {
					$html = $html . '<td>'. $tds[$j] .'€</td>';
				}
				$html = $html . '</tr>';
			}
			
			$html = $html . '</tbody></table><table class="table-custom-mob"><tbody>';

			for ($j = 0; $j < sizeof($tds); $j++) {
				$html = $html . '<tr><td>'. $ths[$j] .'</td><td>'. $tds[$j] .'€</td></tr>';
			}

			$html = $html . '</tbody></table></div></div>';
			$html = $html . '<div class="addToCartButton"><div class="button '.($product->get_stock_quantity() ? '' : 'disabled').'" data-product_id="'.$product->get_id().'" data-variation_id="'.$first_id.'" > '.__('Reserve Now', 'salient').'</div></div><br/><br/>';
			
			/*
			$id = $ids[0][0];
			$addToCartUrl = do_shortcode('[add_to_cart_url id="'.$id.'"]');
			$cartUrl = wc_get_cart_url();
			$sku = $product->get_sku();
			$html = $html . '<div class="addToCartButton"><a href="'.$addToCartUrl.'" class="button '.($product->get_stock_quantity() ? '' : 'disabled').'" data-quantity="1" data-product_id="'.$product->get_id().'" data-variation_id="'.$id.'" data-product_sku="'.$sku.'"> '.__('Reserve Now', 'salient').'</a><a href="'.$cartUrl.'" class="added_to_cart wc-forward stHidden" title="View cart"></a></div><br/><br/>';
			*/
		}
	}
	
	$html = $html . '</div>';

	return $html;
}
add_shortcode('product_variations', 'product_variations_shortcode');

//
add_action( 'wp_ajax_update_custom_cart', 'update_custom_cart' );
add_action( 'wp_ajax_nopriv_update_custom_cart', 'update_custom_cart' );
//
function update_custom_cart() {
	
	$post_data = $_POST['post_data'];
	$post_data_arr = explode("-", $post_data);
	$variation_id = $post_data_arr[0];
	$variations_ids = explode(",", $post_data_arr[1]);
	$add_cart = $post_data_arr[2];

	if ($post_data_arr[1] && sizeof($variations_ids) > 0) {

		if ($add_cart == 'true') { //add
			array_push($variations_ids, $variation_id);
		}
		else { //remove
			
			if (sizeof($variations_ids) == 1) {
				$variations_ids = array();
			}
			else {
				for ($i = 0; $i < sizeof($variations_ids); $i++) {
					if ( $variations_ids[$i] == $variation_id) {
						array_splice($variations_ids, $i, 1);
						break;
					}
				}
			}
		}
	}
	else if ($add_cart) {
		$variations_ids = array($variation_id);
	}

	echo implode(",", $variations_ids);

	/*$addToCartUrl = do_shortcode('[add_to_cart_url id="'.$id.'"]');
	$cartUrl = wc_get_cart_url();
	$product = wc_get_product( $id );
	$sku = $product->get_sku();

	//echo '<a href="'.$addToCartUrl.'" class="button '.($product->get_stock_quantity() ? '' : 'disabled').'" data-quantity="1" data-product_id="'.$id.'" data-variation_id="" data-product_sku="'.$sku.'"> '.__('Reserve Now', 'salient').'</a><a href="'.$cartUrl.'" class="added_to_cart wc-forward stHidden" title="View cart"></a>';
	*/
	exit();
}

//
add_action( 'wp_ajax_update_custom_cart_add', 'update_custom_cart_add' );
add_action( 'wp_ajax_nopriv_update_custom_cart_add', 'update_custom_cart_add' );
function update_custom_cart_add() {

	global $woocommerce;

	$post_data = $_POST['post_data'];
	$post_data_arr = explode("-", $post_data);
	$product_id = $post_data_arr[0];
	$variations_ids = explode(",", $post_data_arr[1]);
	//$add_cart = $post_data_arr[2];
	$product = wc_get_product($product_id);
	$variations = $product->get_available_variations();		

	$woocommerce->cart->empty_cart(); 

	foreach ($variations as $variation) {

		for ($i = 0; $i < sizeof($variations_ids); $i++) {
			
			if ( $variations_ids[$i] == strval($variation['variation_id'])) {

				$variation_id = $variations_ids[$i];
				$attributes = $variation['attributes'];
				$arr = array();
		
				if ( !empty($attributes) ) {
					foreach ($attributes as $key => $value) {
						$arr[$key] = $value;
					}
				}
				
				WC()->cart->add_to_cart( $product_id, 1, $variation_id, $arr);
			}
		}
	}

	$link = apply_filters( 'wpml_object_id', 21223, 'post', TRUE );
	echo get_permalink($link);

	exit();
}

//
add_action('woocommerce_thankyou', 'thankyoupage', 10, 1);
function thankyoupage( $order_id ) {

    if ( ! $order_id )
		return;

	$order = wc_get_order( $order_id );

	if ($order->get_status() == 'failed') {
		return;
	}

    if( ! get_post_meta( $order_id, '_thankyou_action_done', true )) {

		$tripId = '';
		$product_id = '';
		$extraIds = array();

        foreach ( $order->get_items() as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$tripId = get_post_meta( $product_id, '_sku', true );
			$product = wc_get_product( $product_id );
		}

		$user = $order->get_user()->data;
		$userId = '';
		if ($user) {
			$userId = $user->ID;
		}

		$persons = intval($order->get_meta('persons'));
		
		$personsArr = array();
		
		for ($i = 1; $i <= $persons; $i++) {
			$order_variation_id_person = $order->get_meta('variation_' . $i);

			if ($order_variation_id_person == $product_id)
				$order_variation_id_person = $product->get_attribute( 'pa_alternavite-id' );
			else
				$order_variation_id_person = get_post_meta( $order_variation_id_person, '_sku', true );
			
			array_push($personsArr, 
				array(
					'firstName' => $order->get_meta('firstname_' . $i),
					'lastName' => $order->get_meta('lastname_' . $i),
					'email' => $order->get_meta('email_' . $i),
					'departureId' => $order->get_meta('departure_' . $i), //array_shift( wc_get_product_terms( $product_id, 'pa_departure-id-' . get_user_meta( $userId, 'departure_' . $i, true ), array( 'fields' => 'names' ) )),
					'dateOfBirth' =>  $order->get_meta('datebirth_' . $i),
					'phone' => $order->get_meta('phone_' . $i),
					'cardId' => $order->get_meta('card_' . $i),
					'comments' => $order->get_meta('comments_' . $i),
					'alternativeId' => $order_variation_id_person,//$order->get_meta('variation_' . $i)
					'extraIds' => $extraIds
				)
			);
		}

		$states = WC()->countries->get_states();
		$state = $order->get_billing_state();
		$country = $order->get_billing_country();

        $data = array(
			'firstName' => $order->get_billing_first_name(),
			'lastName' => $order->get_billing_last_name(),
			'companyName' => $order->get_billing_company(),
			'country' => $country,
			'address' => $order->get_billing_address_1(),
			'city' => $order->get_billing_city(),
			'region' => $states[$country][$state],
			'postcode' => $order->get_billing_postcode(),
			'email' => $order->get_billing_email(),
			'phone' => $order->get_billing_phone(),
			'comments' => $order->get_customer_note(),
			'paymentMethod' => $order->get_payment_method_title(),
			'tripId' => $tripId,
			'persons' => $personsArr       	
        );
		//$data_string = json_encode($data, JSON_UNESCAPED_UNICODE);
		$data_string = str_replace("'", "\'", json_encode($data, JSON_UNESCAPED_UNICODE));
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		   CURLOPT_URL => "http://79.129.39.169/SmartApi/api/PutDataJson?Type=order",
		   CURLOPT_RETURNTRANSFER => true,
		   CURLOPT_ENCODING => "",
		   CURLOPT_MAXREDIRS => 10,
		   CURLOPT_TIMEOUT => 0,
		   CURLOPT_FOLLOWLOCATION => true,
		   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		   CURLOPT_CUSTOMREQUEST => "POST",
		   CURLOPT_POSTFIELDS => "'" . $data_string . "'",
		   CURLOPT_HTTPHEADER => array(
			 "Content-Type: application/json",
			 "Authorization: Basic RXhwb2xvc2l2MDozeHBsMHMxdjAhQCM="
		   ),
		));
		 
		$result = curl_exec($curl);
		 
		curl_close($curl);

		if (! $result) {
			wp_mail( "olympia.filippa@gmail.com", "Fail Post Order", "Order Id: ". $order_id, array("From: ", "Content-Type: text/html; charset=UTF-8") );
			$order->update_meta_data( '_thankyou_action_done_send', false );
		}
		else {
			wp_mail( "olympia.filippa@gmail.com", "Success Post Order", "Guid: " . $result . "Order Id: ". $order_id . ' Json: ' . $data_string, array("From: ", "Content-Type: text/html; charset=UTF-8") );
			$order->update_meta_data( '_thankyou_action_done_send', true );
		}
        
       	if (curl_error($ch)) {
		    $error_msg = curl_error($ch);
		    wp_mail( "olympia.filippa@gmail.com", "Error Post Order", "Order Id: ". $order_id, array("From: ", "Content-Type: text/html; charset=UTF-8") );
		}
        
        $order->update_meta_data( '_thankyou_action_done', true );
       	$order->save();
	}
}

//
add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta($order) {

	$persons = intval($order->get_meta('persons'));

	echo '<table>';
	for ($i = 1; $i <= $persons; $i++) {

		$check = 'No';
		if ($order->get_meta('check_' . $i) && $order->get_meta('check_' . $i) == '1') {
			$check = 'Yes';
		}

		echo '<tr><td><strong>'. __('Person', 'salient') .' ' . $i . '</strong></td><td></td></tr>';

		echo '<tr><td><strong>'. __('Firstname', 'salient') .'</strong></td><td>' . $order->get_meta('firstname_' . $i) . '</td></tr>';
		echo '<tr><td><strong>'. __('Lastname', 'salient') .'</strong></td><td>' . $order->get_meta('lastname_' . $i) . '</td></tr>';
		echo '<tr><td><strong>'. __('Email', 'salient') .'</strong></td><td>' . $order->get_meta('email_' . $i) . '</td></tr>';
		echo '<tr><td><strong>'. __('Departure', 'salient') .'</strong></td><td>' . $order->get_meta('departure_' . $i) . '</td></tr>';
		echo '<tr><td><strong>'. __('Datebirth', 'salient') .'</strong></td><td>' . $order->get_meta('datebirth_' . $i) . '</td></tr>';
		echo '<tr><td><strong>'. __('Over 16', 'salient') .'</strong></td><td>' . $check . '</td></tr>';
		echo '<tr><td><strong>'. __('Phone', 'salient') .'</strong></td><td>' . $order->get_meta('phone_' . $i) . '</td></tr>';
		echo '<tr><td><strong>'. __('Card ID', 'salient') .'</strong></td><td>' . $order->get_meta('card_' . $i) . '</td></tr>';
		echo '<tr><td><strong>'. __('Comments', 'salient') .'</strong></td><td>' .$order->get_meta('comments_' . $i) . '</td></tr>';	
	}
	echo '</table>';
}

//
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');
function my_custom_checkout_field_process() {

	/*
	if ( ! $_POST['covid19'] )
    	wc_add_notice( __( 'Please read and accept the terms and conditions concerning Covid-19.', 'salient' ), 'error' );
	*/
	$persons = intval($_POST['persons']);
	//update_user_meta( $user_id, 'persons', sanitize_text_field( $_POST['persons'] ));

	for ($i = 1; $i <= $persons; $i++) {
		if ( ! $_POST['firstname_' . $i] )
			wc_add_notice( __( 'Please enter firstname of Person ', 'salient') . ' ' . $i, 'error' );
		else if (! preg_match("/^[\w\d\s.,-]*$/", $_POST['firstname_' . $i]))
			wc_add_notice( __( 'Please enter firstname using latin characters of Person ', 'salient') . ' ' . $i, 'error' );
		if ( ! $_POST['lastname_' . $i] )
			wc_add_notice( __( 'Please enter lastname of Person ', 'salient') . ' ' . $i, 'error' );
		else if (! preg_match("/^[\w\d\s.,-]*$/", $_POST['lastname_' . $i]))
			wc_add_notice( __( 'Please enter lastname using latin characters of Person ', 'salient') . ' ' . $i, 'error' );   
		if ( ! $_POST['email_' . $i] )
			wc_add_notice( __( 'Please enter email of Person ', 'salient') . ' ' . $i, 'error' );
		if ( ! $_POST['departure_' . $i] )
			wc_add_notice( __( 'Please enter departure of Person ', 'salient') . ' ' . $i, 'error' );
		/*if ( ! $_POST['datebirth_' . $i] )
			wc_add_notice( __( 'Please enter date of birth of Person ', 'salient') . ' ' . $i, 'error' );*/
		if ( ! $_POST['phone_' . $i] )
			wc_add_notice( __( 'Please enter phone of Person ', 'salient') . ' ' . $i, 'error' );
		/*if (ICL_LANGUAGE_CODE == 'en') {
			if ( ! $_POST['card_' . $i] )
				wc_add_notice( __( 'Please enter Card ID / Passport of Person ', 'salient') . ' ' . $i, 'error' );
		}*/
		if ( ! $_POST['variation_' . $i] )
			wc_add_notice( __( 'Please enter room type of Person ', 'salient') . ' ' . $i, 'error' );
		if ( ! $_POST['check_' . $i] )
			wc_add_notice( __( 'Please check that Person', 'salient') . ' ' . $i . ' ' . __( 'is over 18 years old.', 'salient'), 'error' );

		if (! preg_match("/^[\w\d\s.,-]*$/", $_POST['billing_first_name']))
			wc_add_notice( __( 'Please enter firstname using latin characters ', 'salient'), 'error' );
		if (! preg_match("/^[\w\d\s.,-]*$/", $_POST['billing_last_name']))
			wc_add_notice( __( 'Please enter lastname using latin characters ', 'salient'), 'error' );
			
	}            
}

//
function wooc_extra_register_fields() {?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" placeholder="<?php esc_html_e( 'First name', 'woocommerce' ); ?> *" />
	</p>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
	    <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" placeholder="<?php esc_html_e( 'Last name', 'woocommerce' ); ?> *" />
    </p>
    <?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

function wooc_extra_register_fields2() {
	$link = icl_object_id(13485, 'page', true);
?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide last-woocommerce-register-row">
	    <input type="checkbox" name="gdpr_check" value="<?php if ( ! empty( $_POST['gdpr_check'] ) ) esc_attr_e( $_POST['gdpr_check'] ); ?>" />
		<label class="gdpr_check"><?php esc_html_e( 'I agree with the', 'salient' ); ?> <a href="<?php echo get_permalink( $link ); ?>"><?php esc_html_e( 'privacy policy.', 'salient' ); ?></a>*</label>
    </p>
    <?php
}
add_action( 'woocommerce_register_form', 'wooc_extra_register_fields2' );

//
function custom_registration_redirect() {
    //wp_logout();
    return home_url();
}
add_action('woocommerce_registration_redirect', 'custom_registration_redirect', 2);

add_filter( 'woocommerce_login_redirect', 'my_login_redirect' );
function my_login_redirect() {
	//$my_home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
    return home_url();//icl_get_home_url();
}

add_action( 'wp_login_failed', 'my_front_end_login_fail' );
function my_front_end_login_fail( $username ) {
   $referrer = $_SERVER['HTTP_REFERER'];
   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
      wp_redirect( $referrer . '?login=failed' );
      exit;
   }
}

//
function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
	if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
		$validation_errors->add( 'billing_first_name_error', __( 'First name is required', 'salient' ) );
	}
	if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
		$validation_errors->add( 'billing_last_name_error', __( 'Last name is required', 'salient' ) );
	}

	if ( $_POST['gdpr_check'] === null ) {
		$validation_errors->add( 'gdpr_check', __( 'Gdpr check is required', 'salient' ) );
	}

    return $validation_errors;
}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );

function wooc_save_extra_register_fields( $customer_id ) {
	if ( isset( $_POST['billing_first_name'] ) ) {
		update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
		update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
	}
	if ( isset( $_POST['billing_last_name'] ) ) {
		update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
		update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
	}
	/*if ( isset( $_POST['our_mailing_subscribe'] ) ) {
		try {
			$subscriber_data = array(
			  'email' => $_POST['email'],
			  'first_name' => $_POST['billing_first_name'],
			  'last_name' => $_POST['billing_last_name'],
			  'cf_1' => true
			);
			$subscriber = \MailPoet\API\API::MP('v1')->addSubscriber($subscriber_data, array(2));
		} catch(Exception $exception) {
		}
	}*/
}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );

//
add_action( 'woocommerce_email_order_meta', 'misha_add_email_order_meta', 10, 3 );
/*
 * @param $order_obj Order Object
 * @param $sent_to_admin If this email is for administrator or for a customer
 * @param $plain_text HTML or Plain text (can be configured in WooCommerce > Settings > Emails)
 */
function misha_add_email_order_meta( $order_obj, $sent_to_admin, $plain_text ){

	$order = wc_get_order( $order_obj->get_order_number() );
	$persons = intval($order->get_meta('persons'));

	if ($persons >=1 ) {
		echo '<div style="margin-bottom: 40px;"><table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;" border="1">';
		
		echo '<thead><tr><th>'. __('Person', 'salient') . '</th><th>'. __('Firstname', 'salient') . '</th><th>'. __('Lastname', 'salient') . '</th><th>'. __('Departure', 'salient') . '</th></tr></thead><tbody>';
		for ($i = 1; $i <= $persons; $i++) {

			$departure_id = $order->get_meta('departure_'.$i);
			foreach ( $order->get_items() as $item_id => $item ) {
				$product = wc_get_product( $item->get_product_id() );
				if ($product->get_attribute( 'pa_departure-id-1' ) && $product->get_attribute( 'pa_departure-id-1' ) == $departure_id) {
					$departure =  '<a href="https://bit.ly/2rdgMTr">' . __('Kalamaria', 'salient') . '</a> - ' . $product->get_attribute( 'pa_departure-time-1' );
				}
				else if ($product->get_attribute( 'pa_departure-id-2' ) && $product->get_attribute( 'pa_departure-id-2' ) == $departure_id) {
					$departure = '<a href="https://bit.ly/2OzKOcq">' . __('City Center', 'salient') . '</a> - ' . $product->get_attribute( 'pa_departure-time-2' );
				}
				else if ($product->get_attribute( 'pa_departure-id-3' ) && $product->get_attribute( 'pa_departure-id-3' ) == $departure_id) {
					$departure =  '<a href="https://goo.gl/maps/GCZomJzCxCZtd7x89">' . __('One Salonica', 'salient') . '</a> - ' . $product->get_attribute( 'pa_departure-time-3' );
				}
				else if ($product->get_attribute( 'pa_departure-id-4' ) && $product->get_attribute( 'pa_departure-id-4' ) == $departure_id) {
					$departure =  '<a href="https://maps.app.goo.gl/FrycGAnWJZ3PM2QL7">' . __('Litochoro', 'salient') . '</a> - ' . $product->get_attribute( 'pa_departure-time-4' );
				}
			}

			echo '<tr><td>'.$i.'</td><td>' . $order->get_meta('firstname_' . $i) . '</td>';
			echo '<td>' . $order->get_meta('lastname_' . $i) . '</td>';
			echo '<td>' .date('d M', strtotime($product->get_attribute( 'pa_start-date' ))) . ' - ' . $departure . '</td></tr>';
		}
		
		echo '</tbody></table></div>';
	}
}

//
add_action( 'manage_posts_extra_tablenav', 'admin_order_list_top_bar_button', 20, 1 );
function admin_order_list_top_bar_button( $which ) {
    global $typenow;

    if ( 'product' === $typenow && 'top' === $which ) {
        ?>
        <div class="alignleft actions ajax-insert button">
           <?php echo __( 'Εισαγωγή εκδρομών', 'salient' ); ?>
        </div>
        <div class="alignleft actions ajax-update button">
           <?php echo __( 'Ανανέωση εκδρομών', 'salient' ); ?>
        </div>
        <?php
    }
}

add_action('admin_head', 'my_custom_fonts');
function my_custom_fonts() {
	echo '<style>
    .success-admin-custom {
   		top: 32px;
	    font-size: 15px;
	    width: 100%;
	    left: 0;
	    padding: 15px;
	    color: green;
	    background-color: #fff;
	    margin: 15px 0;
    }
    .load-spinner {
    	position: fixed;
	    width: 100%;
	    height: 100%;
	    top: 0;
	    background: #fff;
	    opacity: 0.5;
	    margin: auto;
	    text-align: center;
    }
    .load-spinner img {
    	top: 30%;
    	position: relative;
    }
    .status-ok {
    	width: 20px;
    	height: 20px;
    	background: green;
    	border-radius: 20px;
    	display: block;
    }
    .status-notok {
    	width: 20px;
    	height: 20px;
    	background: red;
    	border-radius: 20px;
    	display: block;
    }
  </style>';
  $path = get_stylesheet_directory_uri(). '/images';
  $icon = '<img src="' . WC_HTTPS::force_https_url( "$path/loader.gif" ) . '" />';
  ?>
  <script type="text/javascript" >
	jQuery(document).ready(function($) {
	    $('.ajax-insert').click(function(){
	        var data = {
	            action: 'insert_action'
	        };

	        $('body').append('<div class="load-spinner"><?php echo $icon; ?></div>');

	        $.post(ajaxurl, data, function(response) {

	            if (response) {
	            	//$('.tablenav.top').prepend("<div class='success-admin-custom'><span>Η Εισαγωγή εκδρομών ολοκληρώθηκε επιτυχώς.</span><div class='button'>Ανανέωση</div></div>");
	            	location.reload();
	            }
	            else {
	            	//$('.tablenav.top').prepend("<div class='fail-admin-custom'>Παρουσιάστηκε κάποιο σφάλμα.</div>");
	            	alert('Παρουσιάστηκε κάποιο σφάλμα.');
	            }
	        });
	    });

	    $('.ajax-update').click(function(){
	        var data = {
	            action: 'update_action'
	        };

	        $('body').append('<div class="load-spinner"><?php echo $icon; ?></div>');

	        $.post(ajaxurl, data, function(response) {
	            if (response) {
	            	location.reload();
	            }
	            else {
	            	alert('Παρουσιάστηκε κάποιο σφάλμα.');
	            }
	        });
	    });
	});
	</script>
  <?php
}

add_action('wp_ajax_insert_action', 'insert_action_callback');
function insert_action_callback() {
    require $_SERVER['DOCUMENT_ROOT'] . '/cron-erp.php';

    $init_wp = new Exelixis_Cron;
	$exelixisUpdate = $init_wp->insert_trips();
    
    return $exelixisUpdate;
    exit();
}

add_action('wp_ajax_update_action', 'update_action_callback');
function update_action_callback() {
    require $_SERVER['DOCUMENT_ROOT'] . '/cron-erp.php';

    $init_wp = new Exelixis_Cron;
	$exelixisUpdate = $init_wp->update_trips();

	return $exelixisUpdate;
    exit();
}

//
add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns)
{
    $reordered_columns = array();

    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
            $reordered_columns['exelixis-status'] = __( 'Exelixis status','salient');
        }
    }
    return $reordered_columns;
}

add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id )
{
    switch ( $column )
    {
        case 'exelixis-status' :
            $my_var_one = get_post_meta( $post_id, '_thankyou_action_done_send', true );
            if ($my_var_one)
            	echo '<span class="status-ok"></span>';
            else 
            	echo '<span class="status-notok"></span>';
            break;
    }
}

//
add_filter( 'bulk_actions-edit-shop_order', 'downloads_bulk_actions_edit_product', 20, 1 );
function downloads_bulk_actions_edit_product( $actions ) {
    $actions['write_downloads'] = __( 'Send Exelixis', 'salient' );
    return $actions;
}

add_filter( 'handle_bulk_actions-edit-shop_order', 'downloads_handle_bulk_action_edit_shop_order', 10, 3 );
function downloads_handle_bulk_action_edit_shop_order( $redirect_to, $action, $post_ids ) {
    if ( $action !== 'write_downloads' )
        return $redirect_to;

    $processed_ids = array();

    foreach ( $post_ids as $post_id ) {
        $order = wc_get_order( $post_id );
        
        send_order($order, $post_id);

        $processed_ids[] = $post_id;
    }

    return $redirect_to = add_query_arg( array(
        'write_downloads' => '1',
        'processed_count' => count( $processed_ids ),
        'processed_ids' => implode( ',', $processed_ids ),
    ), $redirect_to );
}

//Send order
function send_order ($order, $order_id) {
	$tripId = '';
	$product_id = '';
	$extraIds = array();

    foreach ( $order->get_items() as $item_id => $item ) {
		$product_id = $item->get_product_id();
		$tripId = get_post_meta( $product_id, '_sku', true );
		$product = wc_get_product( $product_id );
	}

	$user = $order->get_user()->data;
	$userId = '';
	if ($user) {
		$userId = $user->ID;
	}

	$persons = intval($order->get_meta('persons'));
	
	$personsArr = array();
	
	for ($i = 1; $i <= $persons; $i++) {
		$order_variation_id_person = $order->get_meta('variation_' . $i);

		if ($order_variation_id_person == $product_id)
			$order_variation_id_person = $product->get_attribute( 'pa_alternavite-id' );
		else
			$order_variation_id_person = get_post_meta( $order_variation_id_person, '_sku', true );
		
		array_push($personsArr, 
			array(
				'firstName' => $order->get_meta('firstname_' . $i),
				'lastName' => $order->get_meta('lastname_' . $i),
				'email' => $order->get_meta('email_' . $i),
				'departureId' => $order->get_meta('departure_' . $i), //array_shift( wc_get_product_terms( $product_id, 'pa_departure-id-' . get_user_meta( $userId, 'departure_' . $i, true ), array( 'fields' => 'names' ) )),
				'dateOfBirth' =>  $order->get_meta('datebirth_' . $i),
				'phone' => $order->get_meta('phone_' . $i),
				'cardId' => $order->get_meta('card_' . $i),
				'comments' => $order->get_meta('comments_' . $i),
				'alternativeId' => $order_variation_id_person,//$order->get_meta('variation_' . $i)
				'extraIds' => $extraIds
			)
		);
	}

	$states = WC()->countries->get_states();
	$state = $order->get_billing_state();
	$country = $order->get_billing_country();

    $data = array(
		'firstName' => $order->get_billing_first_name(),
		'lastName' => $order->get_billing_last_name(),
		'companyName' => $order->get_billing_company(),
		'country' => $country,
		'address' => $order->get_billing_address_1(),
		'city' => $order->get_billing_city(),
		'region' => $states[$country][$state],
		'postcode' => $order->get_billing_postcode(),
		'email' => $order->get_billing_email(),
		'phone' => $order->get_billing_phone(),
		'comments' => $order->get_customer_note(),
		'paymentMethod' => $order->get_payment_method_title(),
		'tripId' => $tripId,
		'persons' => $personsArr       	
    );
	$data_string = str_replace("'", "\'", json_encode($data, JSON_UNESCAPED_UNICODE));
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	   CURLOPT_URL => "http://79.129.39.169/SmartApi/api/PutDataJson?Type=order",
	   CURLOPT_RETURNTRANSFER => true,
	   CURLOPT_ENCODING => "",
	   CURLOPT_MAXREDIRS => 10,
	   CURLOPT_TIMEOUT => 0,
	   CURLOPT_FOLLOWLOCATION => true,
	   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	   CURLOPT_CUSTOMREQUEST => "POST",
	   CURLOPT_POSTFIELDS => "'" . $data_string . "'",
	   CURLOPT_HTTPHEADER => array(
		 "Content-Type: application/json",
		 "Authorization: Basic RXhwb2xvc2l2MDozeHBsMHMxdjAhQCM="
	   ),
	));
	 
	$result = curl_exec($curl);
	 
	curl_close($curl);

	if (! $result) {
		wp_mail( "olympia.filippa@gmail.com", "Fail Post Order", "Order Id: ". $order_id, array("From: ", "Content-Type: text/html; charset=UTF-8") );
		$order->update_meta_data( '_thankyou_action_done_send', false );
	}
	else {
		wp_mail( "olympia.filippa@gmail.com", "Success Post Order", "Guid: " . $result . "Order Id: ". $order_id . ' Json: ' . $data_string, array("From: ", "Content-Type: text/html; charset=UTF-8") );
		$order->update_meta_data( '_thankyou_action_done_send', true );
	}
    
   	if (curl_error($ch)) {
	    $error_msg = curl_error($ch);
	    wp_mail( "olympia.filippa@gmail.com", "Error Post Order", "Order Id: ". $order_id, array("From: ", "Content-Type: text/html; charset=UTF-8") );
	}
    
    $order->update_meta_data( '_thankyou_action_done', true );
   	$order->save();
}

//
add_action( 'template_redirect', 'define_default_payment_gateway' );
function define_default_payment_gateway(){
    if( is_checkout() && ! is_wc_endpoint_url() ) {
        $default_payment_id = 'piraeusbank_gateway';

        WC()->session->set( 'chosen_payment_method', $default_payment_id );
    }
}

//
add_action('woocommerce_cancelled_order','lenura_redirect_to_home');
 function lenura_redirect_to_home() {
    wp_redirect(wc_get_checkout_url());
 }

//
//add_action('woocommerce_checkout_before_terms_and_conditions', 'checkout_additional_checkboxes');
function checkout_additional_checkboxes() {
    ?>
    <p class="form-row validate-required">
        <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="covid19">
        <label for="terms" class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox" style="font-size: 14px; line-height: 1;">
			<?php echo __( "I have and agree to the demonstration of the necessary certificates concerning Covid-19, before boarding the bus", "salient" ) ?> <span class="required">*</span>
		</label>
    </p>
<?php
}

//
add_action( 'woocommerce_cart_calculate_fees','woocommerce_custom_surcharge', 10, 1 );
function woocommerce_custom_surcharge( ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
    
    if (  WC()->session->get( 'extra_fee' ) ) {
    	WC()->cart->add_fee(__('Extras', 'salient-child'), WC()->session->get( 'extra_fee' ));
    	WC()->session->set( 'extra_fee', 0 );
    }
}

//

