<div class="card-deck">
	<div class="card">
		<div class="card-body">
			<h4 class="card-title">OTP required?</h4>
			<input type="checkbox" data-toggle="toggle" <?=($config['otp_request'] && $config['otp_request']->value ? 'checked': ''); ?> data-name="otp_request" />
			<p>Disabling this will turn off OTP requirement altogether across the entire system. Users if valid will be taken straight to the Inbox. Please be careful.</p>
		</div>
	</div>
</div>
