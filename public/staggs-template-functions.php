<?php

/**
 * The main functions of this plugin.
 *
 * @link       https://staggs.app
 * @since      1.3.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

if ( ! function_exists( 'staggs_product_configurator_wrapper' ) ) {
	/**
	 * Product configurator wrapper.
	 *
	 * @return void
	 */
	function staggs_product_configurator_wrapper() {
		echo '<div class="staggs-container">';
	}
}

if ( ! function_exists( 'staggs_product_configurator_wrapper_close' ) ) {
	/**
	 * Product configurator wrapper close.
	 *
	 * @return void
	 */
	function staggs_product_configurator_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_message_content_wrapper' ) ) {
	/**
	 * Product configurator top content wrapper
	 *
	 * @return void
	 */
	function staggs_output_message_content_wrapper() {
		$theme_id = staggs_get_theme_id();
		$template = staggs_get_configurator_page_template( $theme_id );
		if ( $template !== 'staggs' ) {
			return;
		}

		$wrapper_class = '';
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' ) ) {
			$wrapper_class .= ' border-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' );
		}

		if ( 'popup' === staggs_get_post_meta( $theme_id, 'sgg_configurator_view' ) || 'default' === $template ) {
			$wrapper_class .= ' inline';
		}

		echo '<div class="staggs-message-wrapper' . esc_attr( $wrapper_class ) . '">';
	}
}

if ( ! function_exists( 'staggs_output_message_content_wrapper_close' ) ) {
	/**
	 * Product configurator top content wrapper close.
	 *
	 * @return void
	 */
	function staggs_output_message_content_wrapper_close() {
		$theme_id = staggs_get_theme_id();
		$template = staggs_get_configurator_page_template( $theme_id );
		if ( $template !== 'staggs' ) {
			return;
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_content_wrapper' ) ) {
	/**
	 * Product configurator content wrapper
	 *
	 * @return void
	 */
	function staggs_output_content_wrapper() {
		$staggs_class = 'staggs-configurator-main';

		$theme_id = staggs_get_theme_id();
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' ) ) {
			$staggs_class .= ' border-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' );
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_layout' ) ) {
			$staggs_class .= ' align-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_layout' );
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_nav_position' ) ) {
			$staggs_class .= ' steps-position-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_step_nav_position' );
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_totals_display_required' ) ) {
			$staggs_class .= ' hide-disabled-buttons';
		}
		if ( 'image_height' === staggs_get_post_meta( $theme_id, 'sgg_configurator_template_height' ) ) {
			$staggs_class .= ' staggs-configurator-height-auto';
		}

		if ( 'inline' === staggs_get_post_meta( $theme_id, 'sgg_mobile_gallery_display' ) ) {
			$staggs_class .= ' gallery-inline';
		} else {
			$staggs_class .= ' gallery-sticky';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_stretch_bg_image' ) ) {
			$staggs_class .= ' staggs-gallery-stretched';
		}

		$view_layout = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_view' ) );
		if ( 'classic' === $view_layout ) {
			$staggs_class .= ' staggs-contained';
		} elseif ( 'floating' === $view_layout ) {
			$staggs_class .= ' staggs-floating';
		} elseif ( 'steps' === $view_layout ) {
			$staggs_class .= ' staggs-stepper';
		} elseif ( 'splitter' === $view_layout ) {
			$staggs_class .= ' staggs-splitter';
		} else {
			$staggs_class .= ' staggs-full';
		}

		echo '<div class="' . esc_attr( $staggs_class ) . '">';
	}
}

if ( ! function_exists( 'staggs_output_content_wrapper_close' ) ) {
	/**
	 * Product configurator content wrapper
	 *
	 * @return void
	 */
	function staggs_output_content_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_topbar_wrapper' ) ) {
	/**
	 * Product configurator content wrapper
	 *
	 * @return void
	 */
	function staggs_output_topbar_wrapper() {
		$topbar_class = '';
		$theme_id = staggs_get_theme_id();
		if ( 'inline' === staggs_get_post_meta( $theme_id, 'sgg_mobile_gallery_display' ) ) {
			$topbar_class .= ' mobile-inline';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_separator_nav' ) ) {
			$topbar_class .= ' has-nav';
		}

		echo '<div class="staggs-configurator-topbar' . esc_attr( $topbar_class ) . '">';
	}
}

if ( ! function_exists( 'staggs_output_topbar_wrapper_close' ) ) {
	/**
	 * Product configurator content wrapper
	 *
	 * @return void
	 */
	function staggs_output_topbar_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_topbar_buttons' ) ) {
	/**
	 * Product configurator top bar buttons
	 *
	 * @return void
	 */
	function staggs_output_topbar_buttons() {
		echo '<div class="bar-action-buttons">';

		do_action( 'staggs_topbar_buttons' );

		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_company_logo' ) ) {
	/**
	 * Product configurator content wrapper
	 * 
	 * @return void
	 */
	function staggs_output_company_logo() {
		$theme_id = staggs_get_theme_id();
		if ( ! staggs_get_post_meta( $theme_id, 'sgg_show_logo' ) ) {
			return;
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_show_logo_options' ) ) {
			return;
		}

		$logo_id = '';
		if ( staggs_get_theme_option( 'sgg_logo' ) ) {
			// Fallback.
			$logo_id = esc_attr( staggs_get_theme_option( 'sgg_logo' ) );
		}

		if ( $logo_id ) :
			$logo_url = esc_url( home_url() );
			if ( staggs_get_post_meta( $theme_id, 'sgg_back_page_url' ) ) {
				$logo_url = esc_url( staggs_get_post_meta( $theme_id, 'sgg_back_page_url' ) );
			}
			?>
			<a href="<?php echo esc_url( $logo_url ); ?>" class="logo-wrapper">
				<span class="logo">
					<?php echo wp_get_attachment_image( esc_attr( $logo_id ), 'full' ); ?>
				</span>
			</a>
			<?php
		endif;
	}
}

if ( ! function_exists( 'staggs_product_single_options_logo' ) ) {
	/**
	 * Product configurator content wrapper
	 * 
	 * @return void
	 */
	function staggs_product_single_options_logo() {
		$theme_id = staggs_get_theme_id();
		if ( ! staggs_get_post_meta( $theme_id, 'sgg_show_logo' ) ) {
			return;
		}
		if ( ! staggs_get_post_meta( $theme_id, 'sgg_show_logo_options' ) ) {
			return;
		}

		$logo_id = '';
		if ( staggs_get_theme_option( 'sgg_logo' ) ) {
			$logo_id = esc_attr( staggs_get_theme_option( 'sgg_logo' ) );
		}

		if ( $logo_id ) :
			$logo_url = esc_url( home_url() );
			if ( staggs_get_post_meta( $theme_id,'sgg_back_page_url' ) ) {
				$logo_url = esc_url( staggs_get_post_meta( $theme_id,'sgg_back_page_url' ) );
			}
			?>
			<a href="<?php echo esc_url( $logo_url ); ?>" class="logo-wrapper">
				<span class="logo">
					<?php echo wp_get_attachment_image( esc_attr( $logo_id ), 'full' ); ?>
				</span>
			</a>
			<?php
		endif;
	}
}

if ( ! function_exists( 'staggs_product_single_options_back_button' ) ) {
	/**
	 * Product configurator content back button
	 * 
	 * @return void
	 */
	function staggs_product_single_options_back_button() {
		$theme_id = staggs_get_theme_id();
		if ( ! staggs_get_post_meta( $theme_id, 'sgg_show_close_button' ) ) {
			return;
		}
		
		$close_url = esc_url( home_url() );
		if ( staggs_get_post_meta( $theme_id,'sgg_back_page_url' ) ) {
			$close_url = esc_url( staggs_get_post_meta( $theme_id,'sgg_back_page_url' ) );
		}
		$close_icon = staggs_get_icon( 'sgg_close_icon', 'close' );
		$attr = '';
		if ( staggs_get_post_meta( $theme_id,'sgg_show_close_button_message' ) ) {
			$exit = staggs_get_post_meta( $theme_id, 'sgg_close_button_message' ) ?: __( 'Do you want to exit?', 'staggs' );
			$attr = ' data-message="' . esc_attr( $exit ) . '"';
		}
		?>
		<a href="<?php echo esc_url( $close_url ); ?>" class="back-button"<?php echo wp_kses_normalize_entities(  $attr ); ?>>
			<?php echo wp_kses_normalize_entities(  $close_icon ); ?>
		</a>
		<?php
	}
}

if ( ! function_exists( 'staggs_output_gallery_section' ) ) {
	/**
	 * Main product configurator gallery section wrapper
	 * 
	 * @return void
	 */
	function staggs_output_gallery_section() {
		$theme_id = staggs_get_theme_id();

		$class = ' staggs-product-view-' . staggs_get_post_meta( get_the_ID(), 'sgg_configurator_type' );
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' ) ) {
			$class .= ' border-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' );
		}
		if ( 'inline' === staggs_get_post_meta( $theme_id, 'sgg_mobile_gallery_display' ) ) {
			$class .= ' mobile-inline';
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_gallery_scale_mobile_display' ) ) {
			$class .= ' fix-mobile-view';
		}
		$view_layout = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_view' ) );
		if ( 'classic' === $view_layout && staggs_get_post_meta( $theme_id, 'sgg_configurator_gallery_sticky' ) ) {
			$class .= ' staggs-product-view-sticky';
		}

		echo '<section class="staggs-product-view' . esc_attr( $class ) . '">';
		echo '<div class="product-view-inner">';
	}
}

