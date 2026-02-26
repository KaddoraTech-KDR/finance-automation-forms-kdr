<?php
/**
 * Forms controller (lean v1): renders templates + handles POST submit + uploads (kdr)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FAFKDR_Forms_kdr' ) ) :

final class FAFKDR_Forms_kdr {

	private static function allowed_types_kdr(): array {
		return array( 'invoice', 'payment', 'gst', 'billing', 'quotation', 'expense' );
	}

	public static function render_form_kdr( string $type ): string {
		$type = sanitize_key( $type );

		if ( ! in_array( $type, self::allowed_types_kdr(), true ) ) {
			$type = 'invoice';
		}

		$notice_html = self::handle_submit_if_any_kdr( $type );

		$template = FAFKDR_PLUGIN_DIR . 'includes/forms/templates/' . $type . '-form-kdr.php';
		if ( ! file_exists( $template ) ) {
			return $notice_html . '<div class="fafkdr-error">Form template not found: ' . esc_html( $type ) . '</div>';
		}

		ob_start();

		echo $notice_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Provide common variables to templates.
		$fafkdr_form_type  = $type;
		$fafkdr_nonce      = wp_create_nonce( 'fafkdr_submit_' . $type );
		$fafkdr_action_url = esc_url( remove_query_arg( array( 'fafkdr_ok', 'fafkdr_err' ) ) );

		include $template;

		return (string) ob_get_clean();
	}

	private static function handle_submit_if_any_kdr( string $type ): string {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return '';
		}

		$post_type = isset( $_POST['fafkdr_form_type'] ) ? sanitize_key( (string) wp_unslash( $_POST['fafkdr_form_type'] ) ) : '';
		if ( $post_type !== $type ) {
			return '';
		}

		$nonce = isset( $_POST['fafkdr_nonce'] ) ? (string) wp_unslash( $_POST['fafkdr_nonce'] ) : '';
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'fafkdr_submit_' . $type ) ) {
			return self::notice_kdr( 'error', __( 'Security check failed. Please refresh and try again.', 'finance-automation-forms-kdr' ) );
		}

		// Sanitize POST payload
		$payload = self::sanitize_payload_kdr( $_POST );

		// Handle uploads for certain forms
		$upload_result = self::handle_uploads_for_form_kdr( $type );
		if ( is_wp_error( $upload_result ) ) {
			return self::notice_kdr( 'error', $upload_result->get_error_message() );
		}
		if ( ! empty( $upload_result ) ) {
			$payload['uploads'] = $upload_result;
		}

		// Basic email extraction
		$customer_email = '';
		if ( isset( $payload['customer_email'] ) ) {
			$customer_email = sanitize_email( (string) $payload['customer_email'] );
		} elseif ( isset( $payload['email'] ) ) {
			$customer_email = sanitize_email( (string) $payload['email'] );
		}

		$res = FAFKDR_DB_kdr::insert_submission_kdr(
			$payload,
			array(
				'form_type'      => $type,
				'customer_email' => $customer_email,
				'status'         => 'submitted',
				'meta'           => array(
					'page_url' => ( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ),
					'ip'       => self::get_ip_kdr(),
				),
			)
		);

		if ( is_wp_error( $res ) ) {
			return self::notice_kdr( 'error', __( 'Failed to save submission. Please try again.', 'finance-automation-forms-kdr' ) );
		}

		return self::notice_kdr( 'success', __( 'Submitted successfully!', 'finance-automation-forms-kdr' ) );
	}

	/**
	 * Upload handler by form type.
	 * Returns array('field'=>['name','url','path','type','size']) or empty array.
	 */
	private static function handle_uploads_for_form_kdr( string $type ): array|WP_Error {
		if ( empty( $_FILES ) || ! is_array( $_FILES ) ) {
			return array();
		}

		$type = sanitize_key( $type );

		$allowed = array(
			'gst'     => array( 'pan_card', 'address_proof', 'photo', 'bank_proof' ),
			'expense' => array( 'receipt' ),
		);

		if ( ! isset( $allowed[ $type ] ) ) {
			return array();
		}

		$out = array();

		foreach ( $allowed[ $type ] as $field ) {
			if ( empty( $_FILES[ $field ] ) || ! isset( $_FILES[ $field ]['name'] ) ) {
				continue;
			}

			$file = $_FILES[ $field ];

			// Skip if user didn't choose file
			if ( empty( $file['name'] ) ) {
				continue;
			}

			// Basic validation
			$check = self::validate_upload_kdr( $file );
			if ( is_wp_error( $check ) ) {
				return $check;
			}

			$saved = self::save_upload_to_wp_kdr( $field, $file );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}

			$out[ $field ] = $saved;
		}

		return $out;
	}

	private static function validate_upload_kdr( array $file ) {
		// If PHP upload error exists
		if ( ! empty( $file['error'] ) ) {
			return new WP_Error( 'fafkdr_upload_error', __( 'Upload failed. Please try again.', 'finance-automation-forms-kdr' ) );
		}

		// File size limit (v1): 5MB
		$max = 5 * 1024 * 1024;
		if ( ! empty( $file['size'] ) && (int) $file['size'] > $max ) {
			return new WP_Error( 'fafkdr_upload_too_large', __( 'File too large. Max 5MB allowed.', 'finance-automation-forms-kdr' ) );
		}

		// Mime validation (allow pdf/jpg/jpeg/png)
		$allowed_mimes = array(
			'pdf'  => 'application/pdf',
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
		);

		$filename = isset( $file['name'] ) ? (string) $file['name'] : '';
		$ext      = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		if ( ! isset( $allowed_mimes[ $ext ] ) ) {
			return new WP_Error( 'fafkdr_upload_invalid_type', __( 'Invalid file type. Only PDF/JPG/PNG allowed.', 'finance-automation-forms-kdr' ) );
		}

		return true;
	}

	private static function save_upload_to_wp_kdr( string $field, array $file ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		// Put into uploads/fafkdr/
		add_filter( 'upload_dir', array( __CLASS__, 'filter_upload_dir_kdr' ) );

		$overrides = array(
			'test_form' => false,
		);

		$movefile = wp_handle_upload( $file, $overrides );

		remove_filter( 'upload_dir', array( __CLASS__, 'filter_upload_dir_kdr' ) );

		if ( ! is_array( $movefile ) || empty( $movefile['file'] ) || empty( $movefile['url'] ) ) {
			return new WP_Error( 'fafkdr_upload_move_failed', __( 'Could not save uploaded file.', 'finance-automation-forms-kdr' ) );
		}

		return array(
			'field' => sanitize_key( $field ),
			'name'  => isset( $file['name'] ) ? sanitize_file_name( (string) $file['name'] ) : '',
			'url'   => esc_url_raw( (string) $movefile['url'] ),
			'path'  => (string) $movefile['file'],
			'type'  => isset( $movefile['type'] ) ? sanitize_text_field( (string) $movefile['type'] ) : '',
			'size'  => isset( $file['size'] ) ? (int) $file['size'] : 0,
		);
	}

	/**
	 * Upload subfolder: wp-content/uploads/fafkdr/YYYY/MM
	 */
	public static function filter_upload_dir_kdr( array $dirs ): array {
		$subdir = '/fafkdr' . $dirs['subdir'];

		$dirs['path']   = $dirs['basedir'] . $subdir;
		$dirs['url']    = $dirs['baseurl'] . $subdir;
		$dirs['subdir'] = $subdir;

		return $dirs;
	}

	private static function sanitize_payload_kdr( array $raw_post ): array {
		$raw = wp_unslash( $raw_post );

		unset( $raw['_wp_http_referer'], $raw['fafkdr_nonce'] );

		$out = array();

		foreach ( $raw as $key => $value ) {
			$key = sanitize_key( (string) $key );
			if ( '' === $key ) {
				continue;
			}
			$out[ $key ] = self::sanitize_value_kdr( $value );
		}

		return $out;
	}

	private static function sanitize_value_kdr( $value ) {
		if ( is_array( $value ) ) {
			$clean = array();
			foreach ( $value as $k => $v ) {
				$ck = is_numeric( $k ) ? (int) $k : sanitize_key( (string) $k );
				$clean[ $ck ] = self::sanitize_value_kdr( $v );
			}
			return $clean;
		}

		$val = trim( (string) $value );
		return sanitize_text_field( $val );
	}

	private static function get_ip_kdr(): string {
		// Optional, simple.
		$ip = '';
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$parts = explode( ',', (string) $_SERVER['HTTP_X_FORWARDED_FOR'] );
			$ip    = trim( (string) $parts[0] );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = (string) $_SERVER['REMOTE_ADDR'];
		}
		return sanitize_text_field( $ip );
	}

	private static function notice_kdr( string $type, string $message ): string {
		$type_class = ( 'success' === $type ) ? 'fafkdr-notice fafkdr-notice--success' : 'fafkdr-notice fafkdr-notice--error';
		return '<div class="' . esc_attr( $type_class ) . '" role="alert">' . esc_html( $message ) . '</div>';
	}
}

endif;