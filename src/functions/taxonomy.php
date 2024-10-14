<?php
/**
 * Retrieves a taxonomy field configuration for a filter.
 *
 * This function is used to generate a field configuration array for a specific taxonomy,
 * which can then be used to create a filter in a WordPress theme or plugin.
 *
 * @param string $taxonomy The name of the taxonomy for which to retrieve the field configuration.
 *
 * @return array An array containing the field configuration.
 *               The array contains the following keys:
 *               - 'name': The name of the taxonomy.
 *               - 'label': The label for the taxonomy field, translated to the current locale.
 *               - 'type': The type of the field, set to 'taxonomy'.
 *               - 'taxonomy': The name of the taxonomy.
 *               - 'choices': An associative array of term IDs and names, if the taxonomy terms were successfully retrieved.
 */
function get_taxonomy_for_filter( $taxonomy ) {
	if ( empty( $taxonomy ) ) {
		return;
	}

	$fields = array(
		'name'     => $taxonomy,
		'label'    => __( 'Select', 'flexi-real-estate' ),
		'type'     => 'taxonomy',
		'taxonomy' => $taxonomy,
	);

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		)
	);

	if ( ! is_wp_error( $terms ) ) {
		$fields['choices'] = wp_list_pluck( $terms, 'name', 'term_id' );
	}

	return array( $fields );
}