if ( ! function_exists( 'staggs_output_gallery_section_close' ) ) {
	/**
	 * Main product configurator gallery section wrapper close
	 * 
	 * @return void
	 */
	function staggs_output_gallery_section_close() {
		echo '</div>';
		echo '</section>';
	}
}

if ( ! function_exists( 'staggs_output_preview_gallery_wrapper' ) ) {
	/**
	 * Product configurator gallery wrapper
	 * 
	 * @return void
	 */
	function staggs_output_preview_gallery_wrapper() {
		$bg_image_data = '';
		$gallery_class = 'staggs-view-gallery';
		$theme_id = staggs_get_theme_id();

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' ) ) {
			$gallery_class .= ' border-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' );
		}

		$image_ids = staggs_get_image_ids();
		if ( '3dmodel' !== staggs_get_post_meta( get_the_ID(), 'sgg_configurator_type' ) || $image_ids > 0 ) {
			$gallery_class .= ' swiper';
			$bg_image_urls = get_configurator_background_urls();

			if ( count( $bg_image_urls ) > 0 ) {
				$bg_image_data = ' data-backgrounds="' . implode( '|', $bg_image_urls ) . '"';
			}
		}
		
		if ( staggs_get_post_meta( $theme_id, 'sgg_capture_bg_image' ) ) {
			$bg_image_data .= ' data-include-bg="1"';
		}

		echo '<figure id="staggs-preview" class="' . esc_attr( $gallery_class ) . '"' . wp_kses_normalize_entities( $bg_image_data ) . '>';

		if ( staggs_get_post_meta( $theme_id, 'sgg_gallery_fullscreen_display' )
			|| staggs_get_post_meta( $theme_id, 'sgg_gallery_camera_display' )
			|| staggs_get_post_meta( $theme_id, 'sgg_gallery_wishlist_display' )
			|| staggs_get_post_meta( $theme_id, 'sgg_gallery_pdf_display' )
			|| staggs_get_post_meta( $theme_id, 'sgg_gallery_share_display' )
			|| staggs_get_post_meta( $theme_id, 'sgg_gallery_reset_display' ) ) {

			echo '<div class="staggs-preview-actions">';
			
			if ( staggs_get_post_meta( $theme_id, 'sgg_gallery_fullscreen_display' ) ) {
				// Display request invoice as main button for cart button is disabled.
				$fullscreen_text = sanitize_text_field( staggs_get_theme_option( 'sgg_gallery_fullscreen_label' ) );
				if ( '' === $fullscreen_text ) {
					$fullscreen_text = __( 'Toggle fullscreen mode', 'staggs' );
				}

				echo sprintf(
					'<button class="preview-action fullscreen">
						<span class="expand-fullscreen">%s</span>
						<span class="close-fullscreen">%s</span>
						<span class="button-label">%s</span>
					</button>',
					wp_kses_normalize_entities( staggs_get_icon( 'sgg_fullscreen_icon', 'fullscreen' ) ),
					wp_kses_normalize_entities( staggs_get_icon( 'sgg_fullscreen_icon', 'fullscreen-close' ) ),
					esc_attr( $fullscreen_text )
				);
			}

			if ( staggs_get_post_meta( $theme_id, 'sgg_gallery_camera_display' ) ) {
				// Display request invoice as main button for cart button is disabled.
				$camera_text = sanitize_text_field( staggs_get_theme_option( 'sgg_gallery_camera_label' ) );
				if ( '' === $camera_text ) {
					$camera_text = __( 'Capture configuration image', 'staggs' );
				}

				echo sprintf(
					'<button class="preview-action capture-image">
						<span class="image-icon">%s</span>
						<span class="button-label">%s</span>
					</button>',
					wp_kses_normalize_entities(  staggs_get_icon( 'sgg_camera_icon', 'camera' ) ), 
					esc_attr( $camera_text )
				);
			}

			if ( staggs_get_post_meta( $theme_id, 'sgg_gallery_wishlist_display' ) ) {
				// Display request invoice as main button for cart button is disabled.
				$wishlist_text = sanitize_text_field( staggs_get_theme_option( 'sgg_gallery_wishlist_label' ) );
				if ( '' === $wishlist_text ) {
					$wishlist_text = __( 'Add to my configurations', 'staggs' );
				}

				echo sprintf(
					'<button class="preview-action wishlist-toggle" data-product="%s">
						<span class="wishlist-icon">%s</span>
						<span class="button-label">%s</span>
					</button>',
					esc_attr( get_the_ID() ),
					wp_kses_normalize_entities(  staggs_get_icon( 'sgg_wishlist_icon', 'heart' ) ),
					esc_attr( $wishlist_text )
				);
			}

			if ( staggs_get_post_meta( $theme_id, 'sgg_gallery_pdf_display' ) ) {
				// Display request invoice as main button for cart button is disabled.
				$invoice_text = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_step_request_invoice_text' ) );
				if ( '' === $invoice_text ) {
					$invoice_text = sanitize_text_field( staggs_get_theme_option( 'sgg_gallery_pdf_label' ) );
				}
				if ( '' === $invoice_text ) {
					$invoice_text = __( 'Download to PDF', 'staggs' );
				}

				echo sprintf(
					'<form action="%s" method="post">
						<button id="download_pdf" data-product="%s" class="preview-action download-pdf">
							<span class="pdf-icon">%s</span>
							<span class="button-label">%s</span>
						</button>
					</form>',
					esc_url( get_permalink( get_the_ID() ) . '?generate_pdf=' . get_the_ID() ),
					esc_attr( get_the_ID() ),
					wp_kses_normalize_entities( staggs_get_icon( 'sgg_group_pdf_icon', 'invoice' ) ),
					esc_attr( $invoice_text )
				);
			}

			if ( staggs_get_post_meta( $theme_id, 'sgg_gallery_share_display' ) ) {
				$save_text = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_step_save_button_text' ) );
				if ( '' === $save_text ) {
					$save_text = sanitize_text_field( staggs_get_theme_option( 'sgg_gallery_share_label' ) );
				}
				if ( '' === $save_text ) {
					$save_text = __( 'Get configuration link', 'staggs' );
				}

				echo sprintf(
					'<button class="preview-action share-link">
						<span class="link-icon">%s</span>
						<span class="button-label">%s</span>
					</button>',
					wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_save_icon', 'save' ) ),
					esc_attr( $save_text )
				);
			}

			if ( staggs_get_post_meta( $theme_id, 'sgg_gallery_reset_display' ) ) {
				// Display request invoice as main button for cart button is disabled.
				$reset_text = sanitize_text_field( staggs_get_theme_option( 'sgg_gallery_reset_label' ) );
				if ( '' === $reset_text ) {
					$reset_text = __( 'Reset configuration', 'staggs' );
				}

				echo sprintf(
					'<button class="preview-action reset-toggle" data-product="%s">
						<span class="reset-icon">%s</span>
						<span class="button-label">%s</span>
					</button>',
					esc_attr( get_the_ID() ), 
					wp_kses_normalize_entities( staggs_get_icon( 'sgg_reset_icon', 'reset' ) ),
					esc_attr( $reset_text )
				);
			}

			echo '</div>';
		}
	}
}

if ( ! function_exists( 'staggs_output_preview_gallery_wrapper_close' ) ) {
	/**
	 * Product configurator gallery wrapper close
	 * 
	 * @return void
	 */
	function staggs_output_preview_gallery_wrapper_close() {
		echo '</figure>';
	}
}

