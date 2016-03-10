<?php
/*
Class: Wikimedia_Term_Choose_Sidebar_Field
Description: Creates a Choose Sidebar term meta field
*/
class Wikimedia_Term_Choose_Sidebar_Field {

	private $term_id; // term ID
	private $name; // field name
	private $label; // field label
	private $taxonomies = array(); // taxonomies attached to
	private $sidebars = array(); // array of existing sidebars

	// construct & initialize the field
	public function __construct($name, $label, $taxonomies) {

		// initialize only when we have a term ID
		if (empty($_GET['tag_ID']) && empty($_POST['tag_ID'])) {
			return;
		}

		// initialize general requisites
		$tag_id = !empty($_GET['tag_ID']) ? absint( $_GET['tag_ID'] ) : absint( $_POST['tag_ID'] );
		$this->set_term_id($tag_id);
		$this->set_name($name);
		$this->set_label($label);
		$this->set_taxonomies($taxonomies);

		// hook field rendering
		foreach ($taxonomies as $taxonomy) {
			add_action( $taxonomy . '_edit_form_fields', array($this, 'render'), 15, 2);
		}

		// hook field saving
		foreach ($taxonomies as $taxonomy) {
			add_action( 'edited_' . $taxonomy, array($this, 'save'), 15, 2);
			add_action( 'created_' . $taxonomy, array($this, 'save'), 15, 2);
		}
	}

	// set the field term id
	public function set_term_id($id) {
		$this->term_id = $id;
	}

	// get the field term id
	public function get_term_id() {
		return $this->term_id;
	}

	// set the field name
	public function set_name($name) {
		$this->name = $name;
	}

	// get the field name
	public function get_name() {
		return $this->name;
	}

	// set the field label
	public function set_label($label) {
		$this->label = $label;
	}

	// get the field label
	public function get_label() {
		return $this->label;
	}

	// set the taxonomies to hook to
	public function set_taxonomies($taxonomies) {
		$this->taxonomies = $taxonomies;
	}

	// get the taxonomies to hook to
	public function get_taxonomies() {
		return $this->taxonomies;
	}

	// get the database key
	public function get_key() {
		return $this->get_name() . '_' . $this->get_term_id();
	}

	// get field value
	public function get_value() {
		return get_option($this->get_key());
	}

	public function get_sidebars() {
		global $wp_registered_sidebars;
		$sidebars = array();

		foreach ($wp_registered_sidebars as $sidebar) {
			$sidebars[] = $sidebar['name'];
		}

		$options = array_combine($sidebars, $sidebars);
		return $options;
	}

	// render the field
	public function render() {
		$sidebars = $this->get_sidebars();
		?>
		<tr class="form-field">
			<th scope="row">
				<?php echo esc_html( $this->get_label() ); ?>
			</th>
			<td>
				<select name="<?php echo esc_attr( $this->get_key() ); ?>" class="term-choose-sidebar">
					<?php foreach ($sidebars as $key => $value): ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php endforeach ?>
				</select>
			</td>
		</tr>
		<?php
	}

	// save the field
	public function save() {
		$value = isset($_POST[$this->get_key()]) ? sanitize_text_field( $_POST[$this->get_key()] ) : '';
		update_option($this->get_key(), $value);
	}
}

# Detect the current category/post categories and retrieve a custom sidebar
function wmb_custom_sidebar() {
	if( is_category() || is_single() ) {
		if( is_single() ) {
			$categories = get_the_category( get_the_ID() );
			foreach( $categories as $cat ) {
				$category = $cat;
				break;
			}
		} else {
			$category = get_queried_object();			
		}
		if(isset($category)) {
			do {
				if($sidebar = get_option('choose_sidebar_' . $category->term_id ) ) {
					return $sidebar;
				}

				$category = $category->parent ? get_category( $category->parent ) : false;
			} while( $category );
		}
	}

	return false;
}