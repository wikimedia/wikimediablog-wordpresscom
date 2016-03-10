<?php
# Render a text widget field
function wmb_widget_field_text($field, $instance, &$context) {
	$value = isset($instance[$field['name']]) ? $instance[$field['name']] : '';
	?>
	<p>
		<label for="<?php echo esc_attr($context->get_field_id($field['name'])); ?>"><?php echo esc_html( $field['label'] . ':' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($context->get_field_id($field['name'])); ?>" name="<?php echo esc_attr($context->get_field_name($field['name'])); ?>" type="text" value="<?php echo esc_attr($value); ?>" />
	</p>
	<?php
}

# Render a textarea widget field
function wmb_widget_field_textarea($field, $instance, &$context) {
	$value = isset($instance[$field['name']]) ? $instance[$field['name']] : '';
	?>
	<p>
		<label for="<?php echo esc_attr($context->get_field_id($field['name'])); ?>"><?php echo esc_html( $field['label'] . ':' ); ?></label> 
		<textarea class="widefat" id="<?php echo esc_attr($context->get_field_id($field['name'])); ?>" name="<?php echo esc_attr($context->get_field_name($field['name'])); ?>"><?php echo esc_html( $value ); ?></textarea>
	</p>
	<?php
}

# Render a select widget field
function wmb_widget_field_select($field, $instance, &$context) {
	$value = isset($instance[$field['name']]) ? $instance[$field['name']] : '';
	?>
	<p>
		<label for="<?php echo esc_attr($context->get_field_id($field['name'])); ?>"><?php echo esc_html( $field['label'] . ':' ); ?></label> 
		<select class="widefat" id="<?php echo esc_attr($context->get_field_id($field['name'])); ?>" name="<?php echo esc_attr($context->get_field_name($field['name'])); ?>">
			<?php foreach ($field['options'] as $optkey => $optvalue): ?>
				<option value="<?php echo esc_attr($optkey); ?>" <?php selected($value, $optkey); ?>><?php echo esc_html( $optvalue ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

# Render a set of widget fields
function wmb_widget_render_fields($fields, $instance, &$context) {
	foreach ($fields as $f) {
		if (function_exists('wmb_widget_field_' . $f['type'])) {
			call_user_func('wmb_widget_field_' . $f['type'], $f, $instance, $context);
		}
	}
}

# Render a "no fields" message
function wmb_widget_no_fields() {
	?>
	<p>There are no options for this widget.</p>
	<?php
}