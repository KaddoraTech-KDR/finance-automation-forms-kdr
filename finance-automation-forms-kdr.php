<?php
/**
 * Plugin Name: Finance Automation Forms kdr
 * Description: Finance forms (Invoice, Payment, GST Registration, Billing, Quotation, Expense) with GST/tax calculations and automation (PDF, emails, exports, integrations).
 * Version:     1.0.0
 * Author:      kaddora Tech
 * Text Domain: finance-automation-forms-kdr
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NOTE:
 * User requested main file name: finance-automaton-forms-kdr.php
 * Folder name can be: finance-automation-forms-kdr/
 */

define( 'FAFKDR_VERSION', '1.0.0' );
define( 'FAFKDR_PLUGIN_FILE', __FILE__ );
define( 'FAFKDR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'FAFKDR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FAFKDR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Core includes (keep this light for v1).
 * Expected paths (from the lean structure we agreed):
 * - includes/core/class-plugin-kdr.php
 * - includes/core/class-db-kdr.php
 */
require_once FAFKDR_PLUGIN_DIR . 'includes/core/class-db-kdr.php';
require_once FAFKDR_PLUGIN_DIR . 'includes/core/class-plugin-kdr.php';

/**
 * Activation / Deactivation hooks
 */
function fafkdr_activate_plugin() {
	if ( class_exists( 'FAFKDR_DB_kdr' ) ) {
		FAFKDR_DB_kdr::activate_kdr();
	}
}

function fafkdr_deactivate_plugin() {
	if ( class_exists( 'FAFKDR_DB_kdr' ) ) {
		FAFKDR_DB_kdr::deactivate_kdr();
	}
}

register_activation_hook( __FILE__, 'fafkdr_activate_plugin' );
register_deactivation_hook( __FILE__, 'fafkdr_deactivate_plugin' );

/**
 * Bootstrap the plugin.
 */
function fafkdr_run_plugin() {
	if ( ! class_exists( 'FAFKDR_Plugin_kdr' ) ) {
		return;
	}

	$plugin_kdr = FAFKDR_Plugin_kdr::instance_kdr();
	$plugin_kdr->init_kdr();
}

add_action( 'plugins_loaded', 'fafkdr_run_plugin' );

// Next steps (v2)
// Roadmap
// Improve submissions UI + actions
// Add finance calculator + GST split
// Add automation: PDF + Email + Export
// Integrations: CRM / Accounting