<?php

/**
 * The admin-specific features display of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.3.0
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 */

/**
 * The admin-specific features display of the plugin.
 *
 * Defines the plugin features.
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_About {

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Constructor.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Short description.
	 *
	 * @since    1.0.0
	 */
	public function register_sub_menu() {
		add_submenu_page(
			'edit.php?post_type=sgg_attribute',
			__( 'About Staggs', 'staggs' ),
			__( 'About Staggs', 'staggs' ),
			'edit_posts',
			'about',
			array( $this, 'about_page_contents' )
		);
	}

	/**
	 * Short description.
	 *
	 * @since    1.0.0
	 */
	public function about_page_contents() {
		// Show page contents.
		?>
		<div class="wrap">
			<h1></h1>
			<div class="staggs-about-wrapper">

				<div class="staggs-about-header">
					<p class="label">Version <?php echo esc_attr( $this->version ); ?></p>
					<h1><?php esc_attr_e( 'Welcome to Staggs!', 'staggs' ); ?></h1>
					<p class="subtitle"><?php esc_attr_e( 'The go-to product configurator toolkit for WordPress and WooCommerce.', 'staggs' ); ?></p>
					<p><?php esc_attr_e( 'Please follow the steps below to set up your first product configurator.', 'staggs' ); ?></p>
				</div>

				<div class="staggs-about-block">
					<div class="about-block-header">
						<h2><?php esc_attr_e( 'Follow the interactive module', 'staggs' ); ?></h2>
						<div style="position: relative; padding-bottom: calc(66.85205784204672% + 41px); height: 0; width: 100%;">
						<iframe
							src="https://demo.arcade.software/Z6eSn3yhcfIa1kWNSaQh?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true"
							title="Dashboard < Staggs Tryout — WordPress"
							frameborder="0"
							loading="lazy"
							webkitallowfullscreen
							mozallowfullscreen
							allowfullscreen
							allow="clipboard-write"
							style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"
						></iframe>
						</div>

						<?php if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) : ?>
							<p class="staggs-about-intro"><b><?php esc_attr_e( 'Note', 'staggs' ); ?>:</b> <?php esc_attr_e( 'Some features may not be visible to you because the screens have been taken from the PRO version', 'staggs' ); ?></p>
						<?php endif; ?>

					</div>
					<div class="about-block-header">
						<h2><?php esc_attr_e( 'Get started in 3 steps', 'staggs' ); ?></h2>
					</div>
					<div class="about-block-steps">
						<div class="step">
							<div class="step-icon">
								<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'img/1.png' ); ?>" alt="Step one">	
							</div>
							<div class="step-details">
								<h3><?php esc_attr_e( 'Create attributes', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Create attributes to add options for the product configurator.', 'staggs' ); ?></p>
								<a href="<?php echo esc_url( admin_url( '/post-new.php?post_type=sgg_attribute' ) ); ?>"><?php esc_attr_e( 'Create attribute', 'staggs' ); ?></a>
							</div>
						</div>
						<div class="step">
							<div class="step-icon">
								<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'img/2.png' ); ?>" alt="Step two">	
							</div>
							<div class="step-content">
								<h3><?php esc_attr_e( 'Build your configurable product', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'The configurable products consists of the attributes, displayed in various ways using the attribute view templates.', 'staggs' ); ?></p>
								<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
									<a href="<?php echo esc_url( admin_url( '/post-new.php?post_type=product' ) ); ?>"><?php esc_attr_e( 'Add a new configurable product', 'staggs' ); ?></a>
								<?php else : ?>
									<a href="<?php echo esc_url( admin_url( '/post-new.php?post_type=sgg_product' ) ); ?>"><?php esc_attr_e( 'Add a new configurable product', 'staggs' ); ?></a>
								<?php endif; ?>
							</div>
						</div>
						<div class="step">
							<div class="step-icon">
								<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'img/3.png' ); ?>" alt="Step three">	
							</div>
							<div class="step-content">
								<h3><?php esc_attr_e( 'Apply a configurator template', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Finally, apply a custom template to the product configurator to make it completely yours!', 'staggs' ); ?></p>
								<a href="<?php echo esc_url( admin_url( '/post-new.php?post_type=sgg_theme' ) ); ?>"><?php esc_attr_e( 'Add a template', 'staggs' ); ?></a>
							</div>
						</div>
					</div>
				</div>

				<div class="staggs-upgrade-notice" style="margin: 30px 0;">
					<h3><?php esc_attr_e( 'A message for page builders / block theme users:', 'staggs' ); ?></h3>
					<p><?php esc_attr_e( 'If you are using a page builder or a block theme, please note that you have to display the configurator using the shortcode block in your page/product template. Otherwise the configurator will not show up.', 'staggs' ); ?></p>
					<p><a href="https://staggs.app/docs/using-shortcodes/" target="_blank"><?php esc_attr_e( 'Learn more', 'staggs' ); ?></a></p>
				</div>
				
				<div class="staggs-about-rows">
					<div class="col-staggs">
						<h2><?php esc_attr_e( 'Documentation', 'staggs' ); ?></h2>
						<div class="about-block center">
							<p><?php esc_attr_e( 'Learn everything about the product configurator in our documentation pages.', 'staggs' ); ?></p>
							<a href="https://staggs.app/docs" target="_blank" class="button button-primary"><?php esc_attr_e( 'Go to docs', 'staggs' ); ?></a>
						</div>
					</div>
					<div class="col-staggs">
						<h2><?php esc_attr_e( 'FAQ', 'staggs' ); ?></h2>
						<div class="about-block center">
							<p><?php esc_attr_e( 'Find answers yourselves through reading frequently asked questions of our customers.', 'staggs' ); ?></p>
							<a href="https://staggs.app/faq" target="_blank" class="button button-primary"><?php esc_attr_e( 'Go to FAQ', 'staggs' ); ?></a>
						</div>
					</div>
					<div class="col-staggs">

						<?php if ( sgg_fs()->is_plan_or_trial( 'professional' ) ) : ?>

							<h2><?php esc_attr_e( 'Support', 'staggs' ); ?></h2>
							<div class="about-block center">
								<p><?php esc_attr_e( 'Need help? Fill out the support form and we will get back to you as soon as possible.', 'staggs' ); ?></p>
								<a href="https://staggs.app/support" target="_blank" class="button button-primary"><?php esc_attr_e( 'Get help', 'staggs' ); ?></a>
							</div>

						<?php endif; ?>

					</div>
				</div>

				<?php if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) : ?>

					<div class="staggs-upgrade-notice">
						<h2><?php esc_attr_e( 'Do more with Staggs PRO', 'staggs' ); ?></h2>
						<p><?php esc_attr_e( 'With our PRO features you can truly create stunning product configurators, with support for 3D models, stock management, shared configuration links and more.', 'staggs' ); ?></p>
						<div class="upgrade-notice-buttons">
							<a href="<?php echo esc_url( sgg_fs()->get_trial_url() ); ?>" class="button button-secondary"><?php esc_attr_e( 'Start 14-day free trail', 'staggs' ); ?></a>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sgg_attribute&page=staggs-pricing' ) ); ?>" class="button button-primary"><?php esc_attr_e( 'Buy PRO', 'staggs' ); ?></a>
						</div>
					</div>

				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}