if ( ! function_exists( 'staggs_output_preview_gallery' ) ) {
	/**
	 * Product configurator gallery
	 * 
	 * @return void
	 */
	function staggs_output_preview_gallery() {
		global $product, $image_ids, $sanitized_steps;

		/**
		 * Regular image slides.
		 */
		$image_ids = staggs_get_image_ids();

		if ( count( $image_ids ) > 0 ) {
			echo '<div class="staggs-view-gallery__images swiper-wrapper">';
			foreach ( $image_ids as $index => $image_id ) {
				// $url = wp_get_attachment_image_url( $image_id, 'full' );
				// if ( strpos( $url, '.svg' ) !== -1 ) {
				// 	$img = file_get_contents( str_replace( get_site_url(), ABSPATH, $url ) );
				// } else {
				// 	$img = '<img id="preview_' . esc_attr( $index ) . '_preview" src="' . $url . '">';
				// }

				echo '<div class="swiper-slide">
					<div id="preview_slide_' . esc_attr( $index ) . '" class="staggs-view-gallery__image">
						<img id="preview_' . esc_attr( $index ) . '_preview" src="' . esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ) . '">
					</div>
				</div>';
			}
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'staggs_output_preview_gallery_thumbnails' ) ) {
	/**
	 * Product configurator gallery thumbnails
	 * 
	 * @return void
	 */
	function staggs_output_preview_gallery_thumbnails() {
		global $image_ids;

		$theme_id     = staggs_get_theme_id();
		$nav_location = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails_align' ) );
		$nav_position = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails_position' ) );	
		$nav_layout   = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails_layout' ) );
		$nav_class    = '';
		if ( '' !== $nav_location ) {
			$nav_class .= ' product-view-nav--' . $nav_location;
		}
		if ( '' !== $nav_position ) {
			$nav_class .= ' product-view-nav--' . $nav_position;
		}
		if ( '' !== $nav_layout ) {
			$nav_class .= ' product-view-nav--show-' . $nav_layout;
		}

		// Check if multiple preview images set.
		if ( '3dmodel' === staggs_get_post_meta( $theme_id, 'sgg_configurator_gallery_type' ) ) {
			$nav_labels = staggs_get_post_meta( $theme_id, 'sgg_configurator_model_controls' );

			$properties = array(
				'position' => 'sgg_control_position',
				'normal' => 'sgg_control_normal',
				'background' => 'sgg_control_background',
				'exposure' => 'sgg_control_exposure'
			);
			?>
			<div class="product-view-nav product-view-nav--thumbnails<?php echo $nav_class; ?>">
				<div class="view-nav-buttons">
					<?php
					foreach ( $nav_labels as $index => $label ) {
						$attributes = '';
						foreach ( $properties as $data_key => $prop ) {
							if ( isset( $label[ $prop ] ) && '' !== $label[ $prop ] ) {
								$attributes .= ' data-' . $data_key . '="' . sanitize_text_field( $label[ $prop ] ) . '"';
							}
						}

						if ( isset( $label['sgg_control_thumbnail'] ) && '' !== $label['sgg_control_thumbnail'] ) {
							$image_id = sanitize_key( $label['sgg_control_thumbnail'] );

							echo '<button data-key="' . esc_attr( $index ) . '"' . wp_kses_normalize_entities( $attributes ) . '>
								<img src="' . esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ) . '">
							</button>';
						}
					}
					?>
				</div>
			</div>
			<?php
		} else {
			// No image previews set.
			if ( ! is_array( $image_ids ) ) {
				return;
			}

			if ( count( $image_ids ) > 1 && 'thumbnails' === staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails' ) ) {
				?>
				<div class="product-view-nav product-view-nav--thumbnails<?php echo esc_attr( $nav_class ); ?>">
					<div class="view-nav-buttons">
						<?php
						foreach ( $image_ids as $index => $image_id ) {
							echo '<button id="preview_nav_' . esc_attr( $index ) . '" data-key="' . esc_attr( $index ) . '">
								<img id="button_preview_' . esc_attr( $index ) . '_preview" src="' . esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ) . '">
							</button>';
						}
						?>
					</div>
				</div>
				<?php
			}
		}
	}
}

if ( ! function_exists( 'staggs_output_preview_gallery_nav' ) ) {
	/**
	 * Product configurator gallery contents
	 * 
	 * @return void
	 */
	function staggs_output_preview_gallery_nav() {
		global $image_ids;

		// No image previews set.
		if ( ! is_array( $image_ids ) ) {
			return;
		}

		$theme_id = staggs_get_theme_id();
		if ( 'labels' === staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails' ) ) {
			$nav_location = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails_align' ) );
			$nav_position = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails_position' ) );	
			$nav_layout   = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails_layout' ) );
			$nav_class    = '';
			if ( '' !== $nav_location ) {
				$nav_class .= ' product-view-nav--' . $nav_location;
			}
			if ( '' !== $nav_position ) {
				$nav_class .= ' product-view-nav--' . $nav_position;
			}
			if ( '' !== $nav_layout ) {
				$nav_class .= ' product-view-nav--show-' . $nav_layout;
			}
			$properties = array(
				'position' => 'sgg_control_position',
				'normal' => 'sgg_control_normal',
				'background' => 'sgg_control_background',
				'exposure' => 'sgg_control_exposure'
			);
	
			if ( '3dmodel' ===  staggs_get_post_meta( $theme_id, 'sgg_configurator_gallery_type' ) ) {
				$nav_labels = staggs_get_post_meta( $theme_id, 'sgg_configurator_model_controls' );
				?>
				<div class="product-view-nav product-view-nav--labels<?php echo esc_attr( $nav_class ); ?>">
					<div class="view-nav-buttons">
						<?php
						foreach ( $nav_labels as $index => $label ) {
							$attributes = '';
							foreach ( $properties as $data_key => $prop ) {
								if ( isset( $label[ $prop ] ) && '' !== $label[ $prop ] ) {
									$attributes .= ' data-' . $data_key . '="' . sanitize_text_field( $label[ $prop ] ) . '"';
								}
							}
							echo '<button id="preview_nav_label_' . esc_attr( $index ) . '" data-key="' . esc_attr( $index ) . '"' . wp_kses_normalize_entities( $attributes ) . '>' . sanitize_text_field( $label['sgg_control_label'] ) . '</button>';
						}
						?>
					</div>
				</div>
				<?php
			} else {
				$nav_labels = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnail_labels' ) );
				$nav_labels = explode( ',', $nav_labels );
				?>
				<div class="product-view-nav product-view-nav--labels<?php echo esc_attr( $nav_class ); ?>">
					<div class="view-nav-buttons">
						<?php
						foreach ( $nav_labels as $index => $label ) {
							echo '<button id="preview_nav_label_' . esc_attr( $index ) . '" data-key="' . esc_attr( $index ) . '">' . esc_attr( $label ) . '</button>';
						}
						?>
					</div>
				</div>
				<?php
			}
		}

		// Output arrows by default.
		$arrow_location = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_arrows' ) );
		if ( 'none' !== $arrow_location ) {
			?>
			<div class="swiper-button-prev swiper-button-prev--<?php echo esc_attr( $arrow_location ); ?>"></div>
			<div class="swiper-button-next swiper-button-next--<?php echo esc_attr( $arrow_location ); ?>"></div>
			<?php
		}
	}
}

if ( ! function_exists( 'staggs_product_single_title' ) ) {
	/**
	 * Product configurator title
	 *
	 * @return void
	 */
	function staggs_product_single_title() {
		echo '<h1 class="product_title entry-title">' . esc_attr( get_the_title() ) . '</h1>';
	}
}

if ( ! function_exists( 'staggs_product_single_description' ) ) {
	/**
	 * Product configurator summary
	 *
	 * @return void
	 */
	function staggs_product_single_description() {
		the_content();
	}
}

if ( ! function_exists( 'staggs_output_options_wrapper' ) ) {
	/**
	 * Product configurator options wrapper
	 *
	 * @return void
	 */
	function staggs_output_options_wrapper() {
		global $sanitized_steps, $sgg_minus_button, $sgg_plus_button;
		if ( ! is_array( $sanitized_steps ) ) {	
			$sanitized_steps = Staggs_Formatter::get_formatted_step_content( get_the_ID() );
		}

		$is_basic = false;
		$theme_id = staggs_get_theme_id();
		if ( 'staggs' !== staggs_get_configurator_page_template( $theme_id ) ) {
			$is_basic = staggs_get_post_meta( $theme_id, 'sgg_disable_attribute_styles' );
		}

		$sgg_minus_button = '';
		$sgg_plus_button = '';
		if ( staggs_get_theme_option('sgg_product_number_input_show_icons') ) {
			$sgg_minus_button = '<a href="#0" class="button-minus">' . wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_minus_icon', 'minus' ) ) . '</a>';
			$sgg_plus_button = '<a href="#0" class="button-plus">' . wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_plus_icon', 'plus' ) ) . '</a>';
		}

		if ( $is_basic ) {
			/**
			 * Applies basic attribute styles.
			 */
			echo '<div class="staggs-product-options-basic">';
			echo '<div class="option-group-wrapper-basic">';
		} else {
			/**
			 * Applies full Staggs attribute styles.
			 */
			$aside_class = '';
			if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_density' ) ) {
				$aside_class .= ' ' . staggs_get_post_meta( $theme_id, 'sgg_configurator_step_density' );
			}
			if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_text_align' ) ) {
				$aside_class .= ' text-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_text_align' );
			}
			if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' ) ) {
				$aside_class .= ' border-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' );
			}
	
			echo '<div class="staggs-product-options' . esc_attr( $aside_class ) .'">';
			echo '<div class="option-group-wrapper">';
		}
	}
}

