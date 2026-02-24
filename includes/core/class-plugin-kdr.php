<?php
/**
 * Core plugin orchestrator for Finance Automation Forms kdr (lean v1)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FAFKDR_Plugin_kdr' ) ) :

final class FAFKDR_Plugin_kdr {

	private static $instance_kdr = null;

	public static function instance_kdr(): FAFKDR_Plugin_kdr {
		if ( null === self::$instance_kdr ) {
			self::$instance_kdr = new self();
		}
		return self::$instance_kdr;
	}

	private function __construct() {}

	/**
	 * Init plugin (called from main file on plugins_loaded).
	 *
	 * IMPORTANT: Do NOT hook includes/boot back onto plugins_loaded,
	 * because init_kdr() itself is called during plugins_loaded.
	 */
	public function init_kdr(): void {

		// Load translations.
		add_action( 'init', array( $this, 'load_textdomain_kdr' ) );

		// Ensure DB schema is up to date even after updates.
		$this->maybe_migrate_kdr();

		// Load required classes now (not later).
		$this->includes_kdr();

		// Boot admin/frontend now.
		$this->boot_components_kdr();
	}

	public function load_textdomain_kdr(): void {
		load_plugin_textdomain(
			'finance-automation-forms-kdr',
			false,
			dirname( FAFKDR_PLUGIN_BASENAME ) . '/languages'
		);
	}

	public function maybe_migrate_kdr(): void {
		if ( class_exists( 'FAFKDR_DB_kdr' ) ) {
			FAFKDR_DB_kdr::maybe_migrate_kdr();
		}
	}

	/**
	 * Include required files immediately.
	 */
	public function includes_kdr(): void {
		$base = trailingslashit( FAFKDR_PLUGIN_DIR );

		// Core components (required for menu + shortcode).
		require_once $base . 'includes/core/class-admin-kdr.php';
		require_once $base . 'includes/core/class-frontend-kdr.php';

		// Forms controller (required for rendering/handling forms).
		$forms_controller = $base . 'includes/forms/class-forms-kdr.php';
		if ( file_exists( $forms_controller ) ) {
			require_once $forms_controller;
		}
	}

	/**
	 * Boot components.
	 */
	public function boot_components_kdr(): void {
		if ( is_admin() && class_exists( 'FAFKDR_Admin_kdr' ) ) {
			FAFKDR_Admin_kdr::instance_kdr()->init_kdr();
		}

		if ( class_exists( 'FAFKDR_Frontend_kdr' ) ) {
			FAFKDR_Frontend_kdr::instance_kdr()->init_kdr();
		}
	}
}

endif;