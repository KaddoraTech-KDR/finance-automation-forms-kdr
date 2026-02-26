<?php
/**
 * DB layer (v1 lean) for Finance Automation Forms kdr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FAFKDR_DB_kdr' ) ) :

final class FAFKDR_DB_kdr {

	/**
	 * Schema version. Increment when changing tables.
	 */
	const SCHEMA_VERSION = '1.0.1';

	/**
	 * Option key to store schema version.
	 */
	const OPTION_SCHEMA_VERSION = 'fafkdr_schema_version';

	/**
	 * Table names.
	 */
	public static function table_forms_kdr(): string {
		global $wpdb;
		return $wpdb->prefix . 'fafkdr_forms';
	}

	public static function table_submissions_kdr(): string {
		global $wpdb;
		return $wpdb->prefix . 'fafkdr_submissions';
	}

	/**
	 * Runs on plugin activation.
	 */
	public static function activate_kdr(): void {
		self::maybe_migrate_kdr();
	}

	/**
	 * Runs on plugin deactivation (usually no destructive actions).
	 */
	public static function deactivate_kdr(): void {
		// Intentionally empty for v1.
	}

	/**
	 * Ensure tables exist and schema is up-to-date.
	 */
	public static function maybe_migrate_kdr(): void {
		$installed = get_option( self::OPTION_SCHEMA_VERSION );

		// Always attempt dbDelta if version mismatch.
		if ( $installed !== self::SCHEMA_VERSION ) {
			self::create_tables_kdr();
			update_option( self::OPTION_SCHEMA_VERSION, self::SCHEMA_VERSION );
		}
	}

	/**
	 * Create/upgrade tables using dbDelta.
	 */
	private static function create_tables_kdr(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$forms_table = self::table_forms_kdr();
		$subs_table  = self::table_submissions_kdr();

		$sql_forms = "CREATE TABLE {$forms_table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(191) NOT NULL,
			type VARCHAR(50) NOT NULL,
			settings LONGTEXT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'active',
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY type (type),
			KEY status (status)
		) {$charset_collate};";

		// IMPORTANT: payload is stored in column `data` (used by admin details page)
		$sql_subs = "CREATE TABLE {$subs_table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			form_type VARCHAR(50) NOT NULL,
			form_id BIGINT(20) UNSIGNED NULL,
			customer_email VARCHAR(191) NULL,
			status VARCHAR(30) NOT NULL DEFAULT 'submitted',
			data LONGTEXT NOT NULL,
			calc LONGTEXT NULL,
			meta LONGTEXT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY form_type (form_type),
			KEY form_id (form_id),
			KEY status (status),
			KEY customer_email (customer_email)
		) {$charset_collate};";

		dbDelta( $sql_forms );
		dbDelta( $sql_subs );
	}

	/**
	 * Insert a submission (v1).
	 *
	 * @param array $payload Any sanitized form payload.
	 * @param array $args Optional: form_type, form_id, customer_email, status, calc, meta.
	 * @return int|WP_Error Inserted ID or error.
	 */
	public static function insert_submission_kdr( array $payload, array $args = array() ) {
		global $wpdb;

		$table = self::table_submissions_kdr();

		$form_type      = isset( $args['form_type'] ) ? sanitize_key( (string) $args['form_type'] ) : '';
		$form_id        = isset( $args['form_id'] ) ? absint( $args['form_id'] ) : 0;
		$customer_email = isset( $args['customer_email'] ) ? sanitize_email( (string) $args['customer_email'] ) : '';
		$status         = isset( $args['status'] ) ? sanitize_key( (string) $args['status'] ) : 'submitted';

		$data_json = wp_json_encode( $payload );
		if ( false === $data_json ) {
			return new WP_Error( 'fafkdr_json_encode_failed', 'Failed to encode submission payload.' );
		}

		$calc_json = null;
		if ( isset( $args['calc'] ) ) {
			$tmp = wp_json_encode( $args['calc'] );
			if ( false !== $tmp ) {
				$calc_json = $tmp;
			}
		}

		$meta_json = null;
		if ( isset( $args['meta'] ) ) {
			$tmp = wp_json_encode( $args['meta'] );
			if ( false !== $tmp ) {
				$meta_json = $tmp;
			}
		}

		$now = current_time( 'mysql' );

		$data = array(
			'form_type'      => $form_type,
			'form_id'        => ( $form_id > 0 ) ? $form_id : null,
			'customer_email' => ( '' !== $customer_email ) ? $customer_email : null,
			'status'         => $status,
			'data'           => $data_json,
			'calc'           => $calc_json,
			'meta'           => $meta_json,
			'created_at'     => $now,
			'updated_at'     => $now,
		);

		$formats = array(
			'%s', // form_type
			'%d', // form_id (NULL is ok)
			'%s', // customer_email (NULL is ok)
			'%s', // status
			'%s', // data
			'%s', // calc (NULL is ok)
			'%s', // meta (NULL is ok)
			'%s', // created_at
			'%s', // updated_at
		);

		$inserted = $wpdb->insert( $table, $data, $formats );

		if ( false === $inserted ) {
			return new WP_Error( 'fafkdr_db_insert_failed', 'Failed to insert submission.' );
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Fetch a submission row by ID.
	 */
	public static function get_submission_kdr( int $id ) {
		global $wpdb;

		$table = self::table_submissions_kdr();
		$id    = absint( $id );

		if ( $id <= 0 ) {
			return null;
		}

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ),
			ARRAY_A
		);
	}

	/**
	 * List submissions (for admin list/dashboard).
	 */
	public static function list_submissions_kdr( int $limit = 100 ): array {
		global $wpdb;

		$table = self::table_submissions_kdr();
		$limit = max( 1, min( 500, absint( $limit ) ) );

		return $wpdb->get_results(
			$wpdb->prepare( "SELECT id, form_type, customer_email, status, created_at FROM {$table} ORDER BY id DESC LIMIT %d", $limit ),
			ARRAY_A
		);
	}

	/**
	 * Update a submission status.
	 */
	public static function update_submission_status_kdr( int $id, string $status ): bool {
		global $wpdb;

		$table  = self::table_submissions_kdr();
		$id     = absint( $id );
		$status = sanitize_key( $status );

		if ( $id <= 0 || '' === $status ) {
			return false;
		}

		$now = current_time( 'mysql' );

		$updated = $wpdb->update(
			$table,
			array(
				'status'     => $status,
				'updated_at' => $now,
			),
			array( 'id' => $id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		return ( false !== $updated );
	}

	/**
	 * JSON decode helpers (admin pages should use these).
	 */
	public static function decode_data_kdr( array $row ): array {
		$json = isset( $row['data'] ) ? (string) $row['data'] : '';
		$decoded = ( '' !== $json ) ? json_decode( $json, true ) : null;
		return is_array( $decoded ) ? $decoded : array();
	}

	public static function decode_calc_kdr( array $row ): array {
		$json = isset( $row['calc'] ) ? (string) $row['calc'] : '';
		$decoded = ( '' !== $json ) ? json_decode( $json, true ) : null;
		return is_array( $decoded ) ? $decoded : array();
	}

	public static function decode_meta_kdr( array $row ): array {
		$json = isset( $row['meta'] ) ? (string) $row['meta'] : '';
		$decoded = ( '' !== $json ) ? json_decode( $json, true ) : null;
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Counts (for dashboard).
	 */
	public static function count_submissions_kdr(): int {
		global $wpdb;
		$table = self::table_submissions_kdr();
		$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		return max( 0, $count );
	}

	public static function count_by_form_type_kdr(): array {
		global $wpdb;
		$table = self::table_submissions_kdr();

		$rows = $wpdb->get_results(
			"SELECT form_type, COUNT(*) as cnt FROM {$table} GROUP BY form_type ORDER BY cnt DESC",
			ARRAY_A
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		$out = array();
		foreach ( $rows as $r ) {
			$out[ (string) $r['form_type'] ] = (int) $r['cnt'];
		}
		return $out;
	}
}

endif;