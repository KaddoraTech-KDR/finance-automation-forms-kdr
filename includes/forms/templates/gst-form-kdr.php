<?php
/**
 * GST Registration form template (kdr)
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
		<div class="fafkdr-card__title"><?php esc_html_e( 'Applicant Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_full_name"><?php esc_html_e( 'Full Name *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_full_name" type="text" name="applicant_full_name" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_email"><?php esc_html_e( 'Email *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_email" type="email" name="applicant_email" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_phone"><?php esc_html_e( 'Phone *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_phone" type="tel" name="applicant_phone" placeholder="+91XXXXXXXXXX" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_pan"><?php esc_html_e( 'PAN *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_pan" type="text" name="applicant_pan" placeholder="AAAAA9999A" required>
			<span class="fafkdr-error" data-error-for="applicant_pan"></span>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_aadhaar"><?php esc_html_e( 'Aadhaar (Optional)', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_aadhaar" type="text" name="applicant_aadhaar">
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Business Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_legal_name"><?php esc_html_e( 'Legal Business Name *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_legal_name" type="text" name="business_legal_name" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_trade_name"><?php esc_html_e( 'Trade Name', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_trade_name" type="text" name="business_trade_name">
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_constitution"><?php esc_html_e( 'Business Constitution *', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_constitution" name="business_constitution" required>
				<option value="proprietorship"><?php esc_html_e( 'Proprietorship', 'finance-automation-forms-kdr' ); ?></option>
				<option value="partnership"><?php esc_html_e( 'Partnership', 'finance-automation-forms-kdr' ); ?></option>
				<option value="llp"><?php esc_html_e( 'LLP', 'finance-automation-forms-kdr' ); ?></option>
				<option value="company"><?php esc_html_e( 'Company', 'finance-automation-forms-kdr' ); ?></option>
				<option value="others"><?php esc_html_e( 'Others', 'finance-automation-forms-kdr' ); ?></option>
			</select>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_nature"><?php esc_html_e( 'Nature of Business *', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_nature" name="business_nature" required>
				<option value="retail"><?php esc_html_e( 'Retail', 'finance-automation-forms-kdr' ); ?></option>
				<option value="wholesale"><?php esc_html_e( 'Wholesale', 'finance-automation-forms-kdr' ); ?></option>
				<option value="services"><?php esc_html_e( 'Services', 'finance-automation-forms-kdr' ); ?></option>
				<option value="manufacturing"><?php esc_html_e( 'Manufacturing', 'finance-automation-forms-kdr' ); ?></option>
				<option value="other"><?php esc_html_e( 'Other', 'finance-automation-forms-kdr' ); ?></option>
			</select>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_commencement"><?php esc_html_e( 'Date of Commencement', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_commencement" type="date" name="business_commencement_date">
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Address Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_country"><?php esc_html_e( 'Country *', 'finance-automation-forms-kdr' ); ?></label>
			<select id="fafkdr_country" name="address_country" required>
				<option value="India">India</option>
				<option value="United States">United States</option>
				<option value="Canada">Canada</option>
				<option value="Australia">Australia</option>
			</select>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_state"><?php esc_html_e( 'State *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_state" type="text" name="address_state" required placeholder="e.g. Maharashtra">
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_district"><?php esc_html_e( 'District *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_district" type="text" name="address_district" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_city"><?php esc_html_e( 'City *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_city" type="text" name="address_city" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_postal"><?php esc_html_e( 'Postal Code *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_postal" type="text" name="address_postal_code" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_full_address"><?php esc_html_e( 'Full Address *', 'finance-automation-forms-kdr' ); ?></label>
			<textarea id="fafkdr_full_address" name="address_full" required></textarea>
		</div>

		<p style="margin-top:10px; font-size:12px; color:#64748b;">
			<?php esc_html_e( 'v1 note: Google address autocomplete will be added later in Settings (API key) + JS module.', 'finance-automation-forms-kdr' ); ?>
		</p>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Bank Details', 'finance-automation-forms-kdr' ); ?></div>

		<div class="fafkdr-field">
			<label for="fafkdr_acc_holder"><?php esc_html_e( 'Account Holder Name *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_acc_holder" type="text" name="bank_account_holder" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_bank_name"><?php esc_html_e( 'Bank Name *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_bank_name" type="text" name="bank_name" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_acc_no"><?php esc_html_e( 'Account Number *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_acc_no" type="text" name="bank_account_number" required>
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_ifsc"><?php esc_html_e( 'IFSC *', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_ifsc" type="text" name="bank_ifsc" required placeholder="e.g. HDFC0000123">
		</div>
	</div>

	<div class="fafkdr-card">
		<div class="fafkdr-card__title"><?php esc_html_e( 'Upload Documents', 'finance-automation-forms-kdr' ); ?></div>

		<p style="margin-top:0; font-size:12px; color:#64748b;">
			<?php esc_html_e( 'v1 note: document upload storage will be enabled in the next update. For now, you can still attach files but they may not be saved.', 'finance-automation-forms-kdr' ); ?>
		</p>

		<div class="fafkdr-field">
			<label for="fafkdr_pan_card"><?php esc_html_e( 'PAN Card (PDF/JPG/PNG)', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_pan_card" type="file" name="docs_pan_card" accept=".pdf,.jpg,.jpeg,.png">
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_address_proof"><?php esc_html_e( 'Address Proof (PDF/JPG/PNG)', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_address_proof" type="file" name="docs_address_proof" accept=".pdf,.jpg,.jpeg,.png">
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_photo"><?php esc_html_e( 'Photo (JPG/PNG)', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_photo" type="file" name="docs_photo" accept=".jpg,.jpeg,.png">
		</div>

		<div class="fafkdr-field">
			<label for="fafkdr_bank_proof"><?php esc_html_e( 'Bank Proof (PDF/JPG/PNG)', 'finance-automation-forms-kdr' ); ?></label>
			<input id="fafkdr_bank_proof" type="file" name="docs_bank_proof" accept=".pdf,.jpg,.jpeg,.png">
		</div>

		<div class="fafkdr-field" style="margin-top:12px;">
			<label>
				<input type="checkbox" name="confirm_details" value="1" required>
				<?php esc_html_e( 'I confirm details are correct *', 'finance-automation-forms-kdr' ); ?>
			</label>
		</div>

		<div class="fafkdr-submit-row">
			<button type="submit" class="fafkdr-btn fafkdr-btn--primary">
				<?php esc_html_e( 'Submit GST Registration', 'finance-automation-forms-kdr' ); ?>
			</button>
		</div>
	</div>

</form>