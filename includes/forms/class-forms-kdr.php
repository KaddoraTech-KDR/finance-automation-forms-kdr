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
			return $notice_html . '<div class="fafkdr-error">Form template not found: ' . esc_html( $type ) . '</div>';
		}

		ob_start();

		echo $notice_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Provide common variables to templates.
		$fafkdr_form_type = $type;
		$fafkdr_nonce     = wp_create_nonce( 'fafkdr_submit_' . $type );

		// The action is the current URL.
		$fafkdr_action_url = esc_url( remove_query_arg( array( 'fafkdr_ok', 'fafkdr_err' ) ) );

		include $template;

		return (string) ob_get_clean();
	}

	/**
	 * Submission handler (v1):
	 * - verify nonce
	 * - sanitize basic payload
	 * - store raw payload JSON in DB
	 * - show success/error notice
	 */
	private static function handle_submit_if_any_kdr( string $type ): string {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return '';
		}

		// Only handle our own forms.
		$post_type = isset( $_POST['fafkdr_form_type'] ) ? sanitize_key( (string) wp_unslash( $_POST['fafkdr_form_type'] ) ) : '';
		if ( $post_type !== $type ) {
			return '';
		}

		$nonce = isset( $_POST['fafkdr_nonce'] ) ? (string) wp_unslash( $_POST['fafkdr_nonce'] ) : '';
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'fafkdr_submit_' . $type ) ) {
			return self::notice_kdr( 'error', __( 'Security check failed. Please refresh and try again.', 'finance-automation-forms-kdr' ) );
		}

		// Minimal sanitization (v1). Later: per-form validators.
		$payload = self::sanitize_payload_kdr( $_POST );

		// Basic customer email extraction for indexing.
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
				),
			)
		);

		if ( is_wp_error( $res ) ) {
			return self::notice_kdr( 'error', __( 'Failed to save submission. Please try again.', 'finance-automation-forms-kdr' ) );
		}

		return self::notice_kdr( 'success', __( 'Submitted successfully!', 'finance-automation-forms-kdr' ) );
	}

	/**
	 * Remove internal fields and sanitize scalars recursively.
	 */
	private static function sanitize_payload_kdr( array $raw_post ): array {
		$raw = wp_unslash( $raw_post );

		// Remove WP/internal fields we don't want stored.
		unset( $raw['_wp_http_referer'], $raw['fafkdr_nonce'] );

		// Keep form type as explicit.
		$out = array();

		foreach ( $raw as $key => $value ) {
			$key = sanitize_key( (string) $key );

			// Skip empty keys.
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
				// Preserve numeric indexes for items arrays.
				$ck = is_numeric( $k ) ? (int) $k : sanitize_key( (string) $k );
				$clean[ $ck ] = self::sanitize_value_kdr( $v );
			}
			return $clean;
		}

		// Scalar
		$val = (string) $value;
		$val = trim( $val );

		// Don't over-sanitize; keep content readable but safe.
		return sanitize_text_field( $val );
	}

	private static function notice_kdr( string $type, string $message ): string {
		$type_class = ( 'success' === $type ) ? 'fafkdr-notice fafkdr-notice--success' : 'fafkdr-notice fafkdr-notice--error';

		return '<div class="' . esc_attr( $type_class ) . '" role="alert">' . esc_html( $message ) . '</div>';
	}
}

endif;