if ( ! function_exists( 'staggs_output_options_wrapper_close' ) ) {
	/**
	 * Product configurator options wrapper close
	 *
	 * @return void
	 */
	function staggs_output_options_wrapper_close() {
		echo '</div>';
		echo '</div>';
	}
}

add_action( 'staggs_single_product_options_totals', 'staggs_get_inline_form', 20 );

if ( ! function_exists( 'staggs_get_inline_form' ) ) {
	function staggs_get_inline_form() {
		$theme_id  = staggs_get_theme_id();

		if ( 'invoice' !== staggs_get_post_meta( $theme_id, 'sgg_configurator_button_type' ) ) {
			return;
		}

		if ( 'inline' !== staggs_get_post_meta( $theme_id, 'sgg_configurator_form_display' ) ) {
			return;
		}

		if ( 'new_page' === staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_location' ) ) {
			return;
		}

		echo do_shortcode( staggs_get_post_meta( $theme_id, 'sgg_configurator_form_shortcode' ) );
	}
}

if ( ! function_exists( 'staggs_output_options_form' ) ) {
	/**
	 * Product configurator options form
	 *
	 * @return void
	 */
	function staggs_output_options_form() {
		global $original_post_id;
		echo '<form method="post" id="configurator-options" enctype="multipart/form-data">';

		if ( $original_post_id ) {
			echo '<input type="hidden" name="original_post_id" value="' . esc_attr( $original_post_id ) . '">';
		}
	}
}

if ( ! function_exists( 'staggs_output_options_form_close' ) ) {
	/**
	 * Product configurator options form close
	 *
	 * @return void
	 */
	function staggs_output_options_form_close() {
		echo '</form>';
	}
}

if ( ! function_exists( 'staggs_output_single_product_options' ) ) {
	/**
	 * Product configurator options
	 *
	 * @return void
	 */
	function staggs_output_single_product_options() {
		global $sanitized_steps, $sanitized_step, $step_count, $density, $text_align, $description_type, $sgg_is_admin;

		$theme_id          = staggs_get_theme_id();
		$step_count        = 0;
		$sgg_is_admin      = ( is_user_logged_in() && current_user_can('administrator') ) && staggs_get_theme_option( 'sgg_admin_display_edit_links' );;
		$hide_inline_title = staggs_get_post_meta( $theme_id, 'sgg_step_hide_inline_option_step_title' );
		$separator_type    = staggs_get_post_meta( $theme_id, 'sgg_configurator_step_separator_function' );
		$indicator         = staggs_get_post_meta( $theme_id, 'sgg_configurator_step_indicator' );
		$density           = staggs_get_post_meta( $theme_id, 'sgg_configurator_step_density' );
		$text_align        = staggs_get_post_meta( $theme_id, 'sgg_configurator_text_align' );
		$description_type  = staggs_get_post_meta( $theme_id, 'sgg_configurator_step_description_type' );

		/**
		 * Main Configurable Product Steps.
		 */
		if ( is_array( $sanitized_steps ) && count( $sanitized_steps ) > 0 ) {

			foreach ( $sanitized_steps as $step_key => $step_attribute ) {

				$sanitized_step = $step_attribute;

				if ( 'separator' === $step_attribute['type'] ) {

					$class = '';
					if ( $step_count > 0 ) {
						echo '</div>';
						echo '</div>';
					}

					if ( $step_attribute['collapsible'] ) {
						$class .= ' collapsible';

						if ( 'collapsed' === $step_attribute['state'] ) {
							$class .= ' collapsed';
						}
					} else {
						if ( $step_count > 0 && 'stepper' === $separator_type ) {
							$class .= ' hidden';
						}
					}

					$step_count++;

					echo '<div class="option-group-step' . esc_attr( $class ) . '" data-step-group-id="' . esc_attr( $step_attribute['number'] ) . '">';

					if ( ! $hide_inline_title ) {
						echo '<div class="option-group-step-title">';

						echo '<p class="option-group-title">';
						if ( 'one' === $indicator ) {
							echo '<span class="step-number">' . esc_attr( $step_attribute['number'] ) . '</span>';
							echo '<span>' . esc_attr( $step_attribute['title'] ) . '</span>';
						} else {
							echo '<span>';
							if ( 'two' === $indicator ) {
								echo esc_attr( $step_attribute['number'] );
								echo esc_attr( apply_filters( 'staggs_step_nav_number_mark', '. ' ) );
							}
							echo esc_attr( $step_attribute['title'] );
							echo '</span>';
						}
						echo '</p>';

						if ( $step_attribute['collapsible'] ) {
							$collapse_icon_url = staggs_get_icon( 'sgg_separator_collapse_icon', 'collapse', true );
							echo '<img src="' . esc_url( $collapse_icon_url ) . '" alt="Arrow">';
						}

						echo '</div>';
					}

					echo '<div class="option-group-step-inner">';

				} else if ( 'tabs' === $step_attribute['type'] ) {

					include STAGGS_BASE . 'public/templates/shared/tab.php';

				} else if ( 'repeater' === $step_attribute['type'] ) {
					
					if ( $step_attribute['is_conditional'] && count( $step_attribute['conditional_rules'] ) > 0 ) {
						echo '<div class="conditional-wrapper" data-step-id="repeater-' . esc_attr( staggs_sanitize_title( $step_attribute['text_title'] ) ) . '" data-step-type="repeater" data-step-rules="' . wp_kses_normalize_entities(  urldecode( str_replace( '"', "'", wp_json_encode( $step_attribute['conditional_rules'] ) ) ) ) . '">';
						include STAGGS_BASE . 'public/templates/shared/repeater.php';
						echo '</div>';
					} else {
						include STAGGS_BASE . 'public/templates/shared/repeater.php';
					}

				} else if ( 'summary' === $step_attribute['type'] ) {

					echo do_shortcode('[staggs_configurator_summary]');

				} else if ( 'html' === $step_attribute['type'] ) {

					echo wp_kses_normalize_entities(  $step_attribute['html'] );

				} else {

					if ( $step_attribute['is_conditional'] && count( $step_attribute['conditional_rules'] ) > 0 ) {
						echo '<div class="conditional-wrapper" data-step-id="' . esc_attr( $step_attribute['id'] ) . '" data-step-rules="' . wp_kses_normalize_entities(  urldecode( str_replace( '"', "'", wp_json_encode( $step_attribute['conditional_rules'] ) ) ) ) . '">';
						include 'templates/shared/attribute.php';
						echo '</div>';
					} else {
						include 'templates/shared/attribute.php';
					}
				}
			}

			if ( $step_count > 0 ) {
				echo '</div>';
				echo '</div>';
			}
		}
	}
}

