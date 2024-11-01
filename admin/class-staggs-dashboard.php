<?php

/**
 * The admin-specific dashboard display of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.3.4
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 */

/**
 * The admin-specific dashboard display of the plugin.
 *
 * Defines the plugin dashboard view.
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_Dashboard {

	/**
	 * Constructor.
	 */
	public function __construct() {
		
	}

	/**
	 * Redirect to 'official' dashboard page.
	 */
	public function redirect_if_top_page() {
		global $pagenow;
		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'dashboard' === $_GET['page'] ) {
			wp_safe_redirect( esc_url( admin_url( '/edit.php?post_type=sgg_attribute&page=dashboard' ) ) );
			exit;
		}
	}

	/**
	 * Register admin Staggs dashboard page.
	 *
	 * @since    1.3.4
	 */
	public function register_sub_menu() {
		$hook = add_submenu_page(
			'edit.php?post_type=sgg_attribute',
			__( 'Dashboard', 'staggs' ),
			__( 'Dashboard', 'staggs' ),
			'edit_posts',
			'dashboard',
			array( $this, 'dashboard_page_contents' ),
			0
		);
	}

	/**
	 * Dashboard admin page html
	 *
	 * @since    1.3.4
	 */
	public function dashboard_page_contents() {
		$pro_class = '';
		$pro_label = '';
		if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) {
			$pro_class = ' staggs-page-article-pro';
			$pro_label = '<span>' . __( 'PRO', 'staggs' ) . '</span>';
		}
		?>
		<div class="wrap">
			<h1></h1>
			<div class="staggs-page-dashboard">
				<div class="staggs-page-header">
					<div class="staggs-page-title">
						<h1><?php esc_attr_e( 'The Staggs Dashboard', 'staggs' ); ?></h1>
					</div>
					<?php if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) : ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sgg_attribute&page=staggs-pricing' ) ); ?>" class="button button-primary"><?php esc_attr_e( 'Buy PRO', 'staggs' ); ?></a>
					<?php endif; ?>
				</div>
				<div class="staggs-page-dashboard-top">
					<h2><?php esc_attr_e( 'Quick links', 'staggs' ); ?></h2>
					<p><?php esc_attr_e( 'Welcome to the product configurator toolkit by Staggs. Here you can find quick links to get you started', 'staggs' ); ?></p>

					<div class="staggs-page-list">
						<div class="staggs-page-list-item">
							<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=sgg_attribute&page=about' ) ); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M11.975 22H12c3.859 0 7-3.14 7-7V9c0-3.841-3.127-6.974-6.981-7h-.06C8.119 2.022 5 5.157 5 9v6c0 3.86 3.129 7 6.975 7zM7 9a5.007 5.007 0 0 1 4.985-5C14.75 4.006 17 6.249 17 9v6c0 2.757-2.243 5-5 5h-.025C9.186 20 7 17.804 7 15V9z"></path><path d="M11 6h2v6h-2z"></path></svg>
								<?php esc_attr_e( 'Follow the tutorial', 'staggs' ); ?>
							</a>
						</div>
						<div class="staggs-page-list-item">
							<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
								<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=product' ) ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M5 22h14a2 2 0 0 0 2-2V9a1 1 0 0 0-1-1h-3v-.777c0-2.609-1.903-4.945-4.5-5.198A5.005 5.005 0 0 0 7 7v1H4a1 1 0 0 0-1 1v11a2 2 0 0 0 2 2zm12-12v2h-2v-2h2zM9 7c0-1.654 1.346-3 3-3s3 1.346 3 3v1H9V7zm-2 3h2v2H7v-2z"></path></svg>
									<?php esc_attr_e( 'Manage products', 'staggs' ); ?>
								</a>
							<?php else: ?>
								<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=sgg_product' ) ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M5 22h14a2 2 0 0 0 2-2V9a1 1 0 0 0-1-1h-3v-.777c0-2.609-1.903-4.945-4.5-5.198A5.005 5.005 0 0 0 7 7v1H4a1 1 0 0 0-1 1v11a2 2 0 0 0 2 2zm12-12v2h-2v-2h2zM9 7c0-1.654 1.346-3 3-3s3 1.346 3 3v1H9V7zm-2 3h2v2H7v-2z"></path></svg>
									<?php esc_attr_e( 'Manage products', 'staggs' ); ?>
								</a>
							<?php endif; ?>
						</div>
						<div class="staggs-page-list-item">
							<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=sgg_attribute&page=appearance' ) ); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"></path><path d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"></path></svg>
								<?php esc_attr_e( 'Configure settings', 'staggs' ); ?>
							</a>
						</div>
						<div class="staggs-page-list-item">
							<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=sgg_attribute' ) ); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M4 11h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1zm1-6h4v4H5V5zm15-2h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zm-1 6h-4V5h4v4zm-9 12a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6zm-5-6h4v4H5v-4zm13-1h-2v2h-2v2h2v2h2v-2h2v-2h-2z"></path></svg>
								<?php esc_attr_e( 'Manage attributes', 'staggs' ); ?>
							</a>
						</div>
						<div class="staggs-page-list-item">
							<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=sgg_theme' ) ); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M13.707 2.293a.999.999 0 0 0-1.414 0l-5.84 5.84c-.015-.001-.029-.009-.044-.009a.997.997 0 0 0-.707.293L4.288 9.831a2.985 2.985 0 0 0-.878 2.122c0 .802.313 1.556.879 2.121l.707.707-2.122 2.122A2.92 2.92 0 0 0 2 19.012a2.968 2.968 0 0 0 1.063 2.308c.519.439 1.188.68 1.885.68.834 0 1.654-.341 2.25-.937l2.04-2.039.707.706c1.134 1.133 3.109 1.134 4.242.001l1.415-1.414a.997.997 0 0 0 .293-.707c0-.026-.013-.05-.015-.076l5.827-5.827a.999.999 0 0 0 0-1.414l-8-8zm-.935 16.024a1.023 1.023 0 0 1-1.414-.001l-1.414-1.413a.999.999 0 0 0-1.414 0l-2.746 2.745a1.19 1.19 0 0 1-.836.352.91.91 0 0 1-.594-.208A.978.978 0 0 1 4 19.01a.959.959 0 0 1 .287-.692l2.829-2.829a.999.999 0 0 0 0-1.414L5.701 12.66a.99.99 0 0 1-.292-.706c0-.268.104-.519.293-.708l.707-.707 7.071 7.072-.708.706zm1.889-2.392L8.075 9.339 13 4.414 19.586 11l-4.925 4.925z"></path></svg>
								<?php esc_attr_e( 'Manage templates', 'staggs' ); ?>
							</a>
						</div>
						<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
							<div class="staggs-page-list-item">
								<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=sgg_attribute&page=analytics' ) ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm7.931 9H13V4.069A8.008 8.008 0 0 1 19.931 11zM4 12c0-4.072 3.061-7.436 7-7.931V12a.996.996 0 0 0 .111.438c.015.03.022.063.041.093l4.202 6.723A7.949 7.949 0 0 1 12 20c-4.411 0-8-3.589-8-8zm13.052 6.196L13.805 13h6.126a7.992 7.992 0 0 1-2.879 5.196z"></path></svg>
									<?php esc_attr_e( 'View analytics', 'staggs' ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="staggs-page-dashboard-main">
					<h2><?php esc_attr_e( 'Attributes', 'staggs' ); ?></h2>
					<p><?php esc_attr_e( 'Easily find modules you can use to create configurator attributes', 'staggs' ); ?></p>

					<div class="staggs-page-row">
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Set attribute option prices', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Charge additional costs for your configurator attribute options', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/configure-attribute-pricing/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Duplicating attributes', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Speed up the process of attribute creation using a duplicate post plugin', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/duplicating-attributes/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Translating attributes', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Make your attributes available in multiple languages', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/translating-staggs-attributes/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
							<div class="staggs-page-column">
								<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
									<h3><?php esc_attr_e( 'WooCommerce linked options', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
									<p><?php esc_attr_e( 'Link your configurator attribute options to WooCommerce products', 'staggs' ); ?></p>
									<div class="staggs-page-article-link">
										<a href="https://staggs.app/docs/attribute-types/product/">
											<?php esc_attr_e( 'Read article', 'staggs' ); ?>
										</a>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Set option stock quantities', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Configure the availability of individual options', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/manage-attribute-stock/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Import/Export', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Manage the attributes data with CSV import export tool', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/staggs-attribute-import-and-export/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="staggs-page-dashboard-main">
					<div class="staggs-page-dashboard-heading">
						<h2><?php esc_attr_e( 'Appearance', 'staggs' ); ?></h2>
						<a href="https://staggs.app/docs/customizing-the-product-configurator/">
							<?php esc_attr_e( 'Read full article', 'staggs' ); ?>
						</a>
					</div>
					<p><?php esc_attr_e( 'Control the appearance of your configurators', 'staggs' ); ?></p>

					<div class="staggs-page-row">
						<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
							<div class="staggs-page-column">
								<div class="staggs-page-article">
									<h3><?php esc_attr_e( 'Configurator shortcode display', 'staggs' ); ?></h3>
									<p><?php esc_attr_e( 'Using a block theme or page builder? Learn how to add the configurator to your page using the shortcode.', 'staggs' ); ?></p>
									<div class="staggs-page-article-link">
										<a href="https://staggs.app/docs/using-shortcodes/">
											<?php esc_attr_e( 'Read article', 'staggs' ); ?>
										</a>
									</div>
								</div>
							</div>
						<?php else : ?>
							<div class="staggs-page-column">
								<div class="staggs-page-article">
									<h3><?php esc_attr_e( 'Configurator display', 'staggs' ); ?></h3>
									<p><?php esc_attr_e( 'Learn how to activate and display the product configurator on your WordPress site', 'staggs' ); ?></p>
									<div class="staggs-page-article-link">
										<a href="https://staggs.app/docs/create-product-configurator-wordpress/">
											<?php esc_attr_e( 'Default page', 'staggs' ); ?>
										</a>
										<a href="https://staggs.app/docs/using-shortcodes/">
											<?php esc_attr_e( 'Shortcode', 'staggs' ); ?>
										</a>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Configurator templates', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Customize the main layout of the configurator by setting a configurator template. Note: a configurator theme is required.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/configurator-templates/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Theme and colors', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Customize the configurator theme and colors.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/customizing-the-product-configurator/#heading-4">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Fonts', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Change the configurator template font families.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/customizing-the-product-configurator/#heading-8">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Icons', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Change the default configurator icons used in the template.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/customizing-the-product-configurator/#heading-7">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Custom CSS', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Modify configurator appearance using custom CSS rules.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/customizing-the-product-configurator/#heading-10">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="staggs-page-dashboard-main">
					<div class="staggs-page-dashboard-heading">
						<h2><?php esc_attr_e( 'Image gallery', 'staggs' ); ?></h2>
						<a href="">
							<?php esc_attr_e( 'View all articles', 'staggs' ); ?>
						</a>
					</div>
					<p><?php esc_attr_e( 'Manage your configurator gallery settings', 'staggs' ); ?></p>

					<div class="staggs-page-row">
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Stackable images', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Create the perfect product preview with stackable image layers.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/stacking-product-images/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Multi view layout', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Learn how to showcase the product from multiple angles.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/configurator-preview-images/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Custom text and images', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Learn how to show custom text and images inside the image gallery.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/display-input-previews-in-product-image/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="staggs-page-dashboard-main">
					<div class="staggs-page-dashboard-heading">
						<h2><?php esc_attr_e( '3D models', 'staggs' ); ?></h2>

						<?php if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) : ?>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sgg_attribute&page=staggs-pricing' ) ); ?>"><?php esc_attr_e( 'Upgrade to PRO', 'staggs' ); ?></a>
						<?php else: ?>
							<a href="https://staggs.app/docs/#3d-configurator">
								<?php esc_attr_e( 'View all articles', 'staggs' ); ?>
							</a>
						<?php endif; ?>
					</div>
					<p><?php esc_attr_e( 'Manage your 3D configurator gallery settings', 'staggs' ); ?></p>

					<div class="staggs-page-row">
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Update model materials', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Dynamically change 3D model appearance with custom colors or textures.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/change-3d-model-textures/">
										<?php esc_attr_e( 'View textures', 'staggs' ); ?>
									</a>
									<a href="https://staggs.app/docs/change-3d-model-colors/">
										<?php esc_attr_e( 'View colors', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Hide 3D model parts', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Learn how to control the 3D model parts visibility.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/how-to-set-3d-model-parts-display/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Custom text and images', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Learn how to apply custom text and images on the 3D model texture.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/apply-text-image-3d-model/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="staggs-page-dashboard-main">
					<div class="staggs-page-dashboard-heading">
						<h2><?php esc_attr_e( 'Configurator builder', 'staggs' ); ?></h2>
						<a href="https://staggs.app/docs/configurator-builder/">
							<?php esc_attr_e( 'Read full article', 'staggs' ); ?>
						</a>
					</div>
					<p><?php esc_attr_e( 'Explore ways to build your product configurator', 'staggs' ); ?></p>

					<div class="staggs-page-row">
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Collapsible attributes', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Mark single attributes as collapsible.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/configurator-builder/#heading-3">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Collapsible sections', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Create collapsible attribute sections.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/creating-collapsible-attributes/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Tabbed attributes', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Create a tabgroup of various attributes of the same type.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/configurator-builder/#heading-8">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Multi step form', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Create a multi step configurator form.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/multi-step-product-configurator/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Conditional logic', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Conditionally display attributes or options in the configurator form.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/conditional-logic/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Advanced price calculations', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Define custom formula\'s to calculate the configurator product total price.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/advanced-measurement-pricing/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="staggs-page-dashboard-main last">
					<h2><?php esc_attr_e( 'Form actions', 'staggs' ); ?></h2>
					<p><?php esc_attr_e( 'Manage your configurator form actions', 'staggs' ); ?></p>

					<div class="staggs-page-row">
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Send email', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Open up email with predefined list of configuration options', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/send-email-configuration-action/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article">
								<h3><?php esc_attr_e( 'Request quote', 'staggs' ); ?></h3>
								<p><?php esc_attr_e( 'Request a quote by linking configurator to a form.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/request-a-quote/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Download PDF', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Saves active configuration as PDF to computer.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/download-pdf/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Generate sharable link', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Generates a link which can be shared with other people.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/share-configuration-link/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="staggs-page-column">
							<div class="staggs-page-article<?php echo esc_attr( $pro_class ); ?>">
								<h3><?php esc_attr_e( 'Add to wishlist', 'staggs' ); ?><?php echo esc_attr( $pro_label ); ?></h3>
								<p><?php esc_attr_e( 'Saves the current product configuration to the customer account wishlist.', 'staggs' ); ?></p>
								<div class="staggs-page-article-link">
									<a href="https://staggs.app/docs/customer-account-wishlist/">
										<?php esc_attr_e( 'Read article', 'staggs' ); ?>
									</a>
								</div>
							</div>
						</div>
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
