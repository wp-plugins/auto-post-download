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
		'setting_format_section_id', // ID
		__( 'Auto Post Download Settings', 'apd_auto-post-download' ) , // Title
		array( $this, 'print_format_section_info' ), // Callback
		'apd_settings_admin' // Page
		);

		add_settings_section(
		'setting_cats_section_id', // ID
		__( 'Categories', 'apd_auto-post-download' ) , // Title
		array( $this, 'print_cats_section_info' ), // Callback
		'apd_settings_admin' // Page
		);
		
		add_settings_section(
		'setting_custom_fields_section_id', // ID
		__('Custom fields', 'apd_auto-post-download' ), // Title
		array( $this, 'print_custom_fields_section_info' ), // Callback
		'apd_settings_admin' // Page
		);

		add_settings_field(
		'apd_format', // ID
		__("Format: ", 'apd_auto-post-download' ), // Title
		array( $this, 'format_callback' ), // Callback
		'apd_settings_admin', // Page
		'setting_format_section_id' // Section
		);
		
		add_settings_field(
		'apd_cats', // ID
		__("Categories: ", 'apd_auto-post-download' ), // Title
		array( $this, 'cats_array_callback' ), // Callback
		'apd_settings_admin', // Page
		'setting_cats_section_id' // Section
		);

		add_settings_field(
		'apd_meta', // ID
		__("Custom fields: ", 'apd_auto-post-download' ), // Title
		array( $this, 'meta_array_callback' ), // Callback
		'apd_settings_admin', // Page
		'setting_custom_fields_section_id' // Section
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

	public	function print_cats_section_info(){
		echo __("Choose categories of posts for which post pack should be generated.", 'apd_auto-post-download');
	}
	
	public	function print_format_section_info(){
		echo __("Choose in which format content of post should be generated, by default: html.", 'apd_auto-post-download');
	}
	
	public	function print_custom_fields_section_info(){
		echo __("Choose custom fields which should be included in content of generated attachment.", 'apd_auto-post-download');
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
	
	public function format_callback()
	{
		$format = $this->options['apd_format'];		
		?>
		<label for="html_format"><?php _e('HTML', 'apd_auto-post-download' ); ?></label>
		<input id="html_format" type="radio" name="apd_options[apd_format]" value="html" <?php echo ($format == "html" || $format==null) ? 'checked="checked"' : ""; ?> />
		<label for="plain_format"><?php _e('Plain text', 'apd_auto-post-download' ); ?></label>
		<input id="plain_format" type="radio" name="apd_options[apd_format]" value="text" <?php echo ($format == "text") ? 'checked="checked"' : ""; ?> />
		<?php 
	}
	
}

?>