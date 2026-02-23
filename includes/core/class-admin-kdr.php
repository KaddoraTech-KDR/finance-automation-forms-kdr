<?php
/**
 * Admin UI (lean v1) for Finance Automation Forms kdr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FAFKDR_Admin_kdr' ) ) :

final class FAFKDR_Admin_kdr {

	private static $instance_kdr = null;

	public static function instance_kdr(): FAFKDR_Admin_kdr {
		if ( null === self::$instance_kdr ) {
			self::$instance_kdr = new self();
		}
		return self::$instance_kdr;
	}

	private function __construct() {}

	public function init_kdr(): void {
		add_action( 'admin_menu', array( $this, 'register_menu_kdr' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets_kdr' ) );
	}

	public function register_menu_kdr(): void {
		$cap = 'manage_options';

		add_menu_page(
			__( 'Finance Automation Forms kdr', 'finance-automation-forms-kdr' ),
			__( 'Finance Forms kdr', 'finance-automation-forms-kdr' ),
			$cap,
			'fafkdr',
			array( $this, 'render_dashboard_kdr' ),
			'dashicons-media-spreadsheet',
			56
		);

		add_submenu_page(
			'fafkdr',
			__( 'Dashboard', 'finance-automation-forms-kdr' ),
			__( 'Dashboard', 'finance-automation-forms-kdr' ),
			$cap,
			'fafkdr',
			array( $this, 'render_dashboard_kdr' )
		);

		add_submenu_page(
			'fafkdr',
			__( 'Submissions', 'finance-automation-forms-kdr' ),
			__( 'Submissions', 'finance-automation-forms-kdr' ),
			$cap,
			'fafkdr-submissions',
			array( $this, 'render_submissions_kdr' )
		);

		add_submenu_page(
			'fafkdr',
			__( 'Settings', 'finance-automation-forms-kdr' ),
			__( 'Settings', 'finance-automation-forms-kdr' ),
			$cap,
			'fafkdr-settings',
			array( $this, 'render_settings_kdr' )
		);
	}

	public function enqueue_assets_kdr( string $hook ): void {
		// Only load on our plugin pages.
		if ( false === strpos( $hook, 'fafkdr' ) ) {
			return;
		}

		wp_enqueue_style(
			'fafkdr-admin',
			FAFKDR_PLUGIN_URL . 'assets/admin.css',
			array(),
			FAFKDR_VERSION
		);

		wp_enqueue_script(
			'fafkdr-admin',
			FAFKDR_PLUGIN_URL . 'assets/admin.js',
			array( 'jquery' ),
			FAFKDR_VERSION,
			true
		);
	}

	public function render_dashboard_kdr(): void {
		?>
		<div class="wrap fafkdr-admin">
			<h1><?php esc_html_e( 'Finance Automation Forms kdr', 'finance-automation-forms-kdr' ); ?></h1>

			<div class="fafkdr-cards">
				<div class="fafkdr-card">
					<h2><?php esc_html_e( 'Quick Start', 'finance-automation-forms-kdr' ); ?></h2>
					<p><?php esc_html_e( 'Use shortcodes to embed forms:', 'finance-automation-forms-kdr' ); ?></p>
					<ul>
						<li><code>[fafkdr_form type="invoice"]</code></li>
						<li><code>[fafkdr_form type="payment"]</code></li>
						<li><code>[fafkdr_form type="quotation"]</code></li>
						<li><code>[fafkdr_form type="billing"]</code></li>
						<li><code>[fafkdr_form type="expense"]</code></li>
						<li><code>[fafkdr_form type="gst"]</code></li>
					</ul>
				</div>

				<div class="fafkdr-card">
					<h2><?php esc_html_e( 'Next steps (v1)', 'finance-automation-forms-kdr' ); ?></h2>
					<ol>
						<li><?php esc_html_e( 'Finalize form templates', 'finance-automation-forms-kdr' ); ?></li>
						<li><?php esc_html_e( 'Add finance calculator + GST split', 'finance-automation-forms-kdr' ); ?></li>
						<li><?php esc_html_e( 'Add automation: PDF + Email + Export', 'finance-automation-forms-kdr' ); ?></li>
					</ol>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_submissions_kdr(): void {
		global $wpdb;

		$table = FAFKDR_DB_kdr::table_submissions_kdr();
		$rows  = $wpdb->get_results( "SELECT id, form_type, customer_email, status, created_at FROM {$table} ORDER BY id DESC LIMIT 50", ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		?>
		<div class="wrap fafkdr-admin">
			<h1><?php esc_html_e( 'Submissions', 'finance-automation-forms-kdr' ); ?></h1>

			<table class="widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'finance-automation-forms-kdr' ); ?></th>
						<th><?php esc_html_e( 'Form Type', 'finance-automation-forms-kdr' ); ?></th>
						<th><?php esc_html_e( 'Customer Email', 'finance-automation-forms-kdr' ); ?></th>
						<th><?php esc_html_e( 'Status', 'finance-automation-forms-kdr' ); ?></th>
						<th><?php esc_html_e( 'Created', 'finance-automation-forms-kdr' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( empty( $rows ) ) : ?>
					<tr><td colspan="5"><?php esc_html_e( 'No submissions found yet.', 'finance-automation-forms-kdr' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $rows as $r ) : ?>
						<tr>
							<td><?php echo esc_html( (string) $r['id'] ); ?></td>
							<td><?php echo esc_html( (string) $r['form_type'] ); ?></td>
							<td><?php echo esc_html( (string) ( $r['customer_email'] ?? '' ) ); ?></td>
							<td><?php echo esc_html( (string) $r['status'] ); ?></td>
							<td><?php echo esc_html( (string) $r['created_at'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>

			<p style="margin-top:12px; color:#666;">
				<?php esc_html_e( 'v1 note: detailed view/edit will be added later.', 'finance-automation-forms-kdr' ); ?>
			</p>
		</div>
		<?php
	}

	public function render_settings_kdr(): void {
		?>
		<div class="wrap fafkdr-admin">
			<h1><?php esc_html_e( 'Settings', 'finance-automation-forms-kdr' ); ?></h1>

			<div class="fafkdr-card">
				<p><?php esc_html_e( 'v1 settings will be added here (business details, GST defaults, email templates, payment gateway keys).', 'finance-automation-forms-kdr' ); ?></p>
			</div>
		</div>
		<?php
	}
}

endif;