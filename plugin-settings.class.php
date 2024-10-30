<?php

class Plugin_Settings {
	
	private $motivision_setting;
	/**
	 * Construct me
	 */
	public function __construct() {
		$this->motivision_setting = get_option( 'motivision_setting', '' );
		
		// register the checkbox
		add_action('admin_init', array( $this, 'register_settings' ) );
	}
		
	/**
	 * Setup the settings
	 * 
	 * Add a single checkbox setting for Active/Inactive and a text field 
	 * just for the sake of our demo
	 * 
	 */
	public function register_settings() {
		register_setting( 'motivision_setting', 'motivision_setting', array( $this, 'validate_settings' ) );

		add_settings_section(
			'settings_section',         // ID used to identify this section and with which to register options
			__( "Parameters", 'mtvision' ),                  // Title to be displayed on the administration page
			array($this, 'settings_callback'), // Callback used to render the description of the section
			'motivision-plugin'                           // Page on which to add this section of options
		);

        add_settings_field(
            'display_time',                      // ID used to identify the field throughout the theme
            __( "Display period: ", 'mtvision' ),                           // The label to the left of the option interface element
            array( $this, 'display_time_callback' ),   // The name of the function responsible for rendering the option interface
            'motivision-plugin',                          // The page on which this option will be displayed
            'settings_section'         // The name of the section to which this field belongs
        );
		
		add_settings_field(
			'goal_period',                      // ID used to identify the field throughout the theme
			__( "Number of desired posts per author in the period: ", 'mtvision' ),                           // The label to the left of the option interface element
			array( $this, 'goal_period_callback' ),   // The name of the function responsible for rendering the option interface
			'motivision-plugin',                          // The page on which this option will be displayed
			'settings_section'         // The name of the section to which this field belongs
		);

        add_settings_field(
            'setting_checkboxes',                      // ID used to identify the field throughout the theme
            __( "Roles to display in the panel: ", 'mtvision' ),                           // The label to the left of the option interface element
            array( $this, 'setting_checkboxes_callback' ),   // The name of the function responsible for rendering the option interface
            'motivision-plugin',                          // The page on which this option will be displayed
            'settings_section'         // The name of the section to which this field belongs
        );

        add_settings_field(
            'badge_popular',                      // ID used to identify the field throughout the theme
            __( "Number of posts to get Popular Badget: ", 'mtvision' ),                           // The label to the left of the option interface element
            array( $this, 'badge_popular_callback' ),   // The name of the function responsible for rendering the option interface
            'motivision-plugin',                          // The page on which this option will be displayed
            'settings_section'         // The name of the section to which this field belongs
        );
	}

	public function settings_callback() {
		echo _e( "Fill in the following parameters in order to display the desired information.", 'mtvision' );
	}
	
	public function goal_period_callback() {
		$out = '';
		$val = '4';
		
		// check if checkbox is checked
		if(! empty( $this->motivision_setting ) && isset ( $this->motivision_setting['goal_period'] ) ) {
			$val = $this->motivision_setting['goal_period'];
		}

		$out = '<input type="text" id="goal_period" name="motivision_setting[goal_period]" value="' . $val . '"  />';
		
		echo $out;
	}

    public function badge_popular_callback() {
        $out = '';
        $val = '100';

        // check if checkbox is checked
        if(! empty( $this->motivision_setting ) && isset ( $this->motivision_setting['badge_popular'] ) ) {
            $val = $this->motivision_setting['badge_popular'];
        }

        $out = '<input type="text" id="badge_popular" name="motivision_setting[badge_popular]" value="' . $val . '"  />';

        echo $out;
    }

    public function display_time_callback() {
        $out = '';
        $week = '';
        $month = '';
        $year = '';

        // check if checkbox is checked
        if(! empty( $this->motivision_setting ) && isset ( $this->motivision_setting['display_time'] ) ) {
            if($this->motivision_setting['display_time'] == 'week') { $week = 'selected';}
            if($this->motivision_setting['display_time'] == 'month') { $month = 'selected';}
            if($this->motivision_setting['display_time'] == 'year') { $year = 'selected';}
        }


        $out = '<select id="display_time" name="motivision_setting[display_time]">
          <option value="week" '.$week.'>Last week</option>
          <option value="month" '.$month.'>Last month</option>
          <option value="year" '.$year.'>Last year</option>
        </select>';

        echo $out;
    }

    public function setting_checkboxes_callback() {

        $allRoles = get_editable_roles();
        $roles = array();
        $out = '';

        foreach($allRoles as $key => $value) {
            $roles[] = $key;
            $out.= '<input type="checkbox" name="motivision_setting[roles]['.$key.']" value="'.$key.'" ';

            if(! empty( $this->motivision_setting ) && isset ( $this->motivision_setting['roles'][$key] ) ) {
                $out.= 'CHECKED';

            } else if (empty( $this->motivision_setting && ($key == 'editor' || $key == 'author' ) )) {

            }

            $out.= ' /> '.$key.' <br />';
        }

        echo $out;
    }
    
	public function validate_settings( $input ) {

	    if(!is_numeric($input['goal_period'])) {
            return false;
        }

		return $input;
	}
}
