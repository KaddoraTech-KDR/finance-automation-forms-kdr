<?php
/**
 * Admin View: Payment Submission Details (kdr)
 *
 * Vars provided by loader:
 * - $row (array)
 * - $payload (array)
 * - $customer_email (string)
 * - $uploads (array) (usually empty for payment)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$get = function ( string $key, string $default = '-' ) use ( $payload ): string {
	if ( ! isset( $payload[ $key ] ) || is_array( $payload[ $key ] ) ) {
		return $default;
	}
	$v = trim( (string) $payload[ $key ] );
	return $v !== '' ? $v : $default;
};

// Common fields from your payment form
$name     = $get( 'name' );
$email    = $get( 'email', '' );
$phone    = $get( 'phone' );
$purpose  = $get( 'purpose' );
$amount_s = $get( 'amount', '0' );
$currency = $get( 'currency', '-' );
$method   = $get( 'payment_method', $get( 'method', '-' ) );
$notes    = $get( 'notes', '' );

// Checkbox flags (your form uses checkboxes)
$generate_link = ! empty( $payload['generate_link'] );
$send_receipt  = ! empty( $payload['send_receipt'] );
$save_customer = ! empty( $payload['save_customer'] );
$enable_tax    = ! empty( $payload['enable_tax'] );

// Amount formatting
$amount = (float) $amount_s;
$amount_display = number_format( $amount, 2 );
$display_email  = $email !== '' ? $email : ( $customer_email ?: '-' );
?>

<!-- ===================== PAYMENT DETAILS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Payment Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Amount', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value" style="font-weight:800;">
				<?php echo esc_html( $currency . ' ' . $amount_display ); ?>
			</div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Payment Method', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $method ); ?></div>
		</div>

		<div style="grid-column:1 / -1;">
			<div class="fafkdr-label"><?php esc_html_e( 'Purpose / Description', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $purpose ); ?></div>
		</div>

		<?php if ( $notes !== '' ) : ?>
			<div style="grid-column:1 / -1;">
				<div class="fafkdr-label"><?php esc_html_e( 'Notes', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value" style="font-weight:500; color:#334155;">
					<?php echo esc_html( $notes ); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- ===================== CUSTOMER ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Customer', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Name', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $name ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Email', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $display_email ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Phone', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $phone ); ?></div>
		</div>
	</div>
</div>

<!-- ===================== OPTIONS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Options', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; max-width:700px;">
		<div><strong><?php esc_html_e( 'Generate payment link:', 'finance-automation-forms-kdr' ); ?></strong> <?php echo esc_html( $generate_link ? 'Yes' : 'No' ); ?></div>
		<div><strong><?php esc_html_e( 'Email receipt:', 'finance-automation-forms-kdr' ); ?></strong> <?php echo esc_html( $send_receipt ? 'Yes' : 'No' ); ?></div>
		<div><strong><?php esc_html_e( 'Save customer:', 'finance-automation-forms-kdr' ); ?></strong> <?php echo esc_html( $save_customer ? 'Yes' : 'No' ); ?></div>
		<div><strong><?php esc_html_e( 'Enable tax:', 'finance-automation-forms-kdr' ); ?></strong> <?php echo esc_html( $enable_tax ? 'Yes' : 'No' ); ?></div>
	</div>
</div>

<?php if ( ! empty( $uploads ) ) : ?>
	<div class="fafkdr-card" style="padding:16px;">
		<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Files', 'finance-automation-forms-kdr' ); ?></h2>
		<ul style="margin:0; padding-left:18px;">
			<?php foreach ( $uploads as $key => $info ) : ?>
				<?php
				$url  = isset( $info['url'] ) ? esc_url( (string) $info['url'] ) : '';
				$name = isset( $info['name'] ) ? sanitize_text_field( (string) $info['name'] ) : $key;
				?>
				<li>
					<strong><?php echo esc_html( (string) $key ); ?>:</strong>
					<?php if ( $url ) : ?>
						<a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $name ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $name ); ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>