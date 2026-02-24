<?php
/**
 * Billing form template (kdr)
 *
 * Available vars from render_form_kdr():
 * - $fafkdr_form_type
 * - $fafkdr_nonce
 * - $fafkdr_action_url
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form class="fafkdr-form fafkdr-form--<?php echo esc_attr( $fafkdr_form_type ); ?>"
	  method="post"
	  action="<?php echo esc_url( $fafkdr_action_url ); ?>">

	<input type="hidden" name="fafkdr_form_type" value="<?php echo esc_attr( $fafkdr_form_type ); ?>">
	<input type="hidden" name="fafkdr_nonce" value="<?php echo esc_attr( $fafkdr_nonce ); ?>">

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Bill Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_bill_date"><?php esc_html_e( 'Bill Date *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_bill_date" type="date" name="bill_date" required>
			<span class="fafkdr-error" data-error-for="bill_date"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_currency"><?php esc_html_e( 'Currency *', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_currency" name="currency" required>
				<option value="INR">INR (₹)</option>
				<option value="USD">USD ($)</option>
				<option value="EUR">EUR (€)</option>
				<option value="GBP">GBP (£)</option>
				<option value="AED">AED</option>
				<option value="SAR">SAR</option>
				<option value="SGD">SGD</option>
			</select>
			<span class="fafkdr-error" data-error-for="currency"></span>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Customer Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_customer_name"><?php esc_html_e( 'Customer Name *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_customer_name" type="text" name="customer_name" required>
			<span class="fafkdr-error" data-error-for="customer_name"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_phone"><?php esc_html_e( 'Phone', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_phone" type="tel" name="phone" placeholder="+91XXXXXXXXXX">
			<span class="fafkdr-error" data-error-for="phone"></span>
		</div>

		<!-- Optional (for future email automation) -->
		<div class="fafkdr-field">
			<label for="fafkdr_customer_email"><?php esc_html_e( 'Email', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_customer_email" type="email" name="customer_email" placeholder="customer@email.com">
			<span class="fafkdr-error" data-error-for="customer_email"></span>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Items', 'finance-automation-forms-kdr' ); ?></div>

		<table class="fafkdr-items" data-fafkdr-items-table="1">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Item', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'Qty', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'Rate', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'Tax %', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'Line Total', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'Remove', 'finance-automation-forms-kdr' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="text" name="items[0][name]" required placeholder="Consulting / Product / Service">
					</td>
					<td>
						<input type="number" name="items[0][qty]" min="1" value="1" required>
					</td>
					<td>
						<input type="number" name="items[0][rate]" min="0" step="0.01" value="0" required>
					</td>
					<td>
						<input type="number" name="items[0][tax]" min="0" step="0.01" value="0">
					</td>
					<td>
						<input type="text" name="items[0][line_total_display]" value="0.00" readonly>
					</td>
					<td>
						<span class="fafkdr-remove-row" role="button" tabindex="0">✕</span>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="fafkdr-add-row">
			<button type="button" class="fafkdr-btn fafkdr-btn--secondary fafkdr-add-row">
				<?php esc_html_e( '+ Add Item', 'finance-automation-forms-kdr' ); ?>
			</button>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Summary (Preview)', 'finance-automation-forms-kdr' ); ?></div>

		<p style="margin:0; color:#64748b;">
			<?php esc_html_e( 'v1 note: totals are preview only; final totals will be stored after submission (calculator will be added next).', 'finance-automation-forms-kdr' ); ?>
		</p>

		<div style="margin-top:10px;">
			<div><strong><?php esc_html_e( 'Total:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="total">0.00</span></div>
			<div><strong><?php esc_html_e( 'Tax:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="tax">0.00</span></div>
			<div><strong><?php esc_html_e( 'Grand Total:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="grand">0.00</span></div>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Options', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label>
				<input type="checkbox" name="generate_pdf" value="1" checked>
				<?php esc_html_e( 'Generate PDF Bill (later)', 'finance-automation-forms-kdr' ); ?>
			</label>
		</div>

		<div class="fafkdr-field">
			<label>
				<input type="checkbox" name="send_email" value="1">
				<?php esc_html_e( 'Send via Email (later)', 'finance-automation-forms-kdr' ); ?>
			</label>
		</div>

		<div class="fafkdr-submit-row">
			<button type="submit" class="fafkdr-btn fafkdr-btn--primary">
				<?php esc_html_e( 'Submit Bill', 'finance-automation-forms-kdr' ); ?>
			</button>
		</div>
	</div>

</form>