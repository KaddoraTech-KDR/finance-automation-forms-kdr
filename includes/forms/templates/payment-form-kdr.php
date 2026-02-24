<?php
/**
 * Payment form template (kdr)
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
		<div class="fafkdr-card__title"><?php esc_html_e( 'Customer Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_customer_name"><?php esc_html_e( 'Name *', 'finance-automation-forms-kdr' ); ?></label>
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
			<input id="fafkdr_customer_phone" type="tel" name="customer_phone" placeholder="+91XXXXXXXXXX">
			<span class="fafkdr-error" data-error-for="customer_phone"></span>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Payment Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_payment_purpose"><?php esc_html_e( 'Purpose / Description *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_payment_purpose" type="text" name="payment_purpose" required>
			<span class="fafkdr-error" data-error-for="payment_purpose"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_amount"><?php esc_html_e( 'Amount *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_amount" type="number" name="payment_amount" min="1" step="0.01" required>
			<span class="fafkdr-error" data-error-for="payment_amount"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_currency"><?php esc_html_e( 'Currency *', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_currency" name="currency" required>
				<option value="INR">INR (₹)</option>
				<option value="USD">USD ($)</option>
				<option value="EUR">EUR (€)</option>
				<option value="GBP">GBP (£)</option>
				<option value="AUD">AUD (A$)</option>
				<option value="CAD">CAD (C$)</option>
				<option value="AED">AED</option>
				<option value="SAR">SAR</option>
				<option value="SGD">SGD</option>
			</select>
			<span class="fafkdr-error" data-error-for="currency"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_method"><?php esc_html_e( 'Payment Method *', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_method" name="payment_method" required>
				<option value="online"><?php esc_html_e( 'Online Payment', 'finance-automation-forms-kdr' ); ?></option>
				<option value="manual"><?php esc_html_e( 'Manual Payment', 'finance-automation-forms-kdr' ); ?></option>
			</select>
			<span class="fafkdr-error" data-error-for="payment_method"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_notes"><?php esc_html_e( 'Notes (Optional)', 'finance-automation-forms-kdr' ); ?></label>
			<textarea id="fafkdr_notes" name="notes" placeholder="<?php echo esc_attr__( 'Additional payment notes...', 'finance-automation-forms-kdr' ); ?>"></textarea>
			<span class="fafkdr-error" data-error-for="notes"></span>
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Options', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label>
				<input type="checkbox" name="generate_link" value="1" checked>
				<?php esc_html_e( 'Generate payment link (default for Online)', 'finance-automation-forms-kdr' ); ?>
			</label>
		</div>

		<div class="fafkdr-field">
			<label>
				<input type="checkbox" name="send_receipt" value="1" checked>
				<?php esc_html_e( 'Email payment receipt after success', 'finance-automation-forms-kdr' ); ?>
			</label>
		</div>

		<div class="fafkdr-field">
			<label>
				<input type="checkbox" name="save_customer" value="1">
				<?php esc_html_e( 'Save customer for future payments (later)', 'finance-automation-forms-kdr' ); ?>
			</label>
		</div>

		<div class="fafkdr-field">
			<label>
				<input type="checkbox" name="enable_tax" value="1">
				<?php esc_html_e( 'Enable tax calculation (later)', 'finance-automation-forms-kdr' ); ?>
			</label>
		</div>

		<div class="fafkdr-submit-row">
			<button type="submit" class="fafkdr-btn fafkdr-btn--primary">
				<?php esc_html_e( 'Proceed', 'finance-automation-forms-kdr' ); ?>
			</button>
		</div>

		<p style="margin-top:10px; font-size:12px; color:#64748b;">
			<?php esc_html_e( 'v1: submissions will be saved. Payment links and receipts will be automated in the next steps.', 'finance-automation-forms-kdr' ); ?>
		</p>
	</div>

</form>