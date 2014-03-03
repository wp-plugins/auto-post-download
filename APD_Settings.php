<?php
class APD_Settings
{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page()
	{
		add_options_page(
		'Auto Post Download',
		'Auto Post Download',
		'manage_options',
		'apd_admin',
		array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page()
	{
		// Set class property
		$this->options = get_option( 'apd_options' );
		?>
<div class="wrap">
	<h2>
		<?php echo __( 'Auto Post Download', 'apd_auto-post-download' ); ?>
	</h2>
	<form method="post" action="options.php">
		<?php
		// This prints out all hidden setting fields
		settings_fields( 'apd_settings_group' );
		do_settings_sections( 'apd_settings_admin' );
		submit_button();
		?>
	</form>
</div>
<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init()
	{
		register_setting(
		'apd_settings_group', // Option group
		'apd_options', // Option name
		array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
		'setting_section_id', // ID
		__( 'Auto Post Download Settings', 'apd_auto-post-download' ) , // Title
		array( $this, 'print_section_info' ), // Callback
		'apd_settings_admin' // Page
		);

		add_settings_field(
		'apd_cats', // ID
		__("Categories: ", 'apd_auto-post-download' ), // Title
		array( $this, 'cats_array_callback' ), // Callback
		'apd_settings_admin', // Page
		'setting_section_id' // Section
		);

		add_settings_field(
		'apd_meta', // ID
		__("Custom fields: ", 'apd_auto-post-download' ), // Title
		array( $this, 'meta_array_callback' ), // Callback
		'apd_settings_admin', // Page
		'setting_section_id' // Section
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input )
	{
		return $input;
	}

	public	function print_section_info(){
		echo __("Choose categories of posts for which post pack should be generated.", 'apd_auto-post-download');
	}

	public function cats_array_callback()
	{
		?>
<select size="20" name="apd_options[apd_cats][]" multiple="multiple">
	<?php 
	$categories = get_categories();
	$i = 0;
	foreach ($categories as $category) {
			  		$option = '<option value="' .$category->term_id . '" ' . ((in_array($category->term_id, $this->options['apd_cats'])) ? 'selected="selected"' : '') . '>';
			  		$option .= $category->cat_name;
			  		$option .= ' ('.$category->category_count.')';
			 	 	$option .= '</option>';
			  		echo $option;
				}
				?>
</select>
<?php 
	}

	public function meta_array_callback()
	{
		?>
<select size="20" name="apd_options[apd_meta][]" multiple="multiple">
	<?php 
	$list_of_meta = $this->get_all_meta();

	$i = 0;
	foreach ($list_of_meta as $meta) {
		$meta = $meta[0];
		$option = '<option value="' . $meta . '" ' . ((in_array($meta, $this->options['apd_meta'])) ? 'selected="selected"' : '') . '>';
		$option .= $meta;
		$option .= '</option>';
		echo $option;
	}
	?>
</select>
<?php 
	}

	private function get_all_meta(){
		global $wpdb;
		$data = $wpdb->get_results("SELECT meta_key FROM $wpdb->postmeta group by meta_key order by meta_key", ARRAY_N);
		return $data;
	}
}

?>