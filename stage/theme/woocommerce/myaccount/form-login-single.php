<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6">
		<h1><?php esc_html_e( 'Log in', 'woocommerce' ); ?></h1>

		<form class="woocommerce-form woocommerce-form-login login loginform-page loginform-page-custom" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" placeholder="<?php esc_html_e( 'Email', 'woocommerce' ); ?> *"/><?php // @codingStandardsIgnoreLine ?>
			
			<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" placeholder="<?php esc_html_e( 'Password', 'woocommerce' ); ?> *" />
			<?php do_action( 'woocommerce_login_form' ); ?>

			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			
			<div>
				<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') )?>lost-password"><?php echo __('Forgot your password', 'salient'); ?></a>
			</div>

			<button type="submit" class="woocommerce-Button button" name="login" value="<?php esc_attr_e( 'Sign in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>	
			<a class="create-account" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') )?>/?action=register"><?php echo __('Create account', 'salient'); ?></a>
		
			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>
	</div>
	<div class="col-md-3"></div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>