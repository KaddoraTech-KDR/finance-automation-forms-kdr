<?php
/**
 * Admin View: Invoice Submission Details (kdr)
 *
 * Vars provided:
 * $row, $payload, $customer_email, $uploads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Safe getter
 */
$get = function ( string $key, string $default = '-' ) use ( $payload ): string {
	if ( ! isset( $payload[ $key ] ) || is_array( $payload[ $key ] ) ) {
		return $default;
	}
	$v = trim( (string) $payload[ $key ] );
	return $v !== '' ? $v : $default;
};

$items = ( isset( $payload['items'] ) && is_array( $payload['items'] ) ) ? $payload['items'] : [];

/**
 * Totals calculation (same logic as frontend)
 */
$subtotal = 0;
$tax_total = 0;

foreach ( $items as $it ) {
	$qty  = isset( $it['qty'] ) ? (float) $it['qty'] : 0;
	$rate = isset( $it['rate'] ) ? (float) $it['rate'] : 0;
	$gst  = isset( $it['gst_rate'] ) ? (float) $it['gst_rate'] : ( isset( $it['tax'] ) ? (float) $it['tax'] : 0 );

	$line = $qty * $rate;
	$line_tax = ( $line * $gst ) / 100;

	$subtotal += $line;
	$tax_total += $line_tax;
}

$shipping = isset( $payload['shipping_charge'] ) ? (float) $payload['shipping_charge'] : 0;
$discount = isset( $payload['global_discount_value'] ) ? (float) $payload['global_discount_value'] : 0;

$grand_total = $subtotal + $tax_total + $shipping - $discount;

/**
 * Customer
 */
$customer_name = $get( 'customer_name' );
$email_payload = $get( 'email', '' );
$display_email = $email_payload ?: ( $customer_email ?: '-' );
$phone         = $get( 'phone' );
$gstin         = $get( 'gstin' );
$currency      = $get( 'currency' );
$invoice_date  = $get( 'invoice_date' );
$due_date      = $get( 'due_date' );
?>

<!-- ===================== INVOICE DETAILS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Invoice Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Invoice Date', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $invoice_date ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Due Date', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $due_date ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Currency', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $currency ); ?></div>
		</div>
	</div>
</div>

<!-- ===================== CUSTOMER ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Customer', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Name', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $customer_name ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Email', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $display_email ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Phone', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $phone ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'GSTIN', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $gstin ); ?></div>
		</div>
	</div>
</div>

<!-- ===================== ITEMS ===================== -->

<?php if ( ! empty( $items ) ) : ?>
<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Items', 'finance-automation-forms-kdr' ); ?></h2>

	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Item', 'finance-automation-forms-kdr' ); ?></th>
				<th><?php esc_html_e( 'Qty', 'finance-automation-forms-kdr' ); ?></th>
				<th><?php esc_html_e( 'Rate', 'finance-automation-forms-kdr' ); ?></th>
				<th><?php esc_html_e( 'GST%', 'finance-automation-forms-kdr' ); ?></th>
				<th><?php esc_html_e( 'Line Total', 'finance-automation-forms-kdr' ); ?></th>
			</tr>
		</thead>

		<tbody>
		<?php foreach ( $items as $it ) : 
			$name = $it['name'] ?? '-';
			$qty  = $it['qty'] ?? '-';
			$rate = $it['rate'] ?? '-';
			$gst  = $it['gst_rate'] ?? ( $it['tax'] ?? '-' );

			$line_total = isset( $it['line_total_display'] )
				? $it['line_total_display']
				: number_format( (float)$qty * (float)$rate, 2 );
		?>
			<tr>
				<td><?php echo esc_html( $name ); ?></td>
				<td><?php echo esc_html( $qty ); ?></td>
				<td><?php echo esc_html( $rate ); ?></td>
				<td><?php echo esc_html( $gst ); ?></td>
				<td><?php echo esc_html( $line_total ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php endif; ?>

<!-- ===================== TOTALS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Totals', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px;">
		<div class="fafkdr-stat">
			<div class="fafkdr-label"><?php esc_html_e( 'Subtotal', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( number_format( $subtotal, 2 ) ); ?></div>
		</div>

		<div class="fafkdr-stat">
			<div class="fafkdr-label"><?php esc_html_e( 'GST', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( number_format( $tax_total, 2 ) ); ?></div>
		</div>

		<div class="fafkdr-stat">
			<div class="fafkdr-label"><?php esc_html_e( 'Shipping', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( number_format( $shipping, 2 ) ); ?></div>
		</div>

		<div class="fafkdr-stat">
			<div class="fafkdr-label"><?php esc_html_e( 'Grand Total', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value" style="font-weight:800;">
				<?php echo esc_html( number_format( $grand_total, 2 ) ); ?>
			</div>
		</div>
	</div>
</div>

<!-- ===================== OPTIONS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Options', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:flex; gap:20px;">
		<div>
			<strong><?php esc_html_e( 'Generate PDF:', 'finance-automation-forms-kdr' ); ?></strong>
			<?php echo ! empty( $payload['generate_pdf'] ) ? 'Yes' : 'No'; ?>
		</div>

		<div>
			<strong><?php esc_html_e( 'Send Email:', 'finance-automation-forms-kdr' ); ?></strong>
			<?php echo ! empty( $payload['send_email'] ) ? 'Yes' : 'No'; ?>
		</div>
	</div>
</div>