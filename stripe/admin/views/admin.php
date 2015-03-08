<?php

/**
 * Represents the view for the administration dashboard.
 *
 * @package    SC
 * @subpackage Views
 * @author     Phil Derksen <pderksen@gmail.com>, Nick Young <mycorsceb@gmail.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $sc_options, $settings;

?>

<div class="wrap">
	<?php settings_errors(); ?>
	<div id="sc-settings">
		<div id="sc-settings-content">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			
			<h2 class="nav-tab-wrapper">
				<?php
					$first_time = true;
					$is_active  = '';
					
					foreach( $settings->get_tabs() as $key => $value) {
						if( $first_time == true ) {
							$is_active = 'nav-tab-active';
							$first_time = false;
						} else {
							$is_active = '';
						}
				?>
						<a href="#<?php echo $key; ?>" class="nav-tab sc-nav-tab <?php echo $is_active; ?>" data-tab-id="<?php echo $key; ?>"><?php echo $value; ?></a>
				<?php
					}
				?>
			</h2>

			<div id="tab_container">
				<?php
					$settings->load_template( 'default' );
					$settings->load_template( 'keys' );
				?>
			</div><!-- #tab_container-->
		</div><!-- #sc-settings-content -->

		<div id="sc-settings-sidebar">
			<?php if ( class_exists( 'Stripe_Checkout_Pro' ) ): ?>
				<?php include( 'admin-sidebar-pro.php' ); ?>
			<?php else: ?>
				<?php include( 'admin-sidebar.php' ); ?>
			<?php endif; ?>
		</div>

	</div>
</div><!-- .wrap -->
