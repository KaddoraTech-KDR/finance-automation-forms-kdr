<?php
/**
 * Admin View: Billing Submission Details (kdr)
 *
 * Available vars from loader:
 * - $row (array)      : DB row (id, form_type, status, created_at, customer_email, etc.)
 * - $payload (array)  : Decoded JSON from submissions.data
 * - $items (array)    : $payload['items'] if present
 * - $uploads (array)  : $payload['uploads'] if present
 * - $customer_email (string) : from DB row
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Small safe getter (string only).
$get = function ( string $key, string $default = '-' ) use ( $payload ): string {
	if ( ! isset( $payload[ $key ] ) || is_array( $payload[ $key ] ) ) {
		return $default;
	}
	$val = trim( (string) $payload[ $key ] );
	return ( '' !== $val ) ? $val : $default;
};

// Items + totals.
$items = ( isset( $payload['items'] ) && is_array( $payload['items'] ) ) ? $payload['items'] : array();

$subtotal  = 0.0;
$tax_total = 0.0;

foreach ( $items as $it ) {
	$qty  = isset( $it['qty'] ) ? (float) $it['qty'] : 0.0;
	$rate = isset( $it['rate'] ) ? (float) $it['rate'] : 0.0;
	$tax  = isset( $it['tax'] ) ? (float) $it['tax'] : 0.0;

	$line     = $qty * $rate;
	$line_tax = ( $line * $tax ) / 100;

	$subtotal  += $line;
	$tax_total += $line_tax;
}

$grand_total = $subtotal + $tax_total;

// Customer fields
$customer_name  = $get( 'customer_name', '-' );
$payload_email  = $get( 'customer_email', '' );
$display_email  = ( '' !== $payload_email ) ? $payload_email : ( $customer_email ?: '-' );
$currency       = $get( 'currency', '-' );
$bill_date      = $get( 'bill_date', '-' );
$phone          = $get( 'phone', '-' );

// Options
$generate_pdf = ! empty( $payload['generate_pdf'] );
$send_email   = ! empty( $payload['send_email'] );
?>

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Billing Details', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
		<div>
			<div style="font-size:12px; color:#64748b;"><?php esc_html_e( 'Bill Date', 'finance-automation-forms-kdr' ); ?></div>
			<div style="font-weight:600;"><?php echo esc_html( $bill_date ); ?></div>
		</div>

		<div>
			<div style="font-size:12px; color:#64748b;"><?php esc_html_e( 'Currency', 'finance-automation-forms-kdr' ); ?></div>
			<div style="font-weight:600;"><?php echo esc_html( $currency ); ?></div>
		</div>

		<div>
			<div style="font-size:12px; color:#64748b;"><?php esc_html_e( 'Customer Name', 'finance-automation-forms-kdr' ); ?></div>
			<div style="font-weight:600;"><?php echo esc_html( $customer_name ); ?></div>
		</div>

		<div>
			<div style="font-size:12px; color:#64748b;"><?php esc_html_e( 'Customer Email', 'finance-automation-forms-kdr' ); ?></div>
			<div style="font-weight:600;"><?php echo esc_html( $display_email ); ?></div>
		</div>

		<div>
			<div style="font-size:12px; color:#64748b;"><?php esc_html_e( 'Phone', 'finance-automation-forms-kdr' ); ?></div>
			<div style="font-weight:600;"><?php echo esc_html( $phone ); ?></div>
		</div>
	</div>
</div>

<?php if ( ! empty( $items ) ) : ?>
	<div class="fafkdr-card" style="padding:16px;">
		<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Items', 'finance-automation-forms-kdr' ); ?></h2>

		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Item', 'finance-automation-forms-kdr' ); ?></th>
					<th style="width:80px;"><?php esc_html_e( 'Qty', 'finance-automation-forms-kdr' ); ?></th>
					<th style="width:100px;"><?php esc_html_e( 'Rate', 'finance-automation-forms-kdr' ); ?></th>
					<th style="width:80px;"><?php esc_html_e( 'Tax %', 'finance-automation-forms-kdr' ); ?></th>
					<th style="width:120px;"><?php esc_html_e( 'Line Total', 'finance-automation-forms-kdr' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $it ) : ?>
					<?php
					$name = isset( $it['name'] ) ? (string) $it['name'] : '-';
					$qty  = isset( $it['qty'] ) ? (string) $it['qty'] : '-';
					$rate = isset( $it['rate'] ) ? (string) $it['rate'] : '-';
					$tax  = isset( $it['tax'] ) ? (string) $it['tax'] : '-';

					$line_display = '';
					if ( isset( $it['line_total_display'] ) && '' !== (string) $it['line_total_display'] ) {
						$line_display = (string) $it['line_total_display'];
					} else {
						$line_display = number_format( ( (float) $qty ) * ( (float) $rate ), 2 );
					}
					?>
					<tr>
						<td><?php echo esc_html( $name ?: '-' ); ?></td>
						<td><?php echo esc_html( $qty ); ?></td>
						<td><?php echo esc_html( $rate ); ?></td>
						<td><?php echo esc_html( $tax ); ?></td>
						<td><?php echo esc_html( $line_display ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div style="margin-top:12px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
			<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
				<div style="font-size:12px; color:#64748b;"><?php esc_html_e( 'Subtotal', 'finance-automation-forms-kdr' ); ?></div>
				<div style="font-weight:700;"><?php echo esc_html( number_format( $subtotal, 2 ) ); ?></div>
			</div>

			<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
				<div style="font-size:12px; color:#64748b;"><?php esc_html_e( 'Tax Total', 'finance-automation-forms-kdr' ); ?></div>
				<div style="font-weight:700;"><?php echo esc_html( number_format( $tax_total, 2 ) ); ?></div>
			</div>

			<div style="padding:10px; border:1px solid #e5e7eb; border-radius:10px;">
				<div style="font-size:12px; color:#64748b;"><?php esc_html_e( 'Grand Total', 'finance-automation-forms-kdr' ); ?></div>
				<div style="font-weight:800;"><?php echo esc_html( number_format( $grand_total, 2 ) ); ?></div>
			</div>
		</div>
	</div>
<?php else : ?>
	<div class="fafkdr-card" style="padding:16px;">
		<h2 style="margin:0 0 10px;"><?php esc_html_e( 'Items', 'finance-automation-forms-kdr' ); ?></h2>
		<p style="margin:0; color:#64748b;"><?php esc_html_e( 'No items found in this submission.', 'finance-automation-forms-kdr' ); ?></p>
	</div>
<?php endif; ?>

<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 12px;"><?php esc_html_e( 'Options', 'finance-automation-forms-kdr' ); ?></h2>

	<div style="display:flex; gap:16px; flex-wrap:wrap;">
		<div>
			<strong><?php esc_html_e( 'Generate PDF:', 'finance-automation-forms-kdr' ); ?></strong>
			<?php echo esc_html( $generate_pdf ? __( 'Yes', 'finance-automation-forms-kdr' ) : __( 'No', 'finance-automation-forms-kdr' ) ); ?>
		</div>

		<div>
			<strong><?php esc_html_e( 'Send Email:', 'finance-automation-forms-kdr' ); ?></strong>
			<?php echo esc_html( $send_email ? __( 'Yes', 'finance-automation-forms-kdr' ) : __( 'No', 'finance-automation-forms-kdr' ) ); ?>
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