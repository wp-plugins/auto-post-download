<?php
class APD_Settings
{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;
	private $selected_cats;
	private $selected_custom_fields;
	
	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		// Set class property
		$this->options = get_option( 'apd_options' );
		$this->selected_cats = (($this->options['apd_cats'] == null) ? array() : $this->options['apd_cats']);
		$this->selected_custom_fields = (($this->options['apd_meta'] == null) ? array() : $this->options['apd_meta']);
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
		'setting_content_section_id', // ID
		__( 'Auto Post Download Settings', 'apd_auto-post-download' ) , // Title
		array( $this, 'print_content_section_info' ), // Callback
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
		'apd_format', // ID
		__("Format: ", 'apd_auto-post-download' ), // Title
		array( $this, 'content_callback' ), // Callback
		'apd_settings_admin', // Page
		'setting_content_section_id' // Section
		);
		
		add_settings_field(
		'apd_cats', // ID
		__("Categories: ", 'apd_auto-post-download' ), // Title
		array( $this, 'cats_array_callback' ), // Callback
		'apd_settings_admin', // Page
		'setting_cats_section_id' // Section
		);

		add_settings_field(
		'apd_meta_title', // ID
		__("Show custom fields titles: ", 'apd_auto-post-download' ), // Title
		array( $this, 'meta_title_callback' ), // Callback
		'apd_settings_admin', // Page
		'setting_custom_fields_section_id' // Section
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
	
	public	function print_content_section_info(){
		echo __("Choose what should be included in generated archive (post content or it's image), by default: both.", 'apd_auto-post-download');
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
	
	foreach ($categories as $category) {
			  		$option = '<option value="' .$category->term_id . '" ' . ((in_array($category->term_id, $this->selected_cats)) ? 'selected="selected"' : '') . '>';
			  		$option .= $category->cat_name;
			  		$option .= ' ('.$category->category_count.')';
			 	 	$option .= '</option>';
			  		echo $option;
				}
				?>
</select>
<?php 
	}

	public function meta_title_callback()
	{
			$checked = ($this->options['apd_meta_title'] == true) ? "checked='checked'" : "";
		?>
<input type="checkbox" name="apd_options[apd_meta_title]"  value="true" <?php echo $checked; ?> />						
		<?php
	}
	
	public function meta_array_callback()
	{
		?>
<select size="20" name="apd_options[apd_meta][]" multiple="multiple">
	<?php 
	$list_of_meta = $this->get_all_meta();
	
	foreach ($list_of_meta as $meta) {
		$meta = $meta[0];
		$option = '<option value="' . $meta . '" ' . ((in_array($meta, $this->selected_custom_fields)) ? 'selected="selected"' : '') . '>';
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
	
	public function content_callback()
	{
		$content = $this->options['apd_content'];
		?>
			<label for="both_content"><?php _e('Both', 'apd_auto-post-download' ); ?></label>
			<input id="both_content" type="radio" name="apd_options[apd_content]" value="both" <?php echo ($content == "both" || $content==null) ? 'checked="checked"' : ""; ?> />
			<label for="text_content"><?php _e('Content only', 'apd_auto-post-download' ); ?></label>
			<input id="text_content" type="radio" name="apd_options[apd_content]" value="text" <?php echo ($content == "text") ? 'checked="checked"' : ""; ?> />
			<label for="image_format"><?php _e('Image only', 'apd_auto-post-download' ); ?></label>
			<input id="image_format" type="radio" name="apd_options[apd_content]" value="image" <?php echo ($content == "image") ? 'checked="checked"' : ""; ?> />
			<?php 
		}
	
}

?>