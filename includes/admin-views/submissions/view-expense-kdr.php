<?php
/**
 * Admin View: Expense Submission Details (kdr)
 *
 * Vars provided by loader:
 * - $row (array)
 * - $payload (array)
 * - $customer_email (string)  (usually not used for expense)
 * - $uploads (array)          (may contain receipt info if you add uploads later)
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

$expense_date    = $get( 'expense_date' );
$category        = $get( 'category' );
$vendor          = $get( 'vendor', '' );
$amount_s        = $get( 'amount', '0' );
$currency        = $get( 'currency', '-' );
$payment_method  = $get( 'payment_method', '-' );
$tax_included    = ! empty( $payload['tax_included'] );
$tax_amount_s    = $get( 'tax_amount', '0' );
$notes           = $get( 'notes', '' );

// Calculate total (simple)
$amount     = (float) $amount_s;
$tax_amount = (float) $tax_amount_s;

// If tax is included, amount is final; else show "amount + tax" summary.
$final_total = $tax_included ? $amount : ( $amount + $tax_amount );
?>

<!-- ===================== EXPENSE DETAILS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Expense Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Expense Date', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $expense_date ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Category', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $category ); ?></div>
		</div>

		<?php if ( $vendor !== '' ) : ?>
			<div style="grid-column:1 / -1;">
				<div class="fafkdr-label"><?php esc_html_e( 'Vendor / Payee', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value"><?php echo esc_html( $vendor ); ?></div>
			</div>
		<?php endif; ?>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Amount', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value" style="font-weight:800;">
				<?php echo esc_html( $currency . ' ' . number_format( $amount, 2 ) ); ?>
			</div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Payment Method', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $payment_method ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Tax Included?', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $tax_included ? __( 'Yes', 'finance-automation-forms-kdr' ) : __( 'No', 'finance-automation-forms-kdr' ) ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Tax Amount', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $currency . ' ' . number_format( $tax_amount, 2 ) ); ?></div>
		</div>
	</div>
</div>

<!-- ===================== TOTAL SUMMARY ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Total Summary', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; max-width:900px;">
		<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
			<div class="fafkdr-label"><?php esc_html_e( 'Base Amount', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( number_format( $amount, 2 ) ); ?></div>
		</div>

		<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
			<div class="fafkdr-label"><?php esc_html_e( 'Tax', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( number_format( $tax_amount, 2 ) ); ?></div>
		</div>

		<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
			<div class="fafkdr-label"><?php esc_html_e( 'Final Total', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value" style="font-weight:800;"><?php echo esc_html( number_format( $final_total, 2 ) ); ?></div>
		</div>
	</div>

	<p style="margin:10px 0 0; color:#64748b; font-size:13px;">
		<?php
		if ( $tax_included ) {
			esc_html_e( 'Tax is included in the amount.', 'finance-automation-forms-kdr' );
		} else {
			esc_html_e( 'Final total = amount + tax.', 'finance-automation-forms-kdr' );
		}
		?>
	</p>
</div>

<!-- ===================== NOTES ===================== -->

<?php if ( $notes !== '' ) : ?>
	<div class="fafkdr-card" style="padding:16px;">
		<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Notes', 'finance-automation-forms-kdr' ); ?></h2>
		<div class="fafkdr-value" style="font-weight:500; color:#334155;"><?php echo esc_html( $notes ); ?></div>
	</div>
<?php endif; ?>

<!-- ===================== FILES (Receipt) ===================== -->
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