<?php
/**
 * Expense form template (kdr)
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
	  action="<?php echo esc_url( $fafkdr_action_url ); ?>"
	  enctype="multipart/form-data">

	<input type="hidden" name="fafkdr_form_type" value="<?php echo esc_attr( $fafkdr_form_type ); ?>">
	<input type="hidden" name="fafkdr_nonce" value="<?php echo esc_attr( $fafkdr_nonce ); ?>">

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Expense Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_expense_date"><?php esc_html_e( 'Expense Date *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_expense_date" type="date" name="expense_date" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_category"><?php esc_html_e( 'Category *', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_category" name="expense_category" required>
				<option value="travel"><?php esc_html_e( 'Travel', 'finance-automation-forms-kdr' ); ?></option>
				<option value="food"><?php esc_html_e( 'Food', 'finance-automation-forms-kdr' ); ?></option>
				<option value="office"><?php esc_html_e( 'Office', 'finance-automation-forms-kdr' ); ?></option>
				<option value="utilities"><?php esc_html_e( 'Utilities', 'finance-automation-forms-kdr' ); ?></option>
				<option value="marketing"><?php esc_html_e( 'Marketing', 'finance-automation-forms-kdr' ); ?></option>
				<option value="salary"><?php esc_html_e( 'Salary', 'finance-automation-forms-kdr' ); ?></option>
				<option value="other"><?php esc_html_e( 'Other', 'finance-automation-forms-kdr' ); ?></option>
			</select>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_vendor"><?php esc_html_e( 'Vendor / Payee', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_vendor" type="text" name="expense_vendor" placeholder="<?php echo esc_attr__( 'Enter vendor or payee name', 'finance-automation-forms-kdr' ); ?>">
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_amount"><?php esc_html_e( 'Amount *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_amount" type="number" name="expense_amount" min="0" step="0.01" required>
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
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_payment_method"><?php esc_html_e( 'Payment Method', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_payment_method" name="expense_payment_method">
				<option value="cash"><?php esc_html_e( 'Cash', 'finance-automation-forms-kdr' ); ?></option>
				<option value="card"><?php esc_html_e( 'Card', 'finance-automation-forms-kdr' ); ?></option>
				<option value="upi"><?php esc_html_e( 'UPI', 'finance-automation-forms-kdr' ); ?></option>
				<option value="bank_transfer"><?php esc_html_e( 'Bank Transfer', 'finance-automation-forms-kdr' ); ?></option>
				<option value="other"><?php esc_html_e( 'Other', 'finance-automation-forms-kdr' ); ?></option>
			</select>
		</div>

		<div class="fafkdr-field">
			<label>
				<input type="checkbox" name="tax_included" value="1">
				<?php esc_html_e( 'Tax included in amount?', 'finance-automation-forms-kdr' ); ?>
			</label>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_tax_amount"><?php esc_html_e( 'Tax Amount', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_tax_amount" type="number" name="tax_amount" min="0" step="0.01" value="0">
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Notes', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_notes"><?php esc_html_e( 'Notes', 'finance-automation-forms-kdr' ); ?></label>
			<textarea id="fafkdr_notes" name="notes"></textarea>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Receipt (Optional)', 'finance-automation-forms-kdr' ); ?></div>

		<p style="margin-top:0; font-size:12px; color:#64748b;">
			<?php esc_html_e( 'v1 note: receipt uploads will be stored in the next update. For now, attachments may not be saved.', 'finance-automation-forms-kdr' ); ?>
		</p>

		<div class="fafkdr-field">
			<label for="fafkdr_receipt"><?php esc_html_e( 'Upload Receipt (PDF/JPG/PNG)', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_receipt" type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png">
		</div>

		<div class="fafkdr-submit-row">
			<button type="submit" class="fafkdr-btn fafkdr-btn--primary">
				<?php esc_html_e( 'Save Expense', 'finance-automation-forms-kdr' ); ?>
			</button>
		</div>
	</div>

</form>