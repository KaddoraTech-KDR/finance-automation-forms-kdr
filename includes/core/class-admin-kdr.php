<?php
/**
 * Admin UI (lean v1) for Finance Automation Forms kdr
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('FAFKDR_Admin_kdr')):

	final class FAFKDR_Admin_kdr
	{

		private static $instance_kdr = null;

		public static function instance_kdr(): FAFKDR_Admin_kdr
		{
			if (null === self::$instance_kdr) {
				self::$instance_kdr = new self();
			}
			return self::$instance_kdr;
		}

		private function __construct()
		{
		}

		public function init_kdr(): void
		{
			add_action('admin_menu', array($this, 'register_menu_kdr'));
			add_action('admin_enqueue_scripts', array($this, 'enqueue_assets_kdr'));
			add_action('admin_init', array($this, 'maybe_export_csv_kdr'));
			add_action('admin_init', array($this, 'register_settings_kdr'));
		}

		public function register_menu_kdr(): void
		{
			$cap = 'manage_options';

			add_menu_page(
				__('Finance Automation Forms kdr', 'finance-automation-forms-kdr'),
				__('Finance Forms kdr', 'finance-automation-forms-kdr'),
				$cap,
				'fafkdr',
				array($this, 'render_dashboard_kdr'),
				'dashicons-media-spreadsheet',
				56
			);

			add_submenu_page(
				'fafkdr',
				__('Dashboard', 'finance-automation-forms-kdr'),
				__('Dashboard', 'finance-automation-forms-kdr'),
				$cap,
				'fafkdr',
				array($this, 'render_dashboard_kdr')
			);

			add_submenu_page(
				'fafkdr',
				__('Submissions', 'finance-automation-forms-kdr'),
				__('Submissions', 'finance-automation-forms-kdr'),
				$cap,
				'fafkdr-submissions',
				array($this, 'render_submissions_kdr')
			);

			add_submenu_page(
				'fafkdr',
				__('Settings', 'finance-automation-forms-kdr'),
				__('Settings', 'finance-automation-forms-kdr'),
				$cap,
				'fafkdr-settings',
				array($this, 'render_settings_kdr')
			);
		}

		public function enqueue_assets_kdr(string $hook): void
		{
			// Only load on our plugin pages.
			$page = isset($_GET['page']) ? sanitize_text_field((string) wp_unslash($_GET['page'])) : '';
			if ('' === $page || false === strpos($page, 'fafkdr')) {
				return;
			}

			wp_enqueue_style(
				'fafkdr-admin',
				FAFKDR_PLUGIN_URL . 'assets/admin.css',
				array(),
				FAFKDR_VERSION
			);

			wp_enqueue_script(
				'fafkdr-admin',
				FAFKDR_PLUGIN_URL . 'assets/admin.js',
				array('jquery'),
				FAFKDR_VERSION,
				true
			);
		}
		private function render_submission_view_kdr(string $form_type, array $row, array $payload): void
		{
			$form_type = sanitize_key($form_type);

			$base = trailingslashit(FAFKDR_PLUGIN_DIR) . 'includes/admin-views/submissions/';
			$file = $base . 'view-' . $form_type . '-kdr.php';

			if (!file_exists($file)) {
				$file = $base . 'view-fallback-kdr.php';
			}

			// Common values for all views
			$customer_email = isset($row['customer_email']) ? (string) $row['customer_email'] : '';
			$uploads = (isset($payload['uploads']) && is_array($payload['uploads'])) ? $payload['uploads'] : [];
			$items = (isset($payload['items']) && is_array($payload['items'])) ? $payload['items'] : [];

			include $file;
		}
		public function register_settings_kdr(): void
		{

			register_setting(
				'fafkdr_settings_group',
				'fafkdr_settings_kdr',
				array(
					'type' => 'array',
					'sanitize_callback' => array($this, 'sanitize_settings_kdr'),
					'default' => array(),
				)
			);

			// Page: fafkdr-settings
			add_settings_section(
				'fafkdr_sec_business',
				__('Business Details', 'finance-automation-forms-kdr'),
				function () {
					echo '<p style="margin:0;color:#64748b;">' . esc_html__('Used in invoices, PDFs, email templates, and exports.', 'finance-automation-forms-kdr') . '</p>';
				},
				'fafkdr-settings'
			);

			add_settings_field(
				'business_name',
				__('Business Name', 'finance-automation-forms-kdr'),
				array($this, 'field_text_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_business',
				array('key' => 'business_name', 'placeholder' => 'Kaddora Tech')
			);

			add_settings_field(
				'business_email',
				__('Business Email', 'finance-automation-forms-kdr'),
				array($this, 'field_email_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_business',
				array('key' => 'business_email', 'placeholder' => 'billing@example.com')
			);

			add_settings_field(
				'business_phone',
				__('Business Phone', 'finance-automation-forms-kdr'),
				array($this, 'field_text_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_business',
				array('key' => 'business_phone', 'placeholder' => '+91...')
			);

			add_settings_field(
				'business_address',
				__('Business Address', 'finance-automation-forms-kdr'),
				array($this, 'field_textarea_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_business',
				array('key' => 'business_address', 'placeholder' => 'Street, City, State, ZIP')
			);

			add_settings_field(
				'business_gstin',
				__('GSTIN (Optional)', 'finance-automation-forms-kdr'),
				array($this, 'field_text_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_business',
				array('key' => 'business_gstin', 'placeholder' => '22AAAAA0000A1Z5')
			);

			// GST defaults
			add_settings_section(
				'fafkdr_sec_gst',
				__('GST Defaults', 'finance-automation-forms-kdr'),
				function () {
					echo '<p style="margin:0;color:#64748b;">' . esc_html__('Default behavior for GST calculations (used in billing/invoice/quotation).', 'finance-automation-forms-kdr') . '</p>';
				},
				'fafkdr-settings'
			);

			add_settings_field(
				'default_currency',
				__('Default Currency', 'finance-automation-forms-kdr'),
				array($this, 'field_select_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_gst',
				array(
					'key' => 'default_currency',
					'options' => array(
						'INR' => 'INR',
						'USD' => 'USD',
						'EUR' => 'EUR',
						'GBP' => 'GBP',
					),
				)
			);

			add_settings_field(
				'default_gst_rate',
				__('Default GST Rate (%)', 'finance-automation-forms-kdr'),
				array($this, 'field_number_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_gst',
				array('key' => 'default_gst_rate', 'min' => 0, 'max' => 28, 'step' => 1, 'placeholder' => '18')
			);

			add_settings_field(
				'default_gst_type',
				__('GST Type', 'finance-automation-forms-kdr'),
				array($this, 'field_select_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_gst',
				array(
					'key' => 'default_gst_type',
					'options' => array(
						'intra' => __('Intra-state (CGST + SGST)', 'finance-automation-forms-kdr'),
						'inter' => __('Inter-state (IGST)', 'finance-automation-forms-kdr'),
					),
				)
			);

			// Email
			add_settings_section(
				'fafkdr_sec_email',
				__('Email', 'finance-automation-forms-kdr'),
				function () {
					echo '<p style="margin:0;color:#64748b;">' . esc_html__('Sender details for receipts and invoice emails.', 'finance-automation-forms-kdr') . '</p>';
				},
				'fafkdr-settings'
			);

			add_settings_field(
				'from_name',
				__('From Name', 'finance-automation-forms-kdr'),
				array($this, 'field_text_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_email',
				array('key' => 'from_name', 'placeholder' => 'Finance Team')
			);

			add_settings_field(
				'from_email',
				__('From Email', 'finance-automation-forms-kdr'),
				array($this, 'field_email_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_email',
				array('key' => 'from_email', 'placeholder' => 'no-reply@example.com')
			);

			// Export / Uploads
			add_settings_section(
				'fafkdr_sec_export',
				__('Export & Uploads', 'finance-automation-forms-kdr'),
				function () {
					echo '<p style="margin:0;color:#64748b;">' . esc_html__('Basic defaults for exports and uploaded documents.', 'finance-automation-forms-kdr') . '</p>';
				},
				'fafkdr-settings'
			);

			add_settings_field(
				'export_delimiter',
				__('CSV Delimiter', 'finance-automation-forms-kdr'),
				array($this, 'field_select_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_export',
				array(
					'key' => 'export_delimiter',
					'options' => array(
						',' => __('Comma (,)', 'finance-automation-forms-kdr'),
						';' => __('Semicolon (;)', 'finance-automation-forms-kdr'),
						"\t" => __('Tab', 'finance-automation-forms-kdr'),
					),
				)
			);

			add_settings_field(
				'upload_max_mb',
				__('Max Upload Size (MB)', 'finance-automation-forms-kdr'),
				array($this, 'field_number_kdr'),
				'fafkdr-settings',
				'fafkdr_sec_export',
				array('key' => 'upload_max_mb', 'min' => 1, 'max' => 50, 'step' => 1, 'placeholder' => '10')
			);
		}
		public function sanitize_settings_kdr($input): array
		{
			$in = is_array($input) ? $input : array();

			$out = array();

			$out['business_name'] = isset($in['business_name']) ? sanitize_text_field($in['business_name']) : '';
			$out['business_email'] = isset($in['business_email']) ? sanitize_email($in['business_email']) : '';
			$out['business_phone'] = isset($in['business_phone']) ? sanitize_text_field($in['business_phone']) : '';
			$out['business_address'] = isset($in['business_address']) ? sanitize_textarea_field($in['business_address']) : '';
			$out['business_gstin'] = isset($in['business_gstin']) ? sanitize_text_field($in['business_gstin']) : '';

			$out['default_currency'] = isset($in['default_currency']) ? sanitize_text_field($in['default_currency']) : 'INR';
			$out['default_gst_rate'] = isset($in['default_gst_rate']) ? max(0, min(28, (int) $in['default_gst_rate'])) : 18;
			$out['default_gst_type'] = isset($in['default_gst_type']) ? sanitize_key($in['default_gst_type']) : 'intra';

			$out['from_name'] = isset($in['from_name']) ? sanitize_text_field($in['from_name']) : '';
			$out['from_email'] = isset($in['from_email']) ? sanitize_email($in['from_email']) : '';

			$out['export_delimiter'] = isset($in['export_delimiter']) ? (string) $in['export_delimiter'] : ',';
			if (!in_array($out['export_delimiter'], array(',', ';', "\t"), true)) {
				$out['export_delimiter'] = ',';
			}

			$out['upload_max_mb'] = isset($in['upload_max_mb']) ? max(1, min(50, (int) $in['upload_max_mb'])) : 10;

			add_settings_error('fafkdr_settings', 'fafkdr_saved', __('Settings saved.', 'finance-automation-forms-kdr'), 'updated');

			return $out;
		}

		private function get_settings_kdr(): array
		{
			$opt = get_option('fafkdr_settings_kdr', array());
			return is_array($opt) ? $opt : array();
		}

		public function field_text_kdr(array $args): void
		{
			$s = $this->get_settings_kdr();
			$key = (string) ($args['key'] ?? '');
			$val = isset($s[$key]) ? (string) $s[$key] : '';
			$ph = isset($args['placeholder']) ? (string) $args['placeholder'] : '';
			printf(
				'<input type="text" class="regular-text" name="fafkdr_settings_kdr[%1$s]" value="%2$s" placeholder="%3$s" />',
				esc_attr($key),
				esc_attr($val),
				esc_attr($ph)
			);
		}

		public function field_email_kdr(array $args): void
		{
			$s = $this->get_settings_kdr();
			$key = (string) ($args['key'] ?? '');
			$val = isset($s[$key]) ? (string) $s[$key] : '';
			$ph = isset($args['placeholder']) ? (string) $args['placeholder'] : '';
			printf(
				'<input type="email" class="regular-text" name="fafkdr_settings_kdr[%1$s]" value="%2$s" placeholder="%3$s" />',
				esc_attr($key),
				esc_attr($val),
				esc_attr($ph)
			);
		}

		public function field_textarea_kdr(array $args): void
		{
			$s = $this->get_settings_kdr();
			$key = (string) ($args['key'] ?? '');
			$val = isset($s[$key]) ? (string) $s[$key] : '';
			$ph = isset($args['placeholder']) ? (string) $args['placeholder'] : '';
			printf(
				'<textarea class="large-text" rows="3" name="fafkdr_settings_kdr[%1$s]" placeholder="%3$s">%2$s</textarea>',
				esc_attr($key),
				esc_textarea($val),
				esc_attr($ph)
			);
		}

		public function field_number_kdr(array $args): void
		{
			$s = $this->get_settings_kdr();
			$key = (string) ($args['key'] ?? '');
			$val = isset($s[$key]) ? (string) $s[$key] : '';
			$min = isset($args['min']) ? (string) $args['min'] : '0';
			$max = isset($args['max']) ? (string) $args['max'] : '999999';
			$step = isset($args['step']) ? (string) $args['step'] : '1';
			$ph = isset($args['placeholder']) ? (string) $args['placeholder'] : '';
			printf(
				'<input type="number" name="fafkdr_settings_kdr[%1$s]" value="%2$s" min="%3$s" max="%4$s" step="%5$s" placeholder="%6$s" />',
				esc_attr($key),
				esc_attr($val),
				esc_attr($min),
				esc_attr($max),
				esc_attr($step),
				esc_attr($ph)
			);
		}

		public function field_select_kdr(array $args): void
		{
			$s = $this->get_settings_kdr();
			$key = (string) ($args['key'] ?? '');
			$val = isset($s[$key]) ? (string) $s[$key] : '';
			$options = isset($args['options']) && is_array($args['options']) ? $args['options'] : array();

			echo '<select name="fafkdr_settings_kdr[' . esc_attr($key) . ']">';
			foreach ($options as $k => $label) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr((string) $k),
					selected($val, (string) $k, false),
					esc_html((string) $label)
				);
			}
			echo '</select>';
		}
		public function maybe_export_csv_kdr(): void
		{
			if (!is_admin()) {
				return;
			}

			$page = isset($_GET['page']) ? sanitize_text_field((string) wp_unslash($_GET['page'])) : '';
			if ('fafkdr-submissions' !== $page) {
				return;
			}

			$do_export = isset($_GET['fafkdr_export']) ? sanitize_key((string) wp_unslash($_GET['fafkdr_export'])) : '';
			if ('csv' !== $do_export) {
				return;
			}

			$cap = 'manage_options';
			if (!current_user_can($cap)) {
				wp_die(esc_html__('You do not have permission to export.', 'finance-automation-forms-kdr'));
			}

			$nonce = isset($_GET['_wpnonce']) ? (string) wp_unslash($_GET['_wpnonce']) : '';
			if (!wp_verify_nonce($nonce, 'fafkdr_export_csv')) {
				wp_die(esc_html__('Security check failed.', 'finance-automation-forms-kdr'));
			}

			global $wpdb;
			$table = FAFKDR_DB_kdr::table_submissions_kdr();

			$rows = $wpdb->get_results(
				"SELECT * FROM {$table} ORDER BY id DESC LIMIT 500",
				ARRAY_A
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

			// Clean any accidental output buffer to prevent headers-sent issues.
			while (ob_get_level() > 0) {
				ob_end_clean();
			}

			nocache_headers();
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=fafkdr-submissions.csv');
			header('Pragma: no-cache');
			header('Expires: 0');

			$out = fopen('php://output', 'w');

			// User-friendly columns (v1)
			fputcsv($out, array(
				'ID',
				'Form Type',
				'Customer Name',
				'Customer Email',
				'Phone',
				'Currency',
				'Subtotal',
				'Tax',
				'Grand Total',
				'Status',
				'Created',
				'Summary',
			));

			if (empty($rows)) {
				fputcsv($out, array('no_rows'));
				fclose($out);
				exit;
			}

			foreach ($rows as $r) {

				// ---- Decode payload JSON (supports schema variations) ----
				$json = '';
				if (!empty($r['data'])) {
					$json = (string) $r['data'];
				} elseif (!empty($r['payload_json'])) {
					$json = (string) $r['payload_json'];
				} elseif (!empty($r['payload'])) {
					$json = (string) $r['payload'];
				} elseif (!empty($r['form_data'])) {
					$json = (string) $r['form_data'];
				}

				$payload = array();
				if ($json !== '') {
					$decoded = json_decode($json, true);
					if (is_array($decoded)) {
						$payload = $decoded;
					}
				}

				$form_type = isset($r['form_type']) ? (string) $r['form_type'] : '';

				// ---- Common fields across forms ----
				$customer_name = '';

				if (isset($payload['customer_name']) && !is_array($payload['customer_name'])) {
					$customer_name = (string) $payload['customer_name'];
				} elseif (isset($payload['name']) && !is_array($payload['name'])) {
					// payment form often uses "name"
					$customer_name = (string) $payload['name'];
				} elseif (isset($payload['applicant_full_name']) && !is_array($payload['applicant_full_name'])) {
					// gst form uses applicant_full_name
					$customer_name = (string) $payload['applicant_full_name'];
				} elseif (isset($payload['expense_vendor']) && !is_array($payload['expense_vendor'])) {
					// expense form may use vendor/payee
					$customer_name = (string) $payload['expense_vendor'];
				}

				$email = '';
				if (!empty($r['customer_email'])) {
					$email = (string) $r['customer_email'];
				} elseif (isset($payload['customer_email']) && !is_array($payload['customer_email'])) {
					$email = (string) $payload['customer_email'];
				} elseif (isset($payload['email']) && !is_array($payload['email'])) {
					$email = (string) $payload['email'];
				} elseif (isset($payload['applicant_email']) && !is_array($payload['applicant_email'])) {
					$email = (string) $payload['applicant_email'];
				}
				$phone = '';
				if (isset($payload['phone']) && !is_array($payload['phone'])) {
					$phone = (string) $payload['phone'];
				} elseif (isset($payload['applicant_phone']) && !is_array($payload['applicant_phone'])) {
					$phone = (string) $payload['applicant_phone'];
				}

				$currency = (isset($payload['currency']) && !is_array($payload['currency'])) ? (string) $payload['currency'] : '';

				// ---- Totals (best-effort; v1) ----
				$subtotal = '';
				$tax = '';
				$grand = '';

				// If you later store calc JSON in $r['calc'], we can use it here.
				if (!empty($r['calc'])) {
					$calc = json_decode((string) $r['calc'], true);
					if (is_array($calc)) {
						if (isset($calc['subtotal']))
							$subtotal = (string) $calc['subtotal'];
						if (isset($calc['tax_total']))
							$tax = (string) $calc['tax_total'];
						if (isset($calc['grand_total']))
							$grand = (string) $calc['grand_total'];
					}
				}

				// Fallback: try payload keys (billing form might not have these yet)
				if ($grand === '' && isset($payload['grand_total']) && !is_array($payload['grand_total'])) {
					$grand = (string) $payload['grand_total'];
				}

				// ---- Summary (short + readable) ----
				$summary = '';
				if ($form_type === 'billing') {
					$date = isset($payload['bill_date']) ? (string) $payload['bill_date'] : '';
					$summary = $date ? 'Bill Date: ' . $date : 'Billing';
				} elseif ($form_type === 'invoice') {
					$date = isset($payload['invoice_date']) ? (string) $payload['invoice_date'] : '';
					$summary = $date ? 'Invoice Date: ' . $date : 'Invoice';
				} elseif ($form_type === 'quotation') {
					$date = isset($payload['quote_date']) ? (string) $payload['quote_date'] : '';
					$summary = $date ? 'Quote Date: ' . $date : 'Quotation';
				} elseif ($form_type === 'expense') {
					$date = isset($payload['expense_date']) ? (string) $payload['expense_date'] : '';
					$summary = $date ? 'Expense Date: ' . $date : 'Expense';
				} elseif ($form_type === 'payment') {
					$amount = isset($payload['amount']) ? (string) $payload['amount'] : '';
					$summary = $amount ? 'Amount: ' . $amount : 'Payment';
				} elseif ($form_type === 'gst') {
					$pan = isset($payload['applicant_pan']) ? (string) $payload['applicant_pan'] : '';
					$summary = $pan ? 'PAN: ' . $pan : 'GST Registration';
				}

				fputcsv($out, array(
					isset($r['id']) ? (string) $r['id'] : '',
					$form_type,
					$customer_name,
					$email,
					$phone,
					$currency,
					$subtotal,
					$tax,
					$grand,
					isset($r['status']) ? (string) $r['status'] : '',
					isset($r['created_at']) ? (string) $r['created_at'] : '',
					$summary,
				));
			}

			fclose($out);
			exit;
		}

		private function fafkdr_extract_customer_name_from_row_kdr(array $row): string
		{
			// Try common JSON columns
			$json = '';

			if (!empty($row['data']) && is_string($row['data'])) {
				$json = $row['data'];
			} elseif (!empty($row['payload_json']) && is_string($row['payload_json'])) {
				$json = $row['payload_json'];
			} elseif (!empty($row['payload']) && is_string($row['payload'])) {
				$json = $row['payload'];
			} elseif (!empty($row['form_data']) && is_string($row['form_data'])) {
				$json = $row['form_data'];
			}

			if ('' === $json) {
				return '';
			}

			$payload = json_decode($json, true);
			if (!is_array($payload)) {
				return '';
			}

			// Order matters: invoice/billing/quotation -> gst -> payment
			$keys = array('customer_name', 'full_name', 'name');

			foreach ($keys as $k) {
				if (isset($payload[$k]) && !is_array($payload[$k])) {
					$val = trim((string) $payload[$k]);
					if ('' !== $val) {
						return $val;
					}
				}
			}

			return '';
		}

		public function render_dashboard_kdr(): void
		{
			global $wpdb;

			$cap = 'manage_options';
			if (!current_user_can($cap)) {
				wp_die(esc_html__('You do not have permission to access this page.', 'finance-automation-forms-kdr'));
			}

			// Quick stats (lightweight)
			$table = FAFKDR_DB_kdr::table_submissions_kdr();
			$counts = array(
				'total' => 0,
				'invoice' => 0,
				'payment' => 0,
				'quotation' => 0,
				'billing' => 0,
				'expense' => 0,
				'gst' => 0,
			);

			$rows = $wpdb->get_results(
				"SELECT form_type, COUNT(*) as cnt FROM {$table} GROUP BY form_type",
				ARRAY_A
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

			if (is_array($rows)) {
				foreach ($rows as $r) {
					$type = isset($r['form_type']) ? sanitize_key((string) $r['form_type']) : '';
					$cnt = isset($r['cnt']) ? (int) $r['cnt'] : 0;
					if (isset($counts[$type])) {
						$counts[$type] = $cnt;
					}
					$counts['total'] += $cnt;
				}
			}

			$submissions_url = admin_url('admin.php?page=fafkdr-submissions');
			$settings_url = admin_url('admin.php?page=fafkdr-settings');

			$shortcodes = array(
				'invoice' => '[fafkdr_form type="invoice"]',
				'payment' => '[fafkdr_form type="payment"]',
				'quotation' => '[fafkdr_form type="quotation"]',
				'billing' => '[fafkdr_form type="billing"]',
				'expense' => '[fafkdr_form type="expense"]',
				'gst' => '[fafkdr_form type="gst"]',
			);

			?>
			<div class="wrap fafkdr-admin fafkdr-dashboard">

				<div class="fafkdr-header">
					<div>
						<h1 class="fafkdr-title">
							<?php esc_html_e('Finance Automation Forms kdr', 'finance-automation-forms-kdr'); ?>
						</h1>
						<p class="fafkdr-subtitle">
							<?php esc_html_e('Create finance forms, collect submissions, and automate PDFs/emails/exports.', 'finance-automation-forms-kdr'); ?>
						</p>
					</div>

					<div class="fafkdr-header-actions">
						<a class="button button-primary" href="<?php echo esc_url($submissions_url); ?>">
							<?php esc_html_e('View Submissions', 'finance-automation-forms-kdr'); ?>
						</a>
						<a class="button" href="<?php echo esc_url($settings_url); ?>">
							<?php esc_html_e('Settings', 'finance-automation-forms-kdr'); ?>
						</a>
					</div>
				</div>

				<div class="fafkdr-grid fafkdr-grid--3">

					<!-- Total -->
					<div class="fafkdr-stat fafkdr-stat--primary">
						<div class="fafkdr-stat__label">
							<?php esc_html_e('Total Submissions', 'finance-automation-forms-kdr'); ?>
						</div>

						<div class="fafkdr-stat__value">
							<?php echo esc_html((string) $counts['total']); ?>
						</div>

						<div class="fafkdr-stat__hint">
							<?php esc_html_e('All forms combined', 'finance-automation-forms-kdr'); ?>
						</div>
					</div>

					<!-- Invoices -->
					<div class="fafkdr-stat">
						<div class="fafkdr-stat__label">
							<?php esc_html_e('Invoices', 'finance-automation-forms-kdr'); ?>
						</div>

						<div class="fafkdr-stat__value">
							<?php echo esc_html((string) $counts['invoice']); ?>
						</div>

						<div class="fafkdr-badge fafkdr-badge--blue">
							<?php esc_html_e('Invoice', 'finance-automation-forms-kdr'); ?>
						</div>
					</div>

					<!-- GST -->
					<div class="fafkdr-stat">
						<div class="fafkdr-stat__label">
							<?php esc_html_e('GST Registrations', 'finance-automation-forms-kdr'); ?>
						</div>

						<div class="fafkdr-stat__value">
							<?php echo esc_html((string) $counts['gst']); ?>
						</div>

						<div class="fafkdr-badge fafkdr-badge--purple">
							<?php esc_html_e('GST', 'finance-automation-forms-kdr'); ?>
						</div>
					</div>

				</div>

				<div class="fafkdr-grid fafkdr-grid--2" style="margin-top:14px;">
					<div class="fafkdr-card fafkdr-card--soft">
						<div class="fafkdr-card__head">
							<h2 class="fafkdr-card__title"><?php esc_html_e('Shortcodes', 'finance-automation-forms-kdr'); ?></h2>
							<span class="fafkdr-pill"><?php esc_html_e('Click to copy', 'finance-automation-forms-kdr'); ?></span>
						</div>

						<div class="fafkdr-shortcode-list">
							<?php foreach ($shortcodes as $type => $code): ?>
								<div class="fafkdr-shortcode-row">
									<div class="fafkdr-badge <?php echo esc_attr($this->dashboard_badge_class_kdr($type)); ?>">
										<?php echo esc_html(strtoupper($type)); ?>
									</div>

									<code class="fafkdr-shortcode" data-fafkdr-copy="<?php echo esc_attr($code); ?>">
																																<?php echo esc_html($code); ?>
																															</code>

									<button type="button" class="button fafkdr-copy-btn"
										data-fafkdr-copy-btn="<?php echo esc_attr($code); ?>">
										<?php esc_html_e('Copy', 'finance-automation-forms-kdr'); ?>
									</button>
								</div>
							<?php endforeach; ?>
						</div>

						<p class="fafkdr-muted" style="margin-top:12px;">
							<?php esc_html_e('Tip: paste a shortcode inside any page/post to show the form.', 'finance-automation-forms-kdr'); ?>
						</p>
					</div>
					<?php if (defined('WP_DEBUG') && WP_DEBUG): ?>
						<div class="fafkdr-card">
							<div class="fafkdr-card__head">
								<h2 class="fafkdr-card__title"><?php esc_html_e('Next steps (v1)', 'finance-automation-forms-kdr'); ?>
								</h2>
								<span class="fafkdr-pill"><?php esc_html_e('Roadmap', 'finance-automation-forms-kdr'); ?></span>
							</div>

							<ul class="fafkdr-checklist">
								<li><span
										class="fafkdr-dot fafkdr-dot--blue"></span><?php esc_html_e('Improve submissions UI + actions', 'finance-automation-forms-kdr'); ?>
								</li>
								<li><span
										class="fafkdr-dot fafkdr-dot--green"></span><?php esc_html_e('Add finance calculator + GST split', 'finance-automation-forms-kdr'); ?>
								</li>
								<li><span
										class="fafkdr-dot fafkdr-dot--purple"></span><?php esc_html_e('Add automation: PDF + Email + Export', 'finance-automation-forms-kdr'); ?>
								</li>
								<li><span
										class="fafkdr-dot fafkdr-dot--orange"></span><?php esc_html_e('Integrations: CRM / Accounting', 'finance-automation-forms-kdr'); ?>
								</li>
							</ul>

							<div class="fafkdr-actions-row">
								<a class="button button-primary" href="<?php echo esc_url($submissions_url); ?>">
									<?php esc_html_e('Go to Submissions', 'finance-automation-forms-kdr'); ?>
								</a>
								<a class="button" href="<?php echo esc_url($settings_url); ?>">
									<?php esc_html_e('Configure Settings', 'finance-automation-forms-kdr'); ?>
								</a>
							</div>

							<div class="fafkdr-note">
								<strong><?php esc_html_e('Automation location:', 'finance-automation-forms-kdr'); ?></strong>
								<?php esc_html_e('We will show “Generate PDF / Send Email / Export” on each submission detail page (best UX). Dashboard will show only summary + shortcuts.', 'finance-automation-forms-kdr'); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>

			</div>
			<?php
		}

		/**
		 * Small helper for badge colors on dashboard.
		 * (Keep in same class)
		 */
		private function dashboard_badge_class_kdr(string $type): string
		{
			$type = sanitize_key($type);
			if ('invoice' === $type)
				return 'fafkdr-badge--blue';
			if ('payment' === $type)
				return 'fafkdr-badge--green';
			if ('quotation' === $type)
				return 'fafkdr-badge--orange';
			if ('billing' === $type)
				return 'fafkdr-badge--purple';
			if ('expense' === $type)
				return 'fafkdr-badge--slate';
			if ('gst' === $type)
				return 'fafkdr-badge--pink';
			return 'fafkdr-badge--slate';
		}

		public function render_submissions_kdr(): void
		{
			global $wpdb;

			$cap = 'manage_options';
			if (!current_user_can($cap)) {
				wp_die(esc_html__('You do not have permission to access this page.', 'finance-automation-forms-kdr'));
			}

			// View details screen.
			$view_id = isset($_GET['view']) ? absint((string) wp_unslash($_GET['view'])) : 0;
			if ($view_id > 0) {
				$this->render_submission_detail_kdr($view_id);
				return;
			}

			// Default: list view.
			$table = FAFKDR_DB_kdr::table_submissions_kdr();

			// Pull JSON too so we can display customer name without extra queries.
			// 'data' is the column used by class-db-kdr.php; include payload_json as fallback safety.
			$rows = $wpdb->get_results(
				"SELECT * FROM {$table} ORDER BY id DESC LIMIT 100",
				ARRAY_A
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

			$export_url = wp_nonce_url(
				admin_url('admin.php?page=fafkdr-submissions&fafkdr_export=csv'),
				'fafkdr_export_csv'
			);
			?>
			<div class="wrap fafkdr-admin">
				<h1 style="display:flex; align-items:center; justify-content:space-between;">
					<span><?php esc_html_e('Submissions', 'finance-automation-forms-kdr'); ?></span>
					<a class="button button-secondary" href="<?php echo esc_url($export_url); ?>">
						<?php esc_html_e('Export CSV', 'finance-automation-forms-kdr'); ?>
					</a>
				</h1>

				<table class="widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e('ID', 'finance-automation-forms-kdr'); ?></th>
							<th><?php esc_html_e('Form Type', 'finance-automation-forms-kdr'); ?></th>
							<th><?php esc_html_e('Customer Name', 'finance-automation-forms-kdr'); ?></th>
							<th><?php esc_html_e('Customer Email', 'finance-automation-forms-kdr'); ?></th>
							<th><?php esc_html_e('Status', 'finance-automation-forms-kdr'); ?></th>
							<th><?php esc_html_e('Created', 'finance-automation-forms-kdr'); ?></th>
							<th><?php esc_html_e('Actions', 'finance-automation-forms-kdr'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($rows)): ?>
							<tr>
								<td colspan="7"><?php esc_html_e('No submissions found yet.', 'finance-automation-forms-kdr'); ?></td>
							</tr>
						<?php else: ?>
							<?php foreach ($rows as $r): ?>
								<?php
								$view_url = admin_url('admin.php?page=fafkdr-submissions&view=' . absint($r['id']));
								$customer_name = $this->fafkdr_extract_customer_name_from_row_kdr($r);
								$customer_email = isset($r['customer_email']) ? (string) $r['customer_email'] : '';
								?>
								<tr>
									<td><?php echo esc_html((string) $r['id']); ?></td>
									<td><?php echo esc_html((string) $r['form_type']); ?></td>
									<td><?php echo esc_html($customer_name !== '' ? $customer_name : '-'); ?></td>
									<td><?php echo esc_html($customer_email !== '' ? $customer_email : '-'); ?></td>
									<td><?php echo esc_html((string) $r['status']); ?></td>
									<td><?php echo esc_html((string) $r['created_at']); ?></td>
									<td>
										<a class="button button-small" href="<?php echo esc_url($view_url); ?>">
											<?php esc_html_e('View', 'finance-automation-forms-kdr'); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>

				<p style="margin-top:12px; color:#666;">
					<?php esc_html_e('Tip: open a submission to see full details and future automation actions (PDF, email, export).', 'finance-automation-forms-kdr'); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Detail screen: view one submission + show automation placeholders.
		 */
		private function render_submission_detail_kdr(int $id): void
		{
			global $wpdb;

			$table = FAFKDR_DB_kdr::table_submissions_kdr();
			$row = $wpdb->get_row(
				$wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
				ARRAY_A
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

			$back_url = admin_url('admin.php?page=fafkdr-submissions');

			if (empty($row)) {
				?>
				<div class="wrap fafkdr-admin">
					<h1><?php esc_html_e('Submission not found', 'finance-automation-forms-kdr'); ?></h1>
					<p><a class="button"
							href="<?php echo esc_url($back_url); ?>"><?php esc_html_e('Back', 'finance-automation-forms-kdr'); ?></a>
					</p>
				</div>
				<?php
				return;
			}

			// Robust payload read (supports different column names).
			$json = '';
			if (!empty($row['payload_json'])) {
				$json = (string) $row['payload_json'];
			}
			if ('' === $json && !empty($row['payload'])) {
				$json = (string) $row['payload'];
			}
			if ('' === $json && !empty($row['data'])) {
				$json = (string) $row['data'];
			}
			if ('' === $json && !empty($row['form_data'])) {
				$json = (string) $row['form_data'];
			}

			$payload = array();
			if ('' !== $json) {
				$decoded = json_decode($json, true);
				if (is_array($decoded)) {
					$payload = $decoded;
				}
			}

			$uploads = (isset($payload['uploads']) && is_array($payload['uploads'])) ? $payload['uploads'] : array();
			$items = (isset($payload['items']) && is_array($payload['items'])) ? $payload['items'] : array();

			$customer_email = isset($row['customer_email']) ? (string) $row['customer_email'] : '';
			$customer_name = '';

			if (isset($payload['customer_name']) && !is_array($payload['customer_name'])) {
				$customer_name = (string) $payload['customer_name'];
			} elseif (isset($payload['full_name']) && !is_array($payload['full_name'])) {
				$customer_name = (string) $payload['full_name']; // GST
			} elseif (isset($payload['name']) && !is_array($payload['name'])) {
				$customer_name = (string) $payload['name']; // Payment form
			}

			$currency = (isset($payload['currency']) && !is_array($payload['currency'])) ? (string) $payload['currency'] : '';
			if (empty($currency) && (string) $row['form_type'] === 'gst') {
				$currency = 'INR';
			}
			?>
			<div class="wrap fafkdr-admin">
				<div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
					<h1 style="margin:0;">
						<?php echo esc_html__('Submission', 'finance-automation-forms-kdr') . ' #' . esc_html((string) $row['id']); ?>
					</h1>
					<a class="button" href="<?php echo esc_url($back_url); ?>">
						<?php esc_html_e('Back to Submissions', 'finance-automation-forms-kdr'); ?>
					</a>
				</div>

				<div style="margin-top:14px; display:grid; grid-template-columns: 1.2fr 0.8fr; gap:14px; max-width: 1100px;">
					<!-- LEFT COLUMN -->
					<div style="display:flex; flex-direction:column; gap:14px;">
						<div class="fafkdr-card" style="padding:16px;">
							<div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
								<div>
									<h2 style="margin:0 0 8px;"><?php esc_html_e('Overview', 'finance-automation-forms-kdr'); ?>
									</h2>
									<div style="color:#64748b; font-size:13px;">
										<?php echo esc_html(strtoupper((string) $row['form_type'])); ?> •
										<?php echo esc_html((string) $row['created_at']); ?>
									</div>
								</div>
								<div
									style="padding:6px 10px; border-radius:999px; background:#eef2ff; color:#3730a3; font-weight:600; font-size:12px;">
									<?php echo esc_html((string) $row['status']); ?>
								</div>
							</div>

							<div style="margin-top:12px; display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
								<div>
									<div style="font-size:12px; color:#64748b;">
										<?php esc_html_e('Customer', 'finance-automation-forms-kdr'); ?>
									</div>
									<div style="font-weight:600;"><?php echo esc_html($customer_name ?: '-'); ?></div>
									<div style="color:#334155; font-size:13px;"><?php echo esc_html($customer_email ?: '-'); ?>
									</div>
								</div>
								<div>
									<div style="font-size:12px; color:#64748b;">
										<?php esc_html_e('Currency', 'finance-automation-forms-kdr'); ?>
									</div>
									<div style="font-weight:600;"><?php echo esc_html($currency ?: '-'); ?></div>
								</div>
							</div>
						</div>

						<?php if (!empty($items)): ?>
							<div class="fafkdr-card" style="padding:16px;">
								<h2 style="margin:0 0 10px;"><?php esc_html_e('Items', 'finance-automation-forms-kdr'); ?></h2>
								<table class="widefat fixed striped">
									<thead>
										<tr>
											<th><?php esc_html_e('Item', 'finance-automation-forms-kdr'); ?></th>
											<th style="width:70px;"><?php esc_html_e('Qty', 'finance-automation-forms-kdr'); ?></th>
											<th style="width:90px;"><?php esc_html_e('Rate', 'finance-automation-forms-kdr'); ?></th>
											<th style="width:90px;"><?php esc_html_e('GST/Tax', 'finance-automation-forms-kdr'); ?>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($items as $it): ?>
											<?php
											$name = isset($it['name']) ? (string) $it['name'] : '';
											$qty = isset($it['qty']) ? (string) $it['qty'] : '';
											$rate = isset($it['rate']) ? (string) $it['rate'] : '';
											$gst = isset($it['gst_rate']) ? (string) $it['gst_rate'] : (isset($it['tax']) ? (string) $it['tax'] : '');
											?>
											<tr>
												<td><?php echo esc_html($name ?: '-'); ?></td>
												<td><?php echo esc_html($qty ?: '-'); ?></td>
												<td><?php echo esc_html($rate ?: '-'); ?></td>
												<td><?php echo esc_html($gst !== '' ? $gst . '%' : '-'); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>

						<div class="fafkdr-card" style="padding:16px;">
							<?php $this->render_submission_view_kdr((string) $row['form_type'], $row, $payload); ?>
						</div>

					</div>

					<!-- RIGHT COLUMN -->
					<div style="display:flex; flex-direction:column; gap:14px;">
						<div class="fafkdr-card" style="padding:16px;">
							<h2 style="margin:0 0 10px;"><?php esc_html_e('Actions', 'finance-automation-forms-kdr'); ?></h2>
							<p style="margin:0 0 10px; color:#64748b; font-size:13px;">
								<?php esc_html_e('Automation will be enabled in the next step (PDF, email templates, exports, payment link).', 'finance-automation-forms-kdr'); ?>
							</p>
							<div style="display:flex; gap:8px; flex-wrap:wrap;">
								<button class="button button-primary"
									disabled><?php esc_html_e('Generate PDF', 'finance-automation-forms-kdr'); ?></button>
								<button class="button"
									disabled><?php esc_html_e('Send Email', 'finance-automation-forms-kdr'); ?></button>
								<button class="button"
									disabled><?php esc_html_e('Export', 'finance-automation-forms-kdr'); ?></button>
								<button class="button"
									disabled><?php esc_html_e('Payment Link', 'finance-automation-forms-kdr'); ?></button>
							</div>
						</div>

						<?php if (!empty($uploads)): ?>
							<div class="fafkdr-card" style="padding:16px;">
								<h2 style="margin:0 0 10px;"><?php esc_html_e('Files', 'finance-automation-forms-kdr'); ?></h2>
								<ul style="margin:0; padding-left:18px;">
									<?php foreach ($uploads as $key => $info): ?>
										<?php
										$url = isset($info['url']) ? esc_url((string) $info['url']) : '';
										$name = isset($info['name']) ? sanitize_text_field((string) $info['name']) : $key;
										?>
										<li>
											<strong><?php echo esc_html((string) $key); ?>:</strong>
											<?php if ($url): ?>
												<a href="<?php echo $url; ?>" target="_blank"
													rel="noopener noreferrer"><?php echo esc_html($name); ?></a>
											<?php else: ?>
												<?php echo esc_html($name); ?>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<div class="fafkdr-card" style="padding:16px;">
							<h2 style="margin:0 0 10px;"><?php esc_html_e('System', 'finance-automation-forms-kdr'); ?></h2>
							<div style="font-size:13px; color:#475569;">
								<div><strong><?php esc_html_e('Stored Payload:', 'finance-automation-forms-kdr'); ?></strong>
									<?php echo esc_html(empty($payload) ? 'No (check DB column name)' : 'Yes'); ?></div>
								<div><strong><?php esc_html_e('Uploads:', 'finance-automation-forms-kdr'); ?></strong>
									<?php echo esc_html(empty($uploads) ? 'None' : 'Available'); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		public function render_settings_kdr(): void
		{
			$cap = 'manage_options';
			if (!current_user_can($cap)) {
				wp_die(esc_html__('You do not have permission to access this page.', 'finance-automation-forms-kdr'));
			}

			?>
			<div class="wrap fafkdr-admin">
				<h1 style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
					<span><?php esc_html_e('Settings', 'finance-automation-forms-kdr'); ?></span>
					<span
						class="fafkdr-badge fafkdr-badge--purple"><?php esc_html_e('v1', 'finance-automation-forms-kdr'); ?></span>
				</h1>

				<p style="margin-top:6px; color:#64748b;">
					<?php esc_html_e('Configure business details, GST defaults, email sender, and basic export/upload preferences.', 'finance-automation-forms-kdr'); ?>
				</p>

				<?php settings_errors('fafkdr_settings'); ?>

				<form method="post" action="options.php">
					<?php
					settings_fields('fafkdr_settings_group');
					do_settings_sections('fafkdr-settings');
					submit_button(__('Save Settings', 'finance-automation-forms-kdr'));
					?>
				</form>

				<div class="fafkdr-card" style="margin-top:14px; padding:16px;">
					<h2 style="margin:0 0 8px;"><?php esc_html_e('Automation Note', 'finance-automation-forms-kdr'); ?></h2>
					<p style="margin:0; color:#64748b; font-size:13px;">
						<?php esc_html_e('PDF / Email / Export actions will appear on each submission detail page. These settings will be used by those actions.', 'finance-automation-forms-kdr'); ?>
					</p>
				</div>
			</div>
			<?php
		}
	}

endif;