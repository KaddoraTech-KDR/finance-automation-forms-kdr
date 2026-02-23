<?php
/**
 * Frontend layer (lean v1) for Finance Automation Forms kdr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FAFKDR_Frontend_kdr' ) ) :

final class FAFKDR_Frontend_kdr {

	private static $instance_kdr = null;

	public static function instance_kdr(): FAFKDR_Frontend_kdr {
		if ( null === self::$instance_kdr ) {
			self::$instance_kdr = new self();
		}
		return self::$instance_kdr;
	}

	private function __construct() {}

	public function init_kdr(): void {
		add_shortcode( 'fafkdr_form', array( $this, 'shortcode_form_kdr' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets_kdr' ) );
	}

	public function register_assets_kdr(): void {
		wp_register_style(
			'fafkdr-front',
			FAFKDR_PLUGIN_URL . 'assets/front.css',
			array(),
			FAFKDR_VERSION
		);

		wp_register_script(
			'fafkdr-front',
			FAFKDR_PLUGIN_URL . 'assets/front.js',
			array( 'jquery' ),
			FAFKDR_VERSION,
			true
		);

		wp_localize_script(
			'fafkdr-front',
			'fafkdrVars',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	public function shortcode_form_kdr( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'type' => 'invoice',
			),
			$atts,
			'fafkdr_form'
		);

		$type = sanitize_key( (string) $atts['type'] );
		if ( empty( $type ) ) {
			$type = 'invoice';
		}

		// Enqueue modern UI assets.
		wp_enqueue_style( 'fafkdr-front' );
		wp_enqueue_script( 'fafkdr-front' );

		// Render via Forms controller if present.
		if ( class_exists( 'FAFKDR_Forms_kdr' ) ) {
			return FAFKDR_Forms_kdr::render_form_kdr( $type );
		}

		return '<div class="fafkdr-error">Forms module not loaded.</div>';
	}
}

endif;