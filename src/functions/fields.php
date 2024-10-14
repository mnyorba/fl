<?php

/**
 * Retrieves an array of custom fields for specified post types.
 *
 * This function uses Advanced Custom Fields (ACF) to fetch custom field information
 * for the given post types. It processes each field group and extracts relevant
 * details for each custom field.
 *
 * @param array $field_groups {
 *   Optional. An array of field group parameters.
 *
 *   @type string $post_type Comma-separated list of post types to retrieve custom fields for.
 *                           Default is 'post, page'.
 * }
 * @return array An array of custom field information. Each element contains:
 *             - name: The name of the custom field.
 *             - label: The label of the custom field.
 *             - type: The type of the custom field.
 *             - choices: Available choices for select, radio, etc. fields (if applicable).
 *             - min: Minimum value for number fields (if applicable).
 *             - max: Maximum value for number fields (if applicable).
 *             - step: Step value for number fields (if applicable).
 *             - sub_fields: Sub-fields for repeater fields (if applicable).
 */
function get_post_type_custom_fields( $field_groups = array( 'post_type' => 'post, page' ) ) {

	$field_groups = acf_get_field_groups( $field_groups );

	if ( empty( $field_groups ) ) {
		return array();
	}

		$custom_fields = array();

	foreach ( $field_groups as $field_group ) {
		$fields = acf_get_fields( $field_group );
		foreach ( $fields as $field ) {
			$custom_fields[] = array(
				'name'       => $field['name'],
				'label'      => $field['label'],
				'type'       => $field['type'],
				'choices'    => $field['choices'] ?? null,
				'min'        => $field['min'] ?? null,
				'max'        => $field['max'] ?? null,
				'step'       => $field['step'] ?? null,
				'sub_fields' => $field['sub_fields'] ?? null,
			);
		}
	}

		return $custom_fields;
}

/**
 * Generates an HTML form element based on the specified type.
 *
 * This function creates various form elements such as text inputs, radio buttons,
 * range sliders, select dropdowns, and taxonomy selects. It also handles repeater fields.
 *
 * @param string $type The type of form element to generate (e.g., 'text', 'radio', 'range', 'select', 'taxonomy', 'repeater').
 * @param string $name The name attribute for the form element.
 * @param array  $args Additional arguments for the form element. May include:
 *                   - 'label': The label text for the form element.
 *                   - 'value': The current value of the form element.
 *                   - 'choices': An array of options for radio, select, or taxonomy elements.
 *                   - 'min': The minimum value for range inputs.
 *                   - 'max': The maximum value for range inputs.
 *                   - 'sub_fields': An array of sub-fields for repeater elements.
 *
 * @return string The generated HTML for the form element.
 */
function get_form_element_by_type( $type, $name, $args = array() ): string {
	$element       = '';
	$args['value'] = isset( $args['value'] ) ? $args['value'] : '';
	switch ( $type ) {
		case 'text':
			$element  = '<div class="form-group">';
			$element .= '<label class="form-label" for="' . esc_attr( $name ) . '">' . esc_html( $args['label'] ) . '</label>';
			$element .= '<input id="' . esc_attr( $name ) . '" type="text" name="' . esc_attr( $name ) . '" value="' . esc_attr( $args['value'] ) . '" class="form-control" />';
			$element .= '</div>';
			break;
		case 'radio':
			$element  = '<div class="form-group">';
			$element .= '<label class="form-label" for="' . esc_attr( $name ) . '">' . esc_html( $args['label'] ) . '</label>';
			$element .= '<div id="' . esc_attr( $name ) . '" class="form-group input-group">';
			foreach ( $args['choices'] as $key => $value ) {
				$element .= '<div class="form-check form-check-inline">';
				$element .= '<input type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $name . '-' . $key ) . '" value="' . esc_attr( $key ) . '" class="form-check-input" ' . ( $args['value'] == $key ? 'checked' : '' ) . '>' .
				'<label class="form-check-label" for="' . esc_attr( $name . '-' . $key ) . '">' . esc_html( $value ) . '</label>';
				$element .= '</div>';
			}
			$element .= '</div>';
			$element .= '</div>';
			break;
		case 'range':
			$args['value'] = ! empty( $args['value'] ) ? $args['value'] : __( 'No selected', 'flexi-real-estate' );
			$element       = '<div class="form-group">
				<label for="' . esc_attr( $name ) . '" class="form-label">' . esc_html( $args['label'] ) . ': </label>
				<output id="' . esc_attr( $name ) . '_out">' . esc_attr( $args['value'] ) . '</output>
				<input type="range" name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" value="' . esc_attr( $args['value'] ) . '" min="' . esc_attr( $args['min'] ) . '" max="' . esc_attr( $args['max'] ) . '" class="form-range d-flex" list="markers" oninput="' . esc_attr( $name ) . '_out.value = this.value" />
				</div>';
				$element  .= '<datalist id="markers">';
			for ( $i = 1; $i <= 5; $i++ ) {
				$element .= '<option value="' . $i . '">' . $i . '</option>';
			}
				$element .= '</datalist>';
			break;
		case 'select':
			$element = '<div class="form-group">
					<label for="' . esc_attr( $name ) . '" class="form-label">' . esc_html( $args['label'] ) . '</label>
					<select name="' . esc_attr( $name ) . '" class="form-select">';
			foreach ( $args['choices'] as $key => $value ) {
				$element .= '<option value="' . esc_attr( $key ) . '" ' . ( $args['value'] == $key ? 'selected' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
			$element .= '</select>
				</div>';
			break;
		case 'taxonomy':
			$element          = '<div class="form-group">
					<label for="' . esc_attr( $name ) . '" class="form-label">' . esc_html( $args['label'] ) . '</label>
					<select name="' . esc_attr( $name ) . '" class="form-select">';
					$element .= '<option value="">' . __( 'Select', 'flexi-real-estate' ) . '</option>';
			foreach ( $args['choices'] as $key => $value ) {
				$element .= '<option value="' . esc_attr( $key ) . '" ' . ( $args['value'] == $key ? 'selected' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
			$element .= '</select>
				</div>';
			break;
		case 'repeater':
			$element  = '<hr>';
			$element .= __( 'Repeater fields', 'flexi-real-estate' );
			if ( isset( $args['sub_fields'] ) && is_array( $args['sub_fields'] ) ) {
				foreach ( $args['sub_fields'] as $key => $value ) {
					$element .= get_form_element_by_type( $value['type'], $value['name'], $value );
				}
			}
			break;
	}
	return $element;
}
