<?php
/**
 * Admin View: Quotation Submission Details (kdr)
 *
 * Vars provided by loader:
 * - $row (array)
 * - $payload (array)
 * - $customer_email (string)
 * - $uploads (array)
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

$items = ( isset( $payload['items'] ) && is_array( $payload['items'] ) ) ? $payload['items'] : array();

/**
 * Totals (simple v1)
 * Quotation form payload may store:
 * - items[] with qty, rate, tax OR gst_rate
 * - optionally subtotal/discount/tax/grand_total (but we compute to be safe)
 */
$subtotal  = 0.0;
$tax_total = 0.0;

foreach ( $items as $it ) {
	$qty  = isset( $it['qty'] ) ? (float) $it['qty'] : 0.0;
	$rate = isset( $it['rate'] ) ? (float) $it['rate'] : 0.0;

	$gst = 0.0;
	if ( isset( $it['gst_rate'] ) ) {
		$gst = (float) $it['gst_rate'];
	} elseif ( isset( $it['tax'] ) ) {
		$gst = (float) $it['tax'];
	}

	$line     = $qty * $rate;
	$line_tax = ( $line * $gst ) / 100;

	$subtotal  += $line;
	$tax_total += $line_tax;
}

$grand_total = $subtotal + $tax_total;

/**
 * Header fields
 */
$quote_date   = $get( 'quote_date' );
$valid_until  = $get( 'valid_until' );
$currency     = $get( 'currency' );

/**
 * Customer
 */
$customer_name = $get( 'customer_name' );
$email_payload = $get( 'email', '' );
$display_email = $email_payload !== '' ? $email_payload : ( $customer_email ?: '-' );
$phone         = $get( 'phone' );
$address       = $get( 'address', '' );

/**
 * Notes & options
 */
$notes        = $get( 'notes', '' );
$terms        = $get( 'terms', '' );
$generate_pdf = ! empty( $payload['generate_pdf'] );
$send_email   = ! empty( $payload['send_email'] );
?>

<!-- ===================== QUOTATION DETAILS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Quotation Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Quote Date', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $quote_date ); ?></div>
		</div>

		<div>
			<div class="fafkdr-label"><?php esc_html_e( 'Valid Until', 'finance-automation-forms-kdr' ); ?></div>
			<div class="fafkdr-value"><?php echo esc_html( $valid_until ); ?></div>
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

		<?php if ( $address !== '' ) : ?>
			<div style="grid-column:1 / -1;">
				<div class="fafkdr-label"><?php esc_html_e( 'Billing Address', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value" style="font-weight:500; color:#334155;"><?php echo esc_html( $address ); ?></div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- ===================== ITEMS ===================== -->

<?php if ( ! empty( $items ) ) : ?>
	<div class="fafkdr-card" style="padding:16px;">
		<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Items', 'finance-automation-forms-kdr' ); ?></h2>

		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Item', 'finance-automation-forms-kdr' ); ?></th>
					<th style="width:80px;"><?php esc_html_e( 'Qty', 'finance-automation-forms-kdr' ); ?></th>
					<th style="width:100px;"><?php esc_html_e( 'Rate', 'finance-automation-forms-kdr' ); ?></th>
					<th style="width:80px;"><?php esc_html_e( 'GST/Tax %', 'finance-automation-forms-kdr' ); ?></th>
					<th style="width:120px;"><?php esc_html_e( 'Line Total', 'finance-automation-forms-kdr' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $it ) : ?>
					<?php
					$name = isset( $it['name'] ) ? (string) $it['name'] : '-';
					$qty  = isset( $it['qty'] ) ? (string) $it['qty'] : '-';
					$rate = isset( $it['rate'] ) ? (string) $it['rate'] : '-';

					$gst = '';
					if ( isset( $it['gst_rate'] ) ) {
						$gst = (string) $it['gst_rate'];
					} elseif ( isset( $it['tax'] ) ) {
						$gst = (string) $it['tax'];
					}

					$line_total = '';
					if ( isset( $it['line_total_display'] ) && '' !== (string) $it['line_total_display'] ) {
						$line_total = (string) $it['line_total_display'];
					} else {
						$line_total = number_format( ( (float) $qty ) * ( (float) $rate ), 2 );
					}
					?>
					<tr>
						<td><?php echo esc_html( $name ?: '-' ); ?></td>
						<td><?php echo esc_html( $qty ); ?></td>
						<td><?php echo esc_html( $rate ); ?></td>
						<td><?php echo esc_html( $gst !== '' ? $gst : '-' ); ?></td>
						<td><?php echo esc_html( $line_total ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<!-- Totals -->
		<div style="margin-top:12px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
			<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
				<div class="fafkdr-label"><?php esc_html_e( 'Subtotal', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value"><?php echo esc_html( number_format( $subtotal, 2 ) ); ?></div>
			</div>

			<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
				<div class="fafkdr-label"><?php esc_html_e( 'Tax Total', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value"><?php echo esc_html( number_format( $tax_total, 2 ) ); ?></div>
			</div>

			<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
				<div class="fafkdr-label"><?php esc_html_e( 'Grand Total', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value" style="font-weight:800;"><?php echo esc_html( number_format( $grand_total, 2 ) ); ?></div>
			</div>
		</div>
	</div>
<?php else : ?>
	<div class="fafkdr-card" style="padding:16px;">
		<h2 style="margin:0 0 10px;"><?php esc_html_e( 'Items', 'finance-automation-forms-kdr' ); ?></h2>
		<p style="margin:0; color:#64748b;"><?php esc_html_e( 'No items found in this submission.', 'finance-automation-forms-kdr' ); ?></p>
	</div>
<?php endif; ?>

<!-- ===================== NOTES & TERMS ===================== -->

<?php if ( $notes !== '' || $terms !== '' ) : ?>
	<div class="fafkdr-card" style="padding:16px;">
		<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Notes & Terms', 'finance-automation-forms-kdr' ); ?></h2>

		<?php if ( $notes !== '' ) : ?>
			<div style="margin-bottom:10px;">
				<div class="fafkdr-label"><?php esc_html_e( 'Notes', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value" style="font-weight:500; color:#334155;"><?php echo esc_html( $notes ); ?></div>
			</div>
		<?php endif; ?>

		<?php if ( $terms !== '' ) : ?>
			<div>
				<div class="fafkdr-label"><?php esc_html_e( 'Terms', 'finance-automation-forms-kdr' ); ?></div>
				<div class="fafkdr-value" style="font-weight:500; color:#334155;"><?php echo esc_html( $terms ); ?></div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<!-- ===================== OPTIONS ===================== -->

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Options', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:flex; gap:20px; flex-wrap:wrap;">
		<div>
			<strong><?php esc_html_e( 'Generate PDF:', 'finance-automation-forms-kdr' ); ?></strong>
			<?php echo esc_html( $generate_pdf ? 'Yes' : 'No' ); ?>
		</div>

		<div>
			<strong><?php esc_html_e( 'Send Email:', 'finance-automation-forms-kdr' ); ?></strong>
			<?php echo esc_html( $send_email ? 'Yes' : 'No' ); ?>
		</div>
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