if ( ! function_exists( 'staggs_output_option_group_header' ) ) {
	/**
	 * Product configurator option group header.
	 *
	 * @return void
	 */
	function staggs_output_option_group_header() {
		global $sanitized_step, $text_align, $density, $is_horizontal_popup, $description_type, $sgg_is_admin;
		
		if ( ! ( 'true-false' === $sanitized_step['type'] && 'toggle' === $sanitized_step['button_view'] && 'left' === $text_align ) ) :
			?>
			<div class="option-group-header">
				<div class="option-group-title">
					<strong class="title">
						<?php 
						echo esc_attr( $sanitized_step['title'] );
						if ( isset( $sanitized_step['required'] ) && 'yes' === $sanitized_step['required'] ) {
							echo '<span class="required-indicator">*</span>';
						}
						?>
					</strong>
					<?php 
					if ( $sgg_is_admin ) {
						echo '<small><a href="' . esc_url( admin_url('post.php?post=' . $sanitized_step['id'] . '&action=edit' ) ) . '">(' . esc_attr__( 'Edit', 'staggs' ) . ')</a></small>'; 
					}

					if ( $sanitized_step['description'] ) : 
						if ( 'tooltip' === $description_type ) {
							?>
							<a href="#0" class="show-panel tooltip" aria-label="<?php esc_attr_e( 'Show description', 'staggs' ); ?>">
								<?php
								echo wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_info_icon', 'panel-info' ) );
								?>
							</a>
							<div class="option-group-tooltip-description">
								<?php echo wp_kses_normalize_entities(  $sanitized_step['description'] ); ?>
							</div>
							<?php
						} else {
							?>
							<a href="#0" class="show-panel" aria-label="<?php esc_attr_e( 'Show description', 'staggs' ); ?>">
								<?php
								echo wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_info_icon', 'panel-info' ) );
								?>
							</a>
							<?php
						}
				 	endif;

					$show_summary = ( 'show' === $sanitized_step['show_summary'] );
					if ( $show_summary && 'compact' === $density ) : 
						?>
						<p class="option-group-summary"><span class="name"></span> <span class="value"></span></p>
						<?php
					endif;
					?>
				</div>
				<?php
				if ( $sanitized_step['short_description'] ) :
					?>
					<div class="option-group-description">
						<?php echo '<p>' . wp_kses_normalize_entities(  $sanitized_step['short_description'] ) . '</p>'; ?>
					</div>
					<?php 
				endif; 

				if ( isset( $sanitized_step['collapsible'] ) && $sanitized_step['collapsible'] && ! $is_horizontal_popup ) :
					?>
					<div class="option-group-icon">
						<?php 
						$collapse_icon_url = staggs_get_icon( 'sgg_separator_collapse_icon', 'collapse', true );
						echo '<img src="' . esc_url( $collapse_icon_url ) . '" alt="Arrow">';
						?>
					</div>
					<?php 
				endif; 
				?>
			</div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'staggs_output_option_group_content' ) ) {
	/**
	 * Product configurator option group step content.
	 *
	 * @return void
	 */
	function staggs_output_option_group_content() {
		global $step_key, $sanitized_step;

		$file_path = STAGGS_BASE . '/public/partials/' . $sanitized_step['type'] . '.php';
		if ( file_exists( $file_path ) ) {
			include $file_path;
		}
	}
}

if ( ! function_exists( 'staggs_output_option_tab_content' ) ) {
	/**
	 * Product configurator option tab step content.
	 *
	 * @return void
	 */
	function staggs_output_option_tab_content() {
		global $step_key, $sanitized_step;

		$file_path = STAGGS_BASE . '/public/partials/tabs.php';
		if ( file_exists( $file_path ) ) {
			include $file_path;
		}
	}
}

if ( ! function_exists( 'staggs_output_option_group_summary' ) ) {
	/**
	 * Product configurator single option group summary
	 *
	 * @return void
	 */
	function staggs_output_option_group_summary() {
		global $sanitized_step, $density;

		$show_summary = ( 'show' === $sanitized_step['show_summary'] );
		if ( $show_summary && 'compact' !== $density ) :
			?>
			<p class="option-group-summary"><span class="name"></span><span class="value"></span></p>
			<?php 
		endif;
	}
}

if ( ! function_exists( 'staggs_option_group_description_panel' ) ) {
	/**
	 * Product configurator options cart button.
	 *
	 * @return void
	 */
	function staggs_option_group_description_panel() {
		global $sanitized_step, $description_type;

		if ( isset( $sanitized_step['description'] ) && $sanitized_step['description'] && 'tooltip' !== $description_type ) :
			?>
			<div id="description-panel-<?php echo esc_attr( $sanitized_step['id'] ); ?>" class="option-group-panel">
				<div class="option-group-panel-header">
					<p><strong><?php echo esc_attr( $sanitized_step['title'] ); ?></strong></p>
					<a href="#0" class="close-panel" aria-label="<?php esc_attr_e( 'Hide description', 'staggs' ); ?>">
						<?php
						echo wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_close_icon', 'panel-close' ) );
						?>
					</a>
				</div>
				<div class="option-group-panel-content">
					<?php echo wp_kses_normalize_entities(  $sanitized_step['description'] ); ?>
				</div>
			</div>
			<?php 
		endif;
	}
}

if ( ! function_exists( 'staggs_output_description_panels' ) ) {
	/**
	 * Product configurator description panels
	 *
	 * @return void
	 */
	function staggs_output_description_panels() {
		global $sanitized_steps, $sanitized_step;

		if ( is_array( $sanitized_steps ) && count( $sanitized_steps ) > 0 ) {
			echo '<div class="staggs-configurator-panels-wrapper">';

			foreach ( $sanitized_steps as $step_key => $sanitized_step ) {
				// Output description panel.
				staggs_option_group_description_panel();
			}

			echo '</div>';
		}
	}
}

if ( ! function_exists( 'staggs_output_bottom_bar_wrapper' ) ) {
	/**
	 * Product configurator bottom bar wrapper
	 *
	 * @return void
	 */
	function staggs_output_bottom_bar_wrapper() {
		$bottombar_class = '';
		$theme_id = staggs_get_theme_id();
		if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_sticky_button_mobile' ) ) {
			$bottombar_class .= ' mobile-inline';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_sticky_button_mobile' ) ) {
			echo '<div class="staggs-configurator-bottom-bar-spacer"></div>';
		}

		echo '<div class="staggs-configurator-bottom-bar' . esc_attr( $bottombar_class ) . '">';
	}
}

if ( ! function_exists( 'staggs_output_bottom_bar_wrapper_close' ) ) {
	/**
	 * Product configurator bottom bar wrapper close
	 *
	 * @return void
	 */
	function staggs_output_bottom_bar_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_bottom_bar_info_wrapper' ) ) {
	/**
	 * Product configurator bottom bar info wrapper
	 *
	 * @return void
	 */
	function staggs_output_bottom_bar_info_wrapper() {
		echo '<div class="bottom-bar-info">';
	}
}

if ( ! function_exists( 'staggs_output_bottom_bar_info_wrapper_close' ) ) {
	/**
	 * Product configurator bottom bar info wrapper close
	 *
	 * @return void
	 */
	function staggs_output_bottom_bar_info_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_options_summary_widget' ) ) {
	/**
	 * Product configurator summary widget
	 *
	 * @return void
	 */
	function staggs_output_options_summary_widget() {
		$theme_id = staggs_get_theme_id();
		if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_display_summary' ) ) {
			return;
		}

		$summary_title = staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_title' );
		if ( '' == $summary_title ) {
			$summary_title = 'Your configuration';
		}

		$hidden_items = staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_hidden_items' ) ?: '';
		if ( $hidden_items ) {
			$hidden_items = ' data-hidden="' . esc_attr( $hidden_items ) . '"';
		} 
		?>
		<div class="staggs-summary-widget">
			<strong class="staggs-summary-title"><?php echo esc_attr( $summary_title ); ?></strong>
			<ul class="staggs-summary-items"<?php echo wp_kses_normalize_entities(  $hidden_items ); ?>></ul>
		</div>
		<?php
	}
}

