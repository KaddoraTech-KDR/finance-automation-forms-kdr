<?php
/**
 * Invoice form template (kdr)
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
		<div class="fafkdr-card__title"><?php esc_html_e( 'Invoice Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_invoice_date"><?php esc_html_e( 'Invoice Date *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_invoice_date" type="date" name="invoice_date" required>
			<span class="fafkdr-error" data-error-for="invoice_date"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_due_date"><?php esc_html_e( 'Due Date', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_due_date" type="date" name="due_date">
			<span class="fafkdr-error" data-error-for="due_date"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_invoice_number"><?php esc_html_e( 'Invoice Number', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_invoice_number" type="text" value="<?php echo esc_attr__( 'Auto-generated', 'finance-automation-forms-kdr' ); ?>" readonly>
			<span class="fafkdr-error" data-error-for="invoice_number"></span>
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
			<label for="fafkdr_customer_gstin"><?php esc_html_e( 'GSTIN', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_customer_gstin" type="text" name="customer_gstin" placeholder="<?php echo esc_attr__( 'Enter GSTIN (optional)', 'finance-automation-forms-kdr' ); ?>">
			<span class="fafkdr-error" data-error-for="customer_gstin"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_billing_address"><?php esc_html_e( 'Billing Address', 'finance-automation-forms-kdr' ); ?></label>
			<textarea id="fafkdr_billing_address" name="billing_address"></textarea>
			<span class="fafkdr-error" data-error-for="billing_address"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_customer_state"><?php esc_html_e( 'State *', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_customer_state" name="customer_state" required>
				<option value=""><?php esc_html_e( 'Select State (India)', 'finance-automation-forms-kdr' ); ?></option>
				<option value="UP"><?php esc_html_e( 'Uttar Pradesh', 'finance-automation-forms-kdr' ); ?></option>
				<option value="MH"><?php esc_html_e( 'Maharashtra', 'finance-automation-forms-kdr' ); ?></option>
				<option value="DL"><?php esc_html_e( 'Delhi', 'finance-automation-forms-kdr' ); ?></option>
				<option value="GJ"><?php esc_html_e( 'Gujarat', 'finance-automation-forms-kdr' ); ?></option>
			</select>
			<span class="fafkdr-error" data-error-for="customer_state"></span>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Line Items', 'finance-automation-forms-kdr' ); ?></div>

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

		<p style="margin-top:10px; font-size:12px; color:#64748b;">
			<?php esc_html_e( 'Tip: line totals are preview only in v1. Full invoice totals + GST split will be added in the calculator step.', 'finance-automation-forms-kdr' ); ?>
		</p>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Taxes & Discounts (Global)', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label><strong><?php esc_html_e( 'Apply GST?', 'finance-automation-forms-kdr' ); ?></strong></label>
			<label><input type="radio" name="apply_gst" value="yes" checked> <?php esc_html_e( 'Yes', 'finance-automation-forms-kdr' ); ?></label>
			<label><input type="radio" name="apply_gst" value="no"> <?php esc_html_e( 'No', 'finance-automation-forms-kdr' ); ?></label>
		</div>

		<div class="fafkdr-field">
			<label><strong><?php esc_html_e( 'GST Type', 'finance-automation-forms-kdr' ); ?></strong></label>
			<label><input type="radio" name="gst_type" value="intra" checked> <?php esc_html_e( 'Intra-state (CGST + SGST)', 'finance-automation-forms-kdr' ); ?></label>
			<label><input type="radio" name="gst_type" value="inter"> <?php esc_html_e( 'Inter-state (IGST)', 'finance-automation-forms-kdr' ); ?></label>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_global_discount_type"><?php esc_html_e( 'Global Discount Type', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_global_discount_type" name="global_discount_type">
				<option value="none"><?php esc_html_e( 'None', 'finance-automation-forms-kdr' ); ?></option>
				<option value="percent"><?php esc_html_e( 'Percent (%)', 'finance-automation-forms-kdr' ); ?></option>
				<option value="fixed"><?php esc_html_e( 'Fixed', 'finance-automation-forms-kdr' ); ?></option>
			</select>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_global_discount_value"><?php esc_html_e( 'Global Discount Value', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_global_discount_value" type="number" name="global_discount_value" min="0" step="0.01" value="0">
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_shipping_charge"><?php esc_html_e( 'Shipping / Other Charges', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_shipping_charge" type="number" name="shipping_charge" min="0" step="0.01" value="0">
		</div>

		<div style="margin-top:10px;">
			<div><strong><?php esc_html_e( 'Preview Total:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="total">0.00</span></div>
			<div><strong><?php esc_html_e( 'Preview Tax:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="tax">0.00</span></div>
			<div><strong><?php esc_html_e( 'Preview Grand Total:', 'finance-automation-forms-kdr' ); ?></strong> <span data-fafkdr-total="grand">0.00</span></div>
		</div>
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
			<label><input type="checkbox" name="send_email" value="1" checked> <?php esc_html_e( 'Send invoice email to customer (later)', 'finance-automation-forms-kdr' ); ?></label>
		</div>

		<div class="fafkdr-field">
			<label><input type="checkbox" name="generate_pdf" value="1" checked> <?php esc_html_e( 'Generate PDF invoice (later)', 'finance-automation-forms-kdr' ); ?></label>
		</div>

		<div class="fafkdr-submit-row">
			<button type="submit" class="fafkdr-btn fafkdr-btn--primary">
				<?php esc_html_e( 'Create Invoice', 'finance-automation-forms-kdr' ); ?>
			</button>
		</div>
	</div>

</form>