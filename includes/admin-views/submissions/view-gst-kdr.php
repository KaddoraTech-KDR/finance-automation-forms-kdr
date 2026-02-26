<?php
/**
 * Admin View: GST Registration Submission Details (kdr)
 *
 * Vars provided by loader:
 * - $row (array)
 * - $payload (array)
 * - $customer_email (string)
 * - $uploads (array) (if payload includes uploads)
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

// Applicant
$full_name = $get( 'full_name' );
$email     = $get( 'email', '' );
$phone     = $get( 'phone' );
$pan       = $get( 'pan' );
$aadhaar   = $get( 'aadhaar', '' );

// Business
$legal_name    = $get( 'legal_name' );
$trade_name    = $get( 'trade_name', '' );
$constitution  = $get( 'constitution' );
$nature        = $get( 'nature' );
$commencement  = $get( 'commencement' );

// Address
$country      = $get( 'country' );
$state        = $get( 'state' );
$district     = $get( 'district' );
$city         = $get( 'city' );
$postal_code  = $get( 'postal_code' );
$full_address = $get( 'full_address' );

// Bank
$account_holder = $get( 'account_holder' );
$bank_name      = $get( 'bank_name' );
$account_number = $get( 'account_number' );
$ifsc           = $get( 'ifsc' );

// Confirmation checkbox
$confirmed = ! empty( $payload['confirm'] ) || ! empty( $payload['i_confirm'] );

// Uploads (future-ready)
$uploads = ( isset( $payload['uploads'] ) && is_array( $payload['uploads'] ) ) ? $payload['uploads'] : ( isset( $uploads ) && is_array( $uploads ) ? $uploads : [] );

// Mask account number (show last 4)
$masked_account = $account_number;
if ( $masked_account !== '-' ) {
	$digits = preg_replace( '/\s+/', '', (string) $masked_account );
	if ( strlen( $digits ) > 4 ) {
		$masked_account = str_repeat( '•', max( 0, strlen( $digits ) - 4 ) ) . substr( $digits, -4 );
	}
}
?>

<!-- ===================== APPLICANT ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Applicant Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Full Name', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $full_name ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Email', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $email !== '' ? $email : ( $customer_email ?: '-' ) ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Phone', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $phone ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'PAN', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value" style="font-weight:800;"><?php echo esc_html( $pan ); ?></div>
		</div>

		<?php if ( $aadhaar !== '' ) : ?>
			<div>
				<div class="fafkdr-label"><?php esc_html_e( 'Aadhaar (Optional)', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value"><?php echo esc_html( $aadhaar ); ?></div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- ===================== BUSINESS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Business Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div style="grid-column:1 / -1;">
			<div class="fafkdr-label"><?php esc_html_e( 'Legal Business Name', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $legal_name ); ?></div>
		</div>

		<?php if ( $trade_name !== '' ) : ?>
			<div style="grid-column:1 / -1;">
				<div class="fafkdr-label"><?php esc_html_e( 'Trade Name', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value"><?php echo esc_html( $trade_name ); ?></div>
			</div>
		<?php endif; ?>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Constitution', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $constitution ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Nature of Business', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $nature ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Commencement Date', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $commencement ); ?></div>
		</div>
	</div>
</div>

<!-- ===================== ADDRESS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Address Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Country', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $country ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'State', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $state ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'District', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $district ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'City', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $city ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Postal Code', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $postal_code ); ?></div>
		</div>

		<div style="grid-column:1 / -1;">
			<div class="fafkdr-label"><?php esc_html_e( 'Full Address', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value" style="font-weight:500; color:#334155;"><?php echo esc_html( $full_address ); ?></div>
		</div>
	</div>
</div>

<!-- ===================== BANK ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Bank Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Account Holder', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $account_holder ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Bank Name', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $bank_name ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Account Number', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value" style="font-weight:800;"><?php echo esc_html( $masked_account ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'IFSC', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value" style="font-weight:800;"><?php echo esc_html( $ifsc ); ?></div>
		</div>
	</div>
</div>

<!-- ===================== DOCUMENTS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Documents', 'finance-automation-forms-kdr' ); ?></h2>

	<?php if ( ! empty( $uploads ) ) : ?>
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
	<?php else : ?>
		<p style="margin:0; color:#64748b;">
			<?php esc_html_e( 'No uploaded documents are stored yet. (We will enable upload handling in class-forms-kdr.php next.)', 'finance-automation-forms-kdr' ); ?>
		</p>
	<?php endif; ?>
</div>

<!-- ===================== CONFIRMATION ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Confirmation', 'finance-automation-forms-kdr' ); ?></h2>

	<div>
		<strong><?php esc_html_e( 'Applicant confirmed details:', 'finance-automation-forms-kdr' ); ?></strong>
		<?php echo esc_html( $confirmed ? __( 'Yes', 'finance-automation-forms-kdr' ) : __( 'No / Not captured', 'finance-automation-forms-kdr' ) ); ?>
	</div>
</div>