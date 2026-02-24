<?php
/**
 * Forms controller (lean v1): renders templates + handles POST submit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FAFKDR_Forms_kdr' ) ) :

final class FAFKDR_Forms_kdr {

	/**
	 * Allowed form types (v1).
	 */
	private static function allowed_types_kdr(): array {
		return array( 'invoice', 'payment', 'gst', 'billing', 'quotation', 'expense' );
	}

	/**
	 * Render a form by type (called by shortcode).
	 */
	public static function render_form_kdr( string $type ): string {
		$type = sanitize_key( $type );

		if ( ! in_array( $type, self::allowed_types_kdr(), true ) ) {
			$type = 'invoice';
		}

		// Handle submission (POST-back) on same page.
		$notice_html = self::handle_submit_if_any_kdr( $type );

		$template = FAFKDR_PLUGIN_DIR . 'includes/forms/templates/' . $type . '-form-kdr.php';
		if ( ! file_exists( $template ) ) {
			return $notice_html . '<div class="fafkdr-notice fafkdr-notice--error">Form template not found: ' . esc_html( $type ) . '</div>';
		}

		ob_start();

		echo $notice_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Provide common variables to templates.
		$fafkdr_form_type = $type;
		$fafkdr_nonce     = wp_create_nonce( 'fafkdr_submit_' . $type );

		// Current URL (without our flags).
		$fafkdr_action_url = esc_url( remove_query_arg( array( 'fafkdr_ok', 'fafkdr_err' ) ) );

		include $template;

		return (string) ob_get_clean();
	}

	/**
	 * Submission handler (v1):
	 * - verify nonce
	 * - validate per form (basic)
	 * - sanitize payload
	 * - (optional) handle uploads for GST/Expense
	 * - store JSON in DB
	 * - redirect (PRG) to avoid resubmission on refresh
	 */
	private static function handle_submit_if_any_kdr( string $type ): string {
		if ( empty( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			// Also show notice if redirected.
			return self::notice_from_query_kdr();
		}

		// Only handle our own forms.
		$post_type = isset( $_POST['fafkdr_form_type'] ) ? sanitize_key( (string) wp_unslash( $_POST['fafkdr_form_type'] ) ) : '';
		if ( $post_type !== $type ) {
			return self::notice_from_query_kdr();
		}

		$nonce = isset( $_POST['fafkdr_nonce'] ) ? (string) wp_unslash( $_POST['fafkdr_nonce'] ) : '';
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'fafkdr_submit_' . $type ) ) {
			self::redirect_with_notice_kdr( 'err', __( 'Security check failed. Please refresh and try again.', 'finance-automation-forms-kdr' ) );
			return '';
		}

		// Validate required fields (basic v1).
		$errors = self::validate_required_fields_kdr( $type, $_POST );
		if ( ! empty( $errors ) ) {
			self::redirect_with_notice_kdr( 'err', implode( ' ', $errors ) );
			return '';
		}

		// Sanitize payload.
		$payload = self::sanitize_payload_kdr( $type, $_POST );

		// Handle uploads for GST/Expense (optional fields in UI; if present, we store).
		$uploads = self::handle_uploads_if_any_kdr( $type, $_FILES );
		if ( is_wp_error( $uploads ) ) {
			self::redirect_with_notice_kdr( 'err', $uploads->get_error_message() );
			return '';
		}
		if ( ! empty( $uploads ) ) {
			$payload['uploads'] = $uploads;
		}

		// Basic customer email extraction for indexing.
		$customer_email = self::extract_customer_email_kdr( $payload );

		$res = FAFKDR_DB_kdr::insert_submission_kdr(
			$payload,
			array(
				'form_type'      => $type,
				'customer_email' => $customer_email,
				'status'         => 'submitted',
				'meta'           => array(
					'page_url' => ( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ),
				),
			)
		);

		if ( is_wp_error( $res ) ) {
			self::redirect_with_notice_kdr( 'err', __( 'Failed to save submission. Please try again.', 'finance-automation-forms-kdr' ) );
			return '';
		}

		self::redirect_with_notice_kdr( 'ok', __( 'Submitted successfully!', 'finance-automation-forms-kdr' ) );
		return '';
	}

	/**
	 * Minimal required fields per form (v1).
	 * Keeps DB clean without building full validators yet.
	 */
	private static function validate_required_fields_kdr( string $type, array $raw_post ): array {
		$raw = wp_unslash( $raw_post );
		$errors = array();

		$required_map = array(
			'payment'   => array( 'customer_name', 'customer_email', 'payment_purpose', 'payment_amount', 'currency', 'payment_method' ),
			'billing'   => array( 'bill_date', 'currency', 'customer_name' ),
			'invoice'   => array( 'invoice_date', 'currency', 'customer_name', 'customer_email', 'customer_state' ),
			'quotation' => array( 'quote_date', 'valid_until', 'currency', 'customer_name', 'customer_email' ),
			'expense'   => array( 'expense_date', 'expense_category', 'expense_amount', 'currency' ),
			'gst'       => array(
				'applicant_full_name',
				'applicant_email',
				'applicant_phone',
				'applicant_pan',
				'business_legal_name',
				'business_constitution',
				'business_nature',
				'address_country',
				'address_state',
				'address_district',
				'address_city',
				'address_postal_code',
				'address_full',
				'bank_account_holder',
				'bank_name',
				'bank_account_number',
				'bank_ifsc',
				'confirm_details',
			),
		);

		$required = $required_map[ $type ] ?? array();
		foreach ( $required as $key ) {
			$key = (string) $key;
			$val = $raw[ $key ] ?? '';
			if ( is_array( $val ) ) {
				if ( empty( $val ) ) {
					$errors[] = sprintf( __( 'Missing field: %s.', 'finance-automation-forms-kdr' ), $key );
				}
			} else {
				$val = trim( (string) $val );
				if ( '' === $val ) {
					$errors[] = sprintf( __( 'Missing field: %s.', 'finance-automation-forms-kdr' ), $key );
				}
			}
		}

		// Light format checks (v1).
		if ( 'gst' === $type ) {
			$pan = isset( $raw['applicant_pan'] ) ? strtoupper( trim( (string) $raw['applicant_pan'] ) ) : '';
			if ( '' !== $pan && ! preg_match( '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan ) ) {
				$errors[] = __( 'Invalid PAN format (example: AAAAA9999A).', 'finance-automation-forms-kdr' );
			}

			$phone = isset( $raw['applicant_phone'] ) ? preg_replace( '/\D+/', '', (string) $raw['applicant_phone'] ) : '';
			if ( '' !== $phone && strlen( $phone ) < 10 ) {
				$errors[] = __( 'Phone number looks too short.', 'finance-automation-forms-kdr' );
			}
		}

		return $errors;
	}

	/**
	 * Sanitize POST payload.
	 * - removes internal fields
	 * - strips display-only fields
	 * - normalizes numbers where possible
	 */
	private static function sanitize_payload_kdr( string $type, array $raw_post ): array {
		$raw = wp_unslash( $raw_post );

		// Remove WP/internal fields we don't want stored.
		unset( $raw['_wp_http_referer'], $raw['fafkdr_nonce'] );

		$out = array();

		foreach ( $raw as $key => $value ) {
			$key = sanitize_key( (string) $key );

			if ( '' === $key ) {
				continue;
			}

			// Drop internal post marker if you prefer to not store it in payload.
			if ( 'fafkdr_form_type' === $key ) {
				continue;
			}

			$out[ $key ] = self::sanitize_value_kdr( $key, $value );
		}

		// Clean items array: remove display-only fields.
		if ( isset( $out['items'] ) && is_array( $out['items'] ) ) {
			foreach ( $out['items'] as $i => $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				unset( $out['items'][ $i ]['line_total_display'] );
			}
		}

		return $out;
	}

	/**
	 * Recursive sanitizer with numeric awareness.
	 */
	private static function sanitize_value_kdr( string $key, $value ) {
		if ( is_array( $value ) ) {
			$clean = array();
			foreach ( $value as $k => $v ) {
				$ck = is_numeric( $k ) ? (int) $k : sanitize_key( (string) $k );
				$clean[ $ck ] = self::sanitize_value_kdr( (string) $ck, $v );
			}
			return $clean;
		}

		$val = trim( (string) $value );

		// Numeric fields: keep as float (or int) for cleaner JSON + future calculator.
		if ( self::is_numeric_field_kdr( $key ) ) {
			// Allow empty numeric field to remain empty.
			if ( '' === $val ) {
				return '';
			}
			// Normalize comma decimals if user typed "1,000.50" -> "1000.50"
			$val = str_replace( ',', '', $val );
			return is_numeric( $val ) ? (float) $val : 0.0;
		}

		if ( self::is_email_field_kdr( $key ) ) {
			return sanitize_email( $val );
		}

		// Default scalar.
		return sanitize_text_field( $val );
	}

	private static function is_email_field_kdr( string $key ): bool {
		return in_array( $key, array( 'customer_email', 'applicant_email', 'email' ), true );
	}

	private static function is_numeric_field_kdr( string $key ): bool {
		// Direct numeric keys.
		$direct = array(
			'payment_amount',
			'expense_amount',
			'tax_amount',
			'shipping_charge',
			'global_discount_value',
		);
		if ( in_array( $key, $direct, true ) ) {
			return true;
		}

		// Items subfields (detected by end of key name).
		// Because array sanitizer passes nested keys like "qty", "rate", "tax", etc.
		$item_fields = array( 'qty', 'rate', 'tax', 'gst_rate', 'discount_value' );
		return in_array( $key, $item_fields, true );
	}

	/**
	 * Extract customer email for indexing (works across forms).
	 */
	private static function extract_customer_email_kdr( array $payload ): string {
		$customer_email = '';

		if ( ! empty( $payload['customer_email'] ) ) {
			$customer_email = sanitize_email( (string) $payload['customer_email'] );
		} elseif ( ! empty( $payload['applicant_email'] ) ) {
			$customer_email = sanitize_email( (string) $payload['applicant_email'] );
		} elseif ( ! empty( $payload['email'] ) ) {
			$customer_email = sanitize_email( (string) $payload['email'] );
		}

		return $customer_email;
	}

	/**
	 * Handle uploads (GST + Expense). Returns array of uploaded file info.
	 * Stores uploaded file URL + name + type.
	 */
	private static function handle_uploads_if_any_kdr( string $type, array $files ) {
		if ( empty( $files ) || ! is_array( $files ) ) {
			return array();
		}

		// Only enable upload handling for these forms (for now).
		if ( ! in_array( $type, array( 'gst', 'expense' ), true ) ) {
			return array();
		}

		$allowed_mimes = array(
			'pdf'  => 'application/pdf',
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
		);

		// Map our template field names.
		$field_whitelist = array();
		if ( 'gst' === $type ) {
			$field_whitelist = array( 'docs_pan_card', 'docs_address_proof', 'docs_photo', 'docs_bank_proof' );
		} elseif ( 'expense' === $type ) {
			$field_whitelist = array( 'receipt' );
		}

		$out = array();

		foreach ( $field_whitelist as $field ) {
			if ( empty( $files[ $field ] ) || ! is_array( $files[ $field ] ) ) {
				continue;
			}

			$file = $files[ $field ];

			// No file uploaded for this field.
			if ( empty( $file['name'] ) || ! empty( $file['error'] ) ) {
				continue;
			}

			// Validate extension/mime using WP.
			$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed_mimes );
			if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
				return new WP_Error( 'fafkdr_bad_file', __( 'Invalid file type. Allowed: PDF, JPG, PNG.', 'finance-automation-forms-kdr' ) );
			}

			// Upload using WordPress handler.
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			$uploaded = wp_handle_upload(
				$file,
				array( 'test_form' => false )
			);

			if ( ! empty( $uploaded['error'] ) ) {
				return new WP_Error( 'fafkdr_upload_failed', sanitize_text_field( (string) $uploaded['error'] ) );
			}

			$out[ $field ] = array(
				'url'  => esc_url_raw( (string) $uploaded['url'] ),
				'file' => sanitize_text_field( (string) $uploaded['file'] ),
				'type' => sanitize_text_field( (string) $uploaded['type'] ),
				'name' => sanitize_text_field( (string) $file['name'] ),
			);
		}

		return $out;
	}

	/**
	 * PRG: Redirect back to same page with notice flags.
	 */
	private static function redirect_with_notice_kdr( string $kind, string $message ): void {
		$kind = ( 'ok' === $kind ) ? 'ok' : 'err';
		$url  = remove_query_arg( array( 'fafkdr_ok', 'fafkdr_err' ) );

		if ( 'ok' === $kind ) {
			$url = add_query_arg( 'fafkdr_ok', rawurlencode( $message ), $url );
		} else {
			$url = add_query_arg( 'fafkdr_err', rawurlencode( $message ), $url );
		}

		wp_safe_redirect( esc_url_raw( $url ) );
		exit;
	}

	private static function notice_from_query_kdr(): string {
		// Display notices after redirect.
		$ok  = isset( $_GET['fafkdr_ok'] ) ? (string) wp_unslash( $_GET['fafkdr_ok'] ) : '';
		$err = isset( $_GET['fafkdr_err'] ) ? (string) wp_unslash( $_GET['fafkdr_err'] ) : '';

		if ( '' !== $ok ) {
			return self::notice_kdr( 'success', sanitize_text_field( $ok ) );
		}
		if ( '' !== $err ) {
			return self::notice_kdr( 'error', sanitize_text_field( $err ) );
		}
		return '';
	}

	private static function notice_kdr( string $type, string $message ): string {
		$type_class = ( 'success' === $type ) ? 'fafkdr-notice fafkdr-notice--success' : 'fafkdr-notice fafkdr-notice--error';

		return '<div class="' . esc_attr( $type_class ) . '" role="alert">' . esc_html( $message ) . '</div>';
	}
}

endif;