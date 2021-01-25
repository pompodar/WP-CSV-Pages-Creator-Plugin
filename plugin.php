<?php
	/*
	Plugin Name: Add Pages Through CSV
	Plugin Name: http://example.com
	Description: The plugin to add pages through csv file path
	Version: 1.0.0
	Author: Svjatoslav Kachmar
	Author Uri: http://example.com
	*/

	if (!defined('ABSPATH'))
	{
		die("Hey, you don't access this file");
	}

	add_action('admin_menu', 'CSVP_settings_menu');
	function CSVP_settings_menu()
	{
		add_options_page('CSVP Plugin Settings', 'Post icon', 'manage_options', 'CSVP_plugin', 'CSVP_option_page');
	}

	function CSVP_option_page()
	{
		?>
<div class="wrap">
    <h2>Add CSV Pages</h2>
    <form action="" method="post">
        <?php
					settings_fields('CSVP_options');
					do_settings_sections('CSVP_plugin');
					submit_button('Save Changes', 'primary');
				?>
    </form>
</div>
<?php
	}

	// Register and define the settings
	add_action('admin_init', 'CSVP_admin_init');
	function CSVP_admin_init()
	{
		// Define the setting args
		$args = array(
			'type' => 'string',
			'default' => NULL
		);
		// Register settings
		register_setting('CSVP_options', 'CSVP_options', $args);

		// Add a settings section
		add_settings_section('CSVP_main', 'CSVP Plugin Settings', 'CSVP_section_text', 'CSVP_plugin');

		// Create input for file path
		add_settings_field('CSVP_settings_path', 'Path', 'CSVP_settings_path', 'CSVP_plugin', 'CSVP_main');

	}

	// Draw the section header
	function CSVP_section_text()
	{
		echo '<p>Enter your path here.</p>';

	}


	function CSVP_settings_path()
	{

		echo "<input id='path' name='path'
 type='text' required='required'/>";
		if (!empty($_POST["path"])) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
			$path = $_POST["path"];

			$wp_filesystem = new WP_Filesystem_Direct(null);
			$csvfile= $wp_filesystem->get_contents(plugin_dir_url( __FILE__ )
			                                       . $path);


			$lines = explode("\n", $csvfile); // split data by new lines
			foreach ($lines as $i => $line) {
				$values = explode(',', $line); // split lines by commas
				// set values removing them as we ago
				$linevalues[$i][0] = trim($values[0]); unset($values[0]);
				$linevalues[$i][1] = trim($values[1]); unset($values[1]);
				$linevalues[$i][2] = trim($values[2]); unset($values[2]);
			}

			for ($i = 0; $i < 3; $i++) {
				$PageGuid = site_url() . $linevalues[0][$i];
				$my_post  = array( 'post_title'     => $linevalues[0][$i],
				                   'post_type'      => 'page',
				                   'post_name'      => $linevalues[0][$i],
				                   'post_content'   => 'This is my page reql.',
				                   'post_status'    => 'publish',
				                   'comment_status' => 'closed',
				                   'ping_status'    => 'closed',
				                   'post_author'    => 1,
				                   'menu_order'     => 0,
				                   'guid'           => $PageGuid );

				$PageID = wp_insert_post( $my_post, FALSE ); // Get Post ID - FALSE to return 0 instead of wp_error.
			}
		}
	}