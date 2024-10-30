<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<div class="dx-help-page">
		<div class="content alignleft">
			<h2 class='page-welcome'>Welcome to <span>Motivision!</span></h2>

			<div id="dx-help-content">

					<h2><?php _e( "Configuration Page", 'mtvision' ); ?></h2>
						
					<p><?php _e( "In this page you will be able to set up Motivision plugin. This settings will allow you to display the desired information to your members.", 'mtvision' ); ?></p>
					
					<form id="motivision-plugin-form" action="options.php" method="POST">
						<hr>
							<?php settings_fields( 'motivision_setting' ) ?>
							<?php do_settings_sections( 'motivision-plugin' ) ?>
							
							<input class="submit-button" type="submit" value="<?php _e( "Save", 'mtvision' ); ?>" />
					</form>
				
			</div>

			<footer class='dx-footer'>
				<a href="http://estadosbinarios.com/" target="_blank"><img class='footer-banner' src="<?=MOTIVISION_URL;?>/img/help-banner.jpg" alt="EstadosBinarios help"></a>
			</footer>

		</div>
		<div class="sidebar alignright">
			<h2>About the plugin</h2>
			<p>This plugin is built by <a href="http://estadosbinarios.com/" target="_blank">Carlos Pérez</a>, I am a spanish backend developer that loves to work on different platforms in order improve my skills and get knowledge from different sources. </p>
			<p>Find me on <a href="https://twitter.com/llegotardeyt" target="_blank">Twitter</a> or hire me on <a href="https://www.linkedin.com/in/mrcarlosdev/" target="_blank">LinkedIn</a></p>
		</div>
	</div>
	
</div>