<?php
/**
 * Core plugin orchestrator for Finance Automation Forms kdr (lean v1)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FAFKDR_Plugin_kdr' ) ) :

final class FAFKDR_Plugin_kdr {

	/**
	 * Singleton instance.
	 *
	 * @var FAFKDR_Plugin_kdr|null
	 */
	private static $instance_kdr = null;

	/**
	 * Get instance.
	 */
	public static function instance_kdr(): FAFKDR_Plugin_kdr {
		if ( null === self::$instance_kdr ) {
			self::$instance_kdr = new self();
		}
		return self::$instance_kdr;
	}

	/**
	 * Prevent direct construction.
	 */
	private function __construct() {}

	/**
	 * Init hooks.
	 */
	public function init_kdr(): void {
		// Load translations.
		add_action( 'init', array( $this, 'load_textdomain_kdr' ) );

		// Ensure DB schema is up to date even after updates.
		add_action( 'plugins_loaded', array( $this, 'maybe_migrate_kdr' ), 5 );

		// Load components.
		add_action( 'plugins_loaded', array( $this, 'includes_kdr' ), 6 );
		add_action( 'plugins_loaded', array( $this, 'boot_components_kdr' ), 7 );
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
	 * Load required class files (lean v1).
	 *
	 * We keep includes minimal. Later we can expand modules without breaking.
	 */
	public function includes_kdr(): void {
		$base = trailingslashit( FAFKDR_PLUGIN_DIR );

		// Core utilities (optional, create later as needed).
		$core_files = array(
			'includes/core/class-admin-kdr.php',
			'includes/core/class-frontend-kdr.php',
		);

		foreach ( $core_files as $rel ) {
			$path = $base . $rel;
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}

		// Forms controller (create later; safe to ignore if not present yet).
		$forms_controller = $base . 'includes/forms/class-forms-kdr.php';
		if ( file_exists( $forms_controller ) ) {
			require_once $forms_controller;
		}

		// Finance + automation can be required later when you add them.
	}

	/**
	 * Boot admin/frontend components if available.
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