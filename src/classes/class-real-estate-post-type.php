<?php
/**
 * Class Real_Estate_Post_Type
 *
 * Class for the post type "real-estate".
 *
 * @package FlexiRealEstate\Core\Classes
 */
class Real_Estate_Post_Type extends Abstract_Post_Type {

	/**
	 * Set the post type name.
	 */
	protected function set_post_type() {
		$this->post_type = 'real-estate';
	}

	/**
	 * Set the arguments for the post type.
	 */
	protected function set_args() {
		$this->args = array(
			'labels'      => array(
				'name'          => __( 'Real Estate', 'flexi-real-estate' ),
				'singular_name' => __( 'Real Estate Item', 'flexi-real-estate' ),
			),
			'public'      => true,
			'has_archive' => true,
			'supports'    => array( 'title', 'thumbnail', 'custom-fields' ),
			'menu_icon'   => 'dashicons-building',
		);
	}

	/**
	 * Order the posts in the archive by meta value.
	 *
	 * @param WP_Query $query The WP_Query object.
	 * @param string   $meta_key The meta key to order by.
	 * @param string   $orderby The orderby parameter.
	 * @param string   $order The order parameter.
	 */
	public function order_by_real_estate( $query, $meta_key = 'environmental_friendliness', $orderby = 'meta_value_num', $order = 'ASC' ) {
		if ( ! $query->is_main_query() && $query->is_post_type_archive( 'real-estate' ) ) {
			$query->set( 'meta_key', $meta_key );
			$query->set( 'orderby', $orderby );
			$query->set( 'order', $order ); // or 'ASC' depending on your needs
		}
	}
}
