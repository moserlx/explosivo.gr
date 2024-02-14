<?php
/**
 * Customer on-hold order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-on-hold-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( __( 'Hi %s,', 'woocommerce' ), $order->get_billing_first_name() ); ?></p>
<?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>

<p><?php _e( 'Thank you for your reservation at', 'salient' ); ?> <a href="https://explosivo.gr/">explosivo.gr</a></p>
<p><?php _e( 'Below we send you all the information about your reservation', 'salient' ); ?>:</p>

<?php // phpcs:ignore WordPress.XSS.EscapeOutput ?>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

?>

<p>- <?php _e( "Please be at the departure point 10' before the departure time", 'salient' ); ?>.</p>
<p>- <?php _e( 'At the daily trips at the ski centers (Snowbus) the possibility of name change or change of the dastination or change to “open” ticket can be done latest on the previous day of the trip until 18:00pm. The policy of General rules and conditions of bookings/ cancelations apply to all the rest trips. Ask the leader of the trip for any group discounts at the lift passes or the equipment rentals', 'salient' ); ?>.</p>
<p>- <?php _e( 'Regarding the trips abroad, do not forget to take your ID/passport with you and to check if they are still valid.', 'salient' ); ?>.</p>
<p>- <?php _e( 'Regarding the multi-day trips, informational leaflet about the destination and details for the day of return, will be provided upon departure from your leader. Regarding the trips that include overnight/s please be aware that according to national regulations in most hotels you can check in after 14:00 and must check out before 12:00', 'salient' ); ?>.</p>
<?php if (ICL_LANGUAGE_CODE == "en") : ?>
<p>- <?php _e( "You can see more information at the page", 'salient' ); ?> <a href="https://www.explosivo.gr/en/oroi-politikes/"><?php _e( 'Terms | Policies', 'salient' ); ?>.</a></p>
<?php else: ?>
<p>- <?php _e( "You can see more information at the page", 'salient' ); ?> <a href="https://www.explosivo.gr/oroi-politikes/"><?php _e( 'Terms | Policies', 'salient' ); ?>.</a></p>
<?php endif; ?>

<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
