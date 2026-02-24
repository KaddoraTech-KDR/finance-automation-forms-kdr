<?php
/**
 * Quotation form template (kdr)
 *
 * Vars from render_form_kdr():
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
		<div class="fafkdr-card__title"><?php esc_html_e( 'Quote Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_quote_date"><?php esc_html_e( 'Quote Date *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_quote_date" type="date" name="quote_date" required>
			<span class="fafkdr-error" data-error-for="quote_date"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_valid_until"><?php esc_html_e( 'Valid Until *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_valid_until" type="date" name="valid_until" required>
			<span class="fafkdr-error" data-error-for="valid_until"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_quote_number"><?php esc_html_e( 'Quote Number', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_quote_number" type="text" value="<?php echo esc_attr__( 'Auto-generated', 'finance-automation-forms-kdr' ); ?>" readonly>
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
			<label for="fafkdr_customer_email"><?php esc_html_e( 'Email *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_customer_email" type="email" name="customer_email" required>
			<span class="fafkdr-error" data-error-for="customer_email"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_customer_phone"><?php esc_html_e( 'Phone', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_customer_phone" type="tel" name="customer_phone">
			<span class="fafkdr-error" data-error-for="customer_phone"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_billing_address"><?php esc_html_e( 'Billing Address', 'finance-automation-forms-kdr' ); ?></label>
			<textarea id="fafkdr_billing_address" name="billing_address"></textarea>
			<span class="fafkdr-error" data-error-for="billing_address"></span>
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
					<th><?php esc_html_e( 'Discount', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'Disc. Type', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'GST %', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'Line Total', 'finance-automation-forms-kdr' ); ?></th>
					<th><?php esc_html_e( 'Remove', 'finance-automation-forms-kdr' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="text" name="items[0][name]" required placeholder="Service / Product">
						<textarea name="items[0][description]" placeholder="Description (optional)"></textarea>
					</td>
					<td><input type="number" name="items[0][qty]" min="1" value="1" required></td>
					<td><input type="number" name="items[0][rate]" min="0" step="0.01" value="0" required></td>
					<td><input type="number" name="items[0][discount_value]" min="0" step="0.01" value="0"></td>
					<td>
						<select name="items[0][discount_type]">
							<option value="none"><?php esc_html_e( 'None', 'finance-automation-forms-kdr' ); ?></option>
							<option value="percent"><?php esc_html_e( 'Percent (%)', 'finance-automation-forms-kdr' ); ?></option>
							<option value="fixed"><?php esc_html_e( 'Fixed', 'finance-automation-forms-kdr' ); ?></option>
						</select>
					</td>
					<td>
						<select name="items[0][gst_rate]">
							<option value="0">0%</option>
							<option value="5">5%</option>
							<option value="12">12%</option>
							<option value="18">18%</option>
							<option value="28">28%</option>
						</select>
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

		<div style="margin-top:10px;">
			<div><strong><?php esc_html_e( 'Preview Total:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="total">0.00</span></div>
			<div><strong><?php esc_html_e( 'Preview Tax:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="tax">0.00</span></div>
			<div><strong><?php esc_html_e( 'Preview Grand Total:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="grand">0.00</span></div>
		</div>

		<p style="margin-top:10px; font-size:12px; color:#64748b;">
			<?php esc_html_e( 'v1 note: totals are preview only. Final quote totals + GST split will be added in the calculator step.', 'finance-automation-forms-kdr' ); ?>
		</p>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Notes & Terms', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_notes"><?php esc_html_e( 'Notes', 'finance-automation-forms-kdr' ); ?></label>
			<textarea id="fafkdr_notes" name="notes"></textarea>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_terms"><?php esc_html_e( 'Terms & Conditions', 'finance-automation-forms-kdr' ); ?></label>
			<textarea id="fafkdr_terms" name="terms"></textarea>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Submit', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label><input type="checkbox" name="send_email" value="1" checked> <?php esc_html_e( 'Email quotation (later)', 'finance-automation-forms-kdr' ); ?></label>
		</div>

		<div class="fafkdr-field">
			<label><input type="checkbox" name="generate_pdf" value="1" checked> <?php esc_html_e( 'Generate PDF quotation (later)', 'finance-automation-forms-kdr' ); ?></label>
		</div>

		<div class="fafkdr-submit-row">
			<button type="submit" class="fafkdr-btn fafkdr-btn--primary">
				<?php esc_html_e( 'Create Quotation', 'finance-automation-forms-kdr' ); ?>
			</button>
		</div>
	</div>

</form>