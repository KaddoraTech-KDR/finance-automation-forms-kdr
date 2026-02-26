<div class="fafkdr-card" style="padding:16px;">
	<h2 style="margin:0 0 8px;"><?php esc_html_e('Form Details', 'finance-automation-forms-kdr'); ?></h2>
	<p style="margin:0; color:#64748b;">
		<?php esc_html_e('Detail view for this form type will be added soon.', 'finance-automation-forms-kdr'); ?>
	</p>

	<?php if (defined('WP_DEBUG') && WP_DEBUG) : ?>
		<details style="margin-top:12px;">
			<summary style="cursor:pointer; font-weight:700;">
				<?php esc_html_e('Debug JSON', 'finance-automation-forms-kdr'); ?>
			</summary>
			<pre style="margin-top:10px; background:#0b1220; color:#e5e7eb; padding:12px; border-radius:10px; overflow:auto; max-height:420px;"><?php
				echo esc_html(wp_json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
			?></pre>
		</details>
	<?php endif; ?>
</div>