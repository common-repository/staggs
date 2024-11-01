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
class Staggs_Features {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Empty.
	}

	/**
	 * Short description.
	 *
	 * @since    1.0.0
	 */
	public function register_sub_menu() {
		add_submenu_page(
			'edit.php?post_type=sgg_attribute',
			__( 'Free vs PRO', 'staggs' ),
			__( 'Free vs PRO', 'staggs' ),
			'edit_posts',
			'compare_features',
			array( $this, 'features_page_contents' )
		);
	}

	/**
	 * Short description.
	 *
	 * @since    1.0.0
	 */
	public function features_page_contents() {
		// Show page contents.
		?>
		<div class="wrap">
			<h1></h1>
			<div class="staggs-page-content">
				<div class="staggs-page-header">
					<h1><?php esc_attr_e( 'Staggs Free vs PRO', 'staggs' ); ?></h1>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sgg_attribute&page=staggs-pricing' ) ); ?>" class="button button-primary"><?php esc_attr_e( 'Buy PRO', 'staggs' ); ?></a>
				</div>
				<div class="staggs-page-table-content">
					<div class="staggs-table staggs-features-table">
						<div class="table-heading">
							<p><?php esc_attr_e( 'Features', 'staggs' ); ?></p>
							<p><?php esc_attr_e( 'Free', 'staggs' ); ?></p>
							<p><?php esc_attr_e( 'PRO', 'staggs' ); ?></p>
						</div>
						<div class="table-contents">
							<?php
							$pro_features = array(
								'Conditionally display steps based on previous step values',
								'Copy and share active configuration as a link',
								'Download active configurations as a PDF document',
								'Support for splitting up the configurator into multiple steps',
								'Configurator stepper template',
								'Configurator horizontal popup template',
								'Image upload field with dynamic preview',
								'Related products attribute for including related WooCommerce products',
								'Advanced pricing options for measurement inputs',
								'Attribute inventory management',
								'Support for 3D product model viewer',
								'Basic AR for displaying 3D models in a room',
								'CSV Import & Export Tool for easy attributes management',
							);
							foreach ( $pro_features as $feature ) :
								?>
								<div class="table-row">
									<p><?php echo esc_attr( sgg__( sprintf( '%s', $feature ) ) ); ?></p>
									<p><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'img/cross.svg' ); ?>" height="15" alt="Feature not included"></p>
									<p><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'img/check.svg' ); ?>" height="20" alt="Feature included"></p>
								</div>
								<?php
							endforeach;
							?>
						</div>
					</div>
				</div>
				<div class="staggs-upgrade-notice">
					<h2><?php esc_attr_e( 'Do more with Staggs PRO', 'staggs' ); ?></h2>
					<p><?php esc_attr_e( 'With our PRO features you can truly create stunning product configurators, with support for 3D models, stock management, shared configuration links and more.', 'staggs' ); ?></p>
					<div class="upgrade-notice-buttons">
						<a href="<?php echo esc_url( sgg_fs()->get_trial_url() ); ?>" class="button button-secondary"><?php esc_attr_e( 'Start 14-day free trail', 'staggs' ); ?></a>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sgg_attribute&page=staggs-pricing' ) ); ?>" class="button button-primary"><?php esc_attr_e( 'Buy PRO', 'staggs' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
