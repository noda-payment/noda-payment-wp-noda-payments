<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 */

?>

<div class="wrap">

	<div id="icon-themes" class="icon32"></div>
	<h2>Noda Payment Button Settings</h2>

	<?php settings_errors(); ?>

	<form method="POST" action="options.php">
		<?php
		settings_fields( 'noda_pay_button_general_settings' );
		do_settings_sections( 'noda_pay_button_general_settings' );
		?>
		<?php submit_button(); ?>
	</form>

</div>