if ( ! function_exists( 'staggs_single_product_options_totals_wrapper' ) ) {
	/**
	 * Product configurator options cart button.
	 *
	 * @return void
	 */
	function staggs_single_product_options_totals_wrapper() {
		global $button_sticky, $totals_display;

		/**
		 * Hook: staggs_before_single_product_options_totals
		 *
		 * @hooked -
		 */
		do_action( 'staggs_before_single_product_options_totals' );	
	
		$theme_id       = staggs_get_theme_id();
		$button_class   = ' total';
		$button_attr    = '';
		$button_sticky  = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_sticky_button' ) );
		$totals_display = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_totals_display' ) );

		if ( $button_sticky && 'always' === $totals_display ) {
			$button_class .= ' fixed';
		}

		if ( 'end' === $totals_display ) {
			$button_class .= ' hidden';
			$button_attr = ' data-show-step="final"';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_totals_display_required' ) ) {
			$button_attr .= ' data-step-valid="required"';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_display_qty_total' ) ) {
			$button_attr .= ' data-qty-totals="1"';
		}

		if ( 'table' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_calculation' ) ) {
			$table_id = staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_table' );
			if ( $table_id ) {
				$button_attr .= ' data-table-id="' . esc_attr( $table_id ) . '"';
			}
		}

		$qty_input_label = staggs_get_post_meta( $theme_id, 'sgg_configurator_qty_input_label' );
		if ( $qty_input_label ) {
			$button_attr .= ' data-quantity-id="' . esc_attr( $qty_input_label ) . '"';
		}

		echo '<div class="option-group' . esc_attr( $button_class ) . '"'. wp_kses_normalize_entities(  $button_attr ) . '>';
		echo '<div class="option-group-content">';
	}
}

if ( ! function_exists( 'staggs_single_product_options_totals_wrapper_close' ) ) {
	/**
	 * Product configurator options cart button.
	 *
	 * @return void
	 */
	function staggs_single_product_options_totals_wrapper_close() {
		global $button_sticky, $totals_display;

		echo '</div>';

		/**
		 * Hook: staggs_after_single_product_options_totals
		 *
		 * @hooked -
		 */
		do_action( 'staggs_after_single_product_options_totals' );

		echo '</div>';

		if ( $button_sticky && 'always' === $totals_display ) {
			$spacer_class = 'option-group-spacer';

			$usps = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_step_usps' );
			if ( is_array( $usps ) && count( $usps ) > 0 ) {
				$spacer_class .= ' option-group-spacer--tall';
			}

			echo '<div class="' . esc_attr( $spacer_class ) . '"></div>';
		}
	}
}

if ( ! function_exists( 'staggs_output_bottom_bar_totals' ) ) {
	/**
	 * Product configurator bottom bar totals
	 *
	 * @return void
	 */
	function staggs_output_bottom_bar_totals() {
		echo '<div class="bottom-bar-totals-wrapper">';

		do_action( 'staggs_before_bottom_bar_totals' );

		$theme_id = staggs_get_theme_id();
		$totals_display = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_totals_display' ) );
		$button_attr = '';
		if ( 'end' === $totals_display ) {
			$button_attr = ' data-show-step="final"';

			$button_position = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_totals_button_position' ) );
			if ( 'in_step_controls' === $button_position ) {
				$button_attr .= ' data-button-position="step-controls"';
			}
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_totals_display_required' ) ) {
			$button_attr .= ' data-step-valid="required"';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_display_qty_input' )
			&& staggs_get_post_meta( $theme_id, 'sgg_configurator_display_qty_total' ) ) {
			$button_attr .= ' data-qty-totals="1"';
		}

		echo '<div class="bottom-bar-totals"' . wp_kses_normalize_entities(  $button_attr ) . '>';

		do_action( 'staggs_bottom_bar_totals' );

		echo '</div>';

		do_action( 'staggs_after_bottom_bar_totals' );

		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_add_to_cart_wrapper' ) ) {
	/**
	 * Product configurator add to cart button wrapper.
	 *
	 * @return void
	 */
	function staggs_output_add_to_cart_wrapper() {
		if ( ! product_is_configurable( get_the_ID() ) ) {
			return;
		}

		$class = '';
		if ( ! staggs_get_post_meta( staggs_get_theme_id(), 'sgg_theme_disable_cart_styles' ) ) {
			$class .= ' staggs';
		}

		echo '<div class="staggs-cart-form-button' . esc_attr( $class ) . '">';
	}
}

if ( ! function_exists( 'staggs_output_add_to_cart_wrapper_close' ) ) {
	/**
	 * Product configurator add to cart button wrapper close.
	 *
	 * @return void
	 */
	function staggs_output_add_to_cart_wrapper_close() {
		if ( ! product_is_configurable( get_the_ID() ) ) {
			return;
		}
		
		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_product_totals_list' ) ) {
	/**
	 * Product configurator options totals list details.
	 *
	 * @return void
	 */
	function staggs_output_product_totals_list() {
		global $sgg_is_shortcode;

		$theme_id = staggs_get_theme_id();
		$display_price = staggs_get_post_meta( $theme_id, 'sgg_configurator_display_pricing' );
		if ( staggs_get_theme_option( 'sgg_product_show_price_logged_in_users' ) && ! is_user_logged_in() ) {
			$display_price = 'hide';
		}

		if ( 'hide' === $display_price ) {
			return;
		}

		$template = staggs_get_configurator_page_template( $theme_id );
		$view = staggs_get_configurator_view_layout( $theme_id );
		if ( 'above' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_display' ) || 'none' === $template || 'default' === $template ) {

			$collapse_icon = '';
			$totals_class  = '';
			if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_price_summary_collapse' ) ) {
				$collapse_icon = '<a href="#0" id="totals-list-collapse">' . staggs_get_icon( 'sgg_separator_collapse_icon', 'collapse' ) . '</a>';
				$totals_class  = ' collapsible collapsed';
			}

			do_action( 'staggs_before_total_list' );
			echo '<div class="totals-list' . esc_attr( $totals_class ) . '">';
			do_action( 'staggs_before_total_row' );

			if ( 'summary' === staggs_get_post_meta( $theme_id, 'sgg_configurator_price_display_template' ) 
				&& in_array( $view, array( 'classic', 'floating', 'full' ) ) ) {

				$product_label  = staggs_get_post_meta( $theme_id, 'sgg_configurator_totals_product_label' ) ?: 'Product total:';
				$options_label  = staggs_get_post_meta( $theme_id, 'sgg_configurator_totals_options_label' ) ?: 'Options total:';
				$combined_label = staggs_get_post_meta( $theme_id, 'sgg_configurator_totals_combined_label' ) ?: 'Grand total:';

				if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_price_summary_collapse' ) ) {
					echo '<div class="totals-row-details">';
				}

				echo '<div class="totals-row">' . esc_attr( apply_filters( 'staggs_total_product_label', sgg__( $product_label ) ) ) . '<span id="productprice"></span></div>';	
				echo '<div class="totals-row">' . esc_attr( apply_filters( 'staggs_total_configuration_label', sgg__( $options_label ) ) ) . '<span id="optionsprice"></span></div>';

				do_action( 'staggs_total_row_details' );
				
				if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_price_summary_collapse' ) ) {
					echo '</div>';
				}

				echo '<div class="totals-row totals-row-last">' . esc_attr( apply_filters( 'staggs_total_combined_label', sgg__( $combined_label ) ) ) . '<span class="totals-row-price"><span id="totalprice"></span>' . wp_kses_normalize_entities(  $collapse_icon ) . '</span></div>';
			} else {
				$totals_label = staggs_get_post_meta( $theme_id, 'sgg_configurator_totals_label' ) ?: __( 'Total:', 'staggs' );
				echo '<div class="totals-row">' . esc_attr( apply_filters( 'staggs_total_row_label', sgg__( $totals_label ) ) ) . '<span id="totalprice"></span></div>';

				if ( ( 'none' === $template || 'default' === $template ) && ! $sgg_is_shortcode ) {
					if ( staggs_get_theme_option( 'sgg_product_totals_show_tax' ) ) {
						$tax_label = staggs_get_theme_option( 'sgg_product_totals_alt_tax_label' ) ?: 'Total tax:';
						echo '<div class="totals-row">' . esc_attr( apply_filters( 'staggs_total_product_tax_label', sgg__( $tax_label ) ) ) . '<span id="totaltaxprice"></span></div>';	
					}
	
					$theme_id = staggs_get_theme_id();
					if ( staggs_get_theme_option( 'sgg_product_totals_show_weight' ) && ! staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_weight' ) ) {
						$weight_label = staggs_get_theme_option( 'sgg_product_totals_weight_label' ) ?: 'Product weight:';
						echo '<div class="totals-row">' . esc_attr( apply_filters( 'staggs_total_product_weight_label', sgg__( $weight_label ) ) ) . '<span id="product_weight"></span></div>';	
					}
				}
			}

			do_action( 'staggs_after_total_row' );
			echo '</div>';
			do_action( 'staggs_after_total_list' );
		}
	}
}

