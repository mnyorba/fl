<?php
/**
 * Class District_Taxonomy
 *
 * Handles the registration of the District taxonomy.
 *
 * @package Flexi Real Estate
 */
class District_Taxonomy {
	use Singleton;

	/**
	 * District_Taxonomy constructor.
	 *
	 * Registers the district taxonomy.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->register_taxonomy();
	}

	/**
	 * Registers the district taxonomy.
	 *
	 * @since 1.0.0
	 */
	protected function register_taxonomy() {
		$args = array(
			'labels'            => array(
				'name'          => __( 'Districts', 'flexi-real-estate' ),
				'singular_name' => __( 'District', 'flexi-real-estate' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
		);

		register_taxonomy( 'district', 'real-estate', $args );
	}
}
