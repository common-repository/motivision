<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-base-template"><br></div>
	<h2><?php _e( "Base plugin page", 'mtvision' ); ?></h2>
	
	<p><?php _e( "Sample base plugin page", 'mtvision' ); ?></p>
	
	<form id="motivision-plugin-form" action="options.php" method="POST">
		
			<?php settings_fields( 'motivision_setting' ) ?>
			<?php do_settings_sections( 'motivision-plugin' ) ?>
			
			<input type="submit" value="<?php _e( "Save", 'mtvision' ); ?>" />
	</form> <!-- end of #dxtemplate-form -->
</div>