if ( ! function_exists( 'staggs_output_product_main_button' ) ) {
	/**
	 * Product configurator options cart button.
	 *
	 * @return void
	 */
	function staggs_output_product_main_button() {
		global $product;

		if ( staggs_get_theme_option( 'sgg_product_totals_show_tax' ) ) {
			$tax_label = staggs_get_theme_option( 'sgg_product_totals_alt_tax_label' ) ?: 'Total tax:';
			echo '<div class="totals-row">' . esc_attr( apply_filters( 'staggs_total_product_tax_label', __( $tax_label, 'staggs' ) ) ) . '<span id="totaltaxprice"></span></div>';	
		}

		$theme_id = staggs_get_theme_id();
		if ( staggs_get_theme_option( 'sgg_product_totals_show_weight' ) && ! staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_weight' ) ) {
			$weight_label = staggs_get_theme_option( 'sgg_product_totals_weight_label' ) ?: 'Product weight:';
			echo '<div class="totals-row">' . esc_attr( apply_filters( 'staggs_total_product_weight_label', __( $weight_label, 'staggs' ) ) ) . '<span id="product_weight"></span></div>';	
		}

		if ( 'none' === staggs_get_post_meta( $theme_id, 'sgg_configurator_button_type' )) {
			return; // No action.
		}

		if ( ! is_user_logged_in() && function_exists('wc_get_page_id') && staggs_get_theme_option('sgg_product_redirect_visitors_to_login_page') ) {
			if ( 'new_page' === staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_location' ) ) {
				$button_text = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_page_button' ) );
				if ( '' === $button_text ) {
					$button_text = __( 'Finish configuration', 'staggs' );
				}
			} else {
				$button_text = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_step_add_to_cart_text' ) );
				if ( '' === $button_text ) {
					if ( 'cart' === staggs_get_post_meta( $theme_id, 'sgg_configurator_button_type' ) ) {
						$button_text = $product->single_add_to_cart_text();
					} else {
						$button_text = __( 'Request invoice', 'staggs' );
					}
				}
			}

			$display_price = staggs_get_post_meta( $theme_id, 'sgg_configurator_display_pricing' );
			if ( staggs_get_theme_option( 'sgg_product_show_price_logged_in_users' ) && ! is_user_logged_in() ) {
				$display_price = 'hide';
			}

			$class = '';
			if ( 'within' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_display' )
				&& 'hide' !== $display_price ) {
				$class = ' inline_price';
			}

			$button_wrapper_class = '';
			if ( ! staggs_get_post_meta( $theme_id, 'sgg_theme_disable_cart_styles' ) ) {
				$button_wrapper_class .= ' staggs';
			}

			$formula = '';
			if ( 'custom' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_calculation' ) ) {
				$formula = staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_formula' );
			}

			$attributes = '';
			if ( $formula ) {
				$attributes .= ' data-formula="' . esc_attr( $formula ) . '"';
			}
			?>
			<div class="button-wrapper"<?php echo wp_kses_normalize_entities(  $attributes ); ?>>
				<?php
				if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_hide_totals_button' ) ) : 
					$login_page_url = apply_filters( 'sgg_visitor_login_page_url', get_permalink( wc_get_page_id('myaccount') ) );
					?>
					<div class="staggs-cart-form-button<?php echo esc_attr( $button_wrapper_class ); ?>">
						<a href="<?php echo esc_url( $login_page_url ); ?>" class="button request-invoice">
							<?php
							echo esc_attr( $button_text );
							if ( 'within' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_display' )
								&& 'hide' !== $display_price ) {
								echo '<span id="totalprice"></span>';
							}
							?>
						</a>
					</div>
					<?php
				endif;
				?>
			</div>
			<?php
			return;
		}
		
		if ( 'new_page' === staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_location' ) ) :
			// Display view summary as main button
			$summary_button_text = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_page_button' ) );
			if ( '' === $summary_button_text ) {
				$summary_button_text = __( 'Finish configuration', 'staggs' );
			}

			$display_price = staggs_get_post_meta( $theme_id, 'sgg_configurator_display_pricing' );
			if ( staggs_get_theme_option( 'sgg_product_show_price_logged_in_users' ) && ! is_user_logged_in() ) {
				$display_price = 'hide';
			}

			$class = '';
			if ( 'within' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_display' )
				&& 'hide' !== $display_price ) {
				$class = ' inline_price';
			}

			$button_wrapper_class = '';
			if ( ! staggs_get_post_meta( $theme_id, 'sgg_theme_disable_cart_styles' ) ) {
				$button_wrapper_class .= ' staggs';
			}

			$formula = '';
			if ( 'custom' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_calculation' ) ) {
				$formula = staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_formula' );
			}

			$attributes = '';
			if ( $formula ) {
				$attributes .= ' data-formula="' . esc_attr( $formula ) . '"';
			}
			?>
			<div class="button-wrapper"<?php echo wp_kses_normalize_entities(  $attributes ); ?>>
				<?php
				if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_hide_totals_button' ) ) : 
					?>
					<div class="staggs-cart-form-button<?php echo esc_attr( $button_wrapper_class ); ?>">
						<a href="#" data-product="<?php echo esc_attr( get_the_ID() ); ?>" class="button request-invoice open-summary-page<?php echo esc_attr( $class ); ?>" id="staggs-show-summary">
							<?php
							echo esc_attr( $summary_button_text );
							if ( 'within' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_display' )
								&& 'hide' !== $display_price ) {
								echo '<span id="totalprice"></span>';
							}
							?>
						</a>
					</div>
					<?php
				endif;

				do_action( 'staggs_after_single_add_to_cart' );
				?>
			</div>
			<?php
		else:
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && 'cart' === staggs_get_post_meta( $theme_id, 'sgg_configurator_button_type' ) ) {

				if ( ! $product ) {
					$product = wc_get_product( get_the_ID() );
				}
				if ( ! $product ) {
					return;
				}

				// WooCommerce product with cart feature.
				if ( $product->is_purchasable() && $product->is_in_stock() ) {
					$formula = '';
					if ( 'custom' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_calculation' ) ) {
						$formula = staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_formula' );
					}

					$attributes = '';
					if ( $formula ) {
						$attributes .= ' data-formula="' . esc_attr( $formula ) . '"';
					}
					?>
					<div class="button-wrapper"<?php echo wp_kses_normalize_entities(  $attributes ); ?>>
						<?php
						// Display add to cart button
						do_action( 'staggs_single_add_to_cart' );
						do_action( 'staggs_after_single_add_to_cart' );
						?>
					</div>
					<?php
				}
			} else {
				// Display request invoice as main button for cart button is disabled.
				$invoice_text = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_step_add_to_cart_text' ) );
				if ( '' === $invoice_text ) {
					$invoice_text = __( 'Request invoice', 'staggs' );
				}

				$total_price_display = staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_display' );
				$display_price = staggs_get_post_meta( $theme_id, 'sgg_configurator_display_pricing' );
				if ( staggs_get_theme_option( 'sgg_product_show_price_logged_in_users' ) && ! is_user_logged_in() ) {
					$display_price = 'hide';
				}

				$class = '';
				if ( 'within' === $total_price_display && 'hide' !== $display_price ) {
					$class = ' inline_price';
				}

				$button_wrapper_class = '';
				if ( ! staggs_get_post_meta( $theme_id, 'sgg_theme_disable_cart_styles' ) ) {
					$button_wrapper_class .= ' staggs';
				}

				$page_id = staggs_get_post_meta( $theme_id, 'sgg_configurator_form_page' );
				$button_type = staggs_get_post_meta( $theme_id, 'sgg_configurator_button_type' );
				$formula = '';
				if ( 'custom' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_calculation' ) ) {
					$formula = staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_formula' );
				}

				$attributes = '';
				if ( $formula ) {
					$attributes .= ' data-formula="' . esc_attr( $formula ) . '"';
				}
				?>
				<div class="button-wrapper"<?php echo wp_kses_normalize_entities(  $attributes ); ?>>
					<?php
					if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_hide_totals_button' ) ) : 
						if ( 'invoice' === $button_type ) :
							$data_atts = '';
							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_total_generate_pdf' ) ) {
								$data_atts .= ' data-include_pdf="' . get_the_ID() . '"';
							}
							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_total_generate_image' ) ) {
								$data_atts .= ' data-include_image="' . get_the_ID() . '"';
							}
							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_total_generate_url' ) ) {
								$data_atts .= ' data-include_url="' . get_the_ID() . '"';
							}
							?>
							<form action="<?php echo esc_url( get_permalink( $page_id ) ); ?>" id="invoice" method="get" class="staggs-main-action product-action action-request-invoice">
								<input type="hidden" id="append" name="product_name" value="<?php echo esc_attr( get_the_title() ); ?>">
								<div class="staggs-cart-form-button<?php echo esc_attr( $button_wrapper_class ); ?>">
									<button type="submit" class="button request-invoice<?php echo esc_attr( $class ); ?>" id="request-invoice"<?php echo wp_kses_normalize_entities(  $data_atts ); ?>>
										<?php echo esc_attr( $invoice_text ); ?>
										<?php
										if ( 'within' === $total_price_display && 'hide' !== $display_price ) {
											echo '<span id="totalprice"></span>';
										}
										?>
									</button>
								</div>
							</form>
							<?php
						elseif ( 'pdf' === $button_type ) :
							?>
							<form action="<?php echo esc_url( get_permalink( get_the_ID() ) . '?generate_pdf=' . get_the_ID() ); ?>" data-product="<?php echo esc_attr( get_the_ID() ); ?>" id="staggs_pdf_invoice" method="get" class="staggs-main-action product-action action-request-invoice staggs-main-action-pdf-download">
								<input type="hidden" name="generate_pdf" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
								<?php
								if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_pdf_collect_email' ) ) :
									?>
									<div class="text-input staggs-pdf-email-input">
										<label for="generate_pdf_email" class="input-field-wrapper">
											<span class="input-heading">
												<p class="input-title">
													<?php
													$input_label = esc_attr__( 'Your email address', 'staggs' );
													if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_pdf_email_label' ) ) {
														$input_label = staggs_get_post_meta( $theme_id, 'sgg_configurator_pdf_email_label' );
													}
													echo esc_attr( $input_label ). ' <span class="required-indicator">*</span>';
													?>
												</p>
											</span>
											<span class="input-field">
												<input type="email" name="generate_pdf_email" value="" />
											</span>
										</label>
									</div>
									<?php
								endif;
								?>
								<div class="staggs-cart-form-button<?php echo esc_attr( $button_wrapper_class ); ?>">
									<button type="submit" class="button request-invoice<?php echo esc_attr( $class ); ?>" id="invoice_pdf_download">
										<?php echo esc_attr( $invoice_text ); ?>
										<?php
										if ( 'within' === $total_price_display && 'hide' !== $display_price ) {
											echo '<span id="totalprice"></span>';
										}
										?>
									</button>
								</div>
							</form>
							<?php
						elseif ( 'email' === $button_type ) :
							$mail_link = 'mailto:';
							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_button_email_recipient' ) ) {
								$mail_link .= staggs_get_post_meta( $theme_id, 'sgg_configurator_button_email_recipient' );
							} else {
								$mail_link .= get_option( 'admin_email' );
							}

							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_button_email_subject' ) ) {
								$mail_link .= '?subject=' . str_replace( ' ', '%20', staggs_get_post_meta( $theme_id, 'sgg_configurator_button_email_subject' ) ) . '&body=';
							} else {
								$mail_link .= '?body=';
							}

							$data_atts = '';
							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_button_email_show_product_title' ) ) {
								$data_atts .= ' data-title="' . get_the_title( get_the_ID() ) . '"';
							}
							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_total_generate_pdf' ) ) {
								$data_atts .= ' data-include_pdf="' . get_the_ID() . '"';
							}
							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_total_generate_image' ) ) {
								$data_atts .= ' data-include_image="' . get_the_ID() . '"';
							}
							if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_total_generate_url' ) ) {
								$data_atts .= ' data-include_url="' . get_the_ID() . '"';
							}
							?>
							<div class="staggs-cart-form-button<?php echo esc_attr( $button_wrapper_class ); ?>">
								<a href="<?php echo esc_url( $mail_link ); ?>" class="button request-invoice send-email<?php echo esc_attr( $class ); ?>" id="staggs-send-email" target="_blank"<?php echo wp_kses_normalize_entities(  $data_atts ); ?>>
									<?php
									echo esc_attr( $invoice_text );
									if ( 'within' === $total_price_display && 'hide' !== $display_price ) {
										echo '<span id="totalprice"></span>';
									}
									?>
								</a>
							</div>
							<?php
						endif;
					endif;

					do_action( 'staggs_after_single_add_to_cart' );
					?>
				</div>
				<?php
			}
		endif;
	}
}

if ( ! function_exists( 'staggs_output_options_usps' ) ) {
	/**
	 * Product configurator options usps.
	 *
	 * @return void
	 */
	function staggs_output_options_usps() {
		$usps = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_step_usps' );
		if ( is_array( $usps ) && count( $usps ) > 0 ) {
			?>
			<div class="staggs-product-usps product-view-usps">
				<?php
				foreach ( $usps as $usp ) :
					?>
					<div class="usps-item">
						<?php echo wp_get_attachment_image( esc_attr( $usp['sgg_usp_icon'] ), 'thumbnail' ); ?>
						<p><?php echo esc_attr( $usp['sgg_usp_title'] ); ?></p>
					</div>
					<?php
				endforeach;
				?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'staggs_output_options_credit' ) ) {
	/**
	 * Product configurator options credit message.
	 *
	 * @return void
	 */
	function staggs_output_options_credit() {
		if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) :
			?>
			<p class="credit">Powered by <a href="https://staggs.app/" target="_blank" rel="noopener noreferrer">Staggs</a></p>
			<?php
		endif;
	}
}

if ( ! function_exists( 'staggs_output_topbar_product_title' ) ) {
	/**
	 * Product configurator popup topbar title.
	 *
	 * @return void
	 */
	function staggs_output_topbar_product_title() {
		$arrow_back = staggs_get_icon( 'sgg_popup_back_icon', 'popup-back' );
		echo '<a href="#" id="close-popup">' . wp_kses_normalize_entities(  $arrow_back ) . '<span>' . esc_attr( get_the_title() ) . '</span></a>';
	}
}

if ( ! function_exists( 'staggs_output_popup_bottom_bar' ) ) {
	/**
	 * Product configurator popup bottom bar.
	 *
	 * @return void
	 */
	function staggs_output_popup_bottom_bar() {
		echo '<div class="bottom-bar-left">';

		do_action( 'staggs_output_popup_bottom_bar_left' );

		echo '</div>';
		echo '<div class="bottom-bar-right">';

		do_action( 'staggs_output_popup_bottom_bar_right' );

		echo '</div>';
	}
}

if ( ! function_exists( 'staggs_output_product_sticky_bar' ) ) {
	/**
	 * Product configurator options save buttons.
	 *
	 * @return void
	 */
	function staggs_output_product_sticky_bar() {
		$usps = array();

		$theme_id = staggs_get_theme_id();
		if ( 'classic' !== staggs_get_configurator_view_layout( $theme_id ) ) {
			return;
		}

		if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_sticky_cart_bar' ) ) {
			return;
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_sticky_cart_bar_usps' ) ) {
			$usps = staggs_get_post_meta( $theme_id, 'sgg_step_usps' );
		}

		$wrapper_class = '';
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' ) ) {
			$wrapper_class .= ' border-' . staggs_get_post_meta( $theme_id, 'sgg_configurator_borders' );
		}

		$cart_text = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_step_add_to_cart_text' ) );
		if ( '' === $cart_text ) {
			if ( 'cart' === staggs_get_post_meta( $theme_id, 'sgg_configurator_button_type' ) ) {
				$cart_text = __( 'Add to cart', 'staggs' );
			} else {
				$cart_text = __( 'Request invoice', 'staggs' );
			}
		}
		?>
		<div class="staggs-configurator-bottom-bar staggs-configurator-sticky-bar<?php echo esc_attr( $wrapper_class ); ?>">
			<div class="staggs-container">
				<div class="staggs-configurator-sticky-bar-inner">
					<div class="staggs-sticky-bar-header">
						<p class="staggs-sticky-bar-title"><?php the_title(); ?></p>
						<?php
						if ( count( $usps ) > 0 ) {
							staggs_output_options_usps();
						}
						?>
					</div>
					<div class="staggs-sticky-bar-totals">
						<div class="sticky-bar-totalprice">
							<?php do_action( 'staggs_before_sticky_bar_total_price' ); ?>
							<span id="totalprice"></span>
						</div>
						<div class="staggs-cart-form-button staggs">
							<button id="staggs-sticky-bar" class="button single_add_to_cart_button">
								<?php echo esc_attr( $cart_text ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}