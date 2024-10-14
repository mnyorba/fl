<?php

/**
 * Class Real_Estate_Ajax_Filter_Widget
 *
 * AJAX filter widget for real estate listings.
 *
 * @package Flexi Real Estate
 */
class Real_Estate_Ajax_Filter_Widget extends \WP_Widget {

	use Singleton;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'real_estate_ajax_filter_widget',
			__( 'Real Estate AJAX Filter', 'flexi-real-estate' ),
			array( 'description' => __( 'AJAX filter for real estate listings', 'flexi-real-estate' ) )
		);
	}

	/**
	 * Outputs the widget based on the arguments and instance.
	 *
	 * @param array $args     The widget arguments.
	 * @param array $instance The widget instance.
	 */
	public function widget( $args, $instance ) {
		$title                 = apply_filters( 'widget_title', $instance['title'] );
		$instance['post_type'] = ! empty( $instance['post_type'] ) ? $instance['post_type'] : 'real-estate';
		$instance['fields']    = ! empty( $instance['fields'] ) ? $instance['fields'] : $this->get_filter_fields( $instance['post_type'] );

		echo $args['before_widget'];

		$fields = $instance['fields'];
		?>
		<div class="ajax-filter-widget bg-light my-3 py-3">
		<div class="container">
			<div class="row">
				<div class="col-12 my-3">
					<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseWidgetFilter" role="button" aria-expanded="false" aria-controls="collapseWidgetFilter">
						<?php _e( 'Real Estate AJAX Filter', 'flexi-real-estate' ); ?>
					</a>
				</div>
			</div>

			<div class="collapse" id="collapseWidgetFilter">
				<div class="row">
					<h2 class="col-12 text-center"><?php echo apply_filters( 'widget_title', $instance['title'] ); ?></h2>
					<div class="col-12 col-md-5 col-lg-4 col-xl-3">
						<form id="real-estate-filter-form">
							<?php foreach ( $fields as $field ) : ?>
								<?php echo get_form_element_by_type( $field['type'], $field['name'], $field ); ?>
							<?php endforeach; ?>
							<button type="submit" class="btn btn-primary"><?php _e( 'Apply', 'flexi-real-estate' ); ?></button>
						</form>
					</div>
					<div class="col-12 col-md-7 col-lg-8 col-xl-9">
						<div id="real-estate-filter-results"></div>
					</div>
				</div>
			</div>
			</div>
		</div>
		<?php

		echo $args['after_widget'];

		$this->enqueue_scripts();
	}

	/**
	 * Updates the widget with new instance data.
	 *
	 * @param array $new_instance The new instance data.
	 * @param array $old_instance The old instance data.
	 *
	 * @return array The updated instance data.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

	/**
	 * Gets the filter fields for the widget.
	 *
	 * @param string $post_type The post type to get the fields for.
	 *
	 * @return array The filter fields.
	 */
	private function get_filter_fields( $post_type ) {
		static $fields;
		if ( null === $fields ) {
			$fields = array_merge(
				get_taxonomy_for_filter( 'district' ) ?? array(),
				get_post_type_custom_fields( array( 'post_type' => $post_type ) )
			);
		}
		return $fields;
	}

	/**
	 * Enqueues the scripts for the widget.
	 */
	private function enqueue_scripts() {
		wp_enqueue_script( 'real-estate-ajax-filter', FLEXIREALE_PLUGIN_URL . 'assets/js/real-estate-ajax-filter.js', array( 'jquery' ), '1.0', true );
		wp_localize_script(
			'real-estate-ajax-filter',
			'realEstateAjaxFilter',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'real_estate_filter_nonce' ),
			)
		);
	}
}

/**
 * Registers the Real Estate Ajax Filter widget.
 *
 * Registers the Real Estate Ajax Filter widget to be used in the WordPress admin.
 */
function register_real_estate_ajax_filter_widget() {
	register_widget( 'Real_Estate_Ajax_Filter_Widget' );
}
add_action( 'widgets_init', 'register_real_estate_ajax_filter_widget' );

/**
 * Handles the AJAX request for the Real Estate filter widget.
 *
 * This function will handle the AJAX request for the Real Estate filter widget
 * and will return the filtered results in JSON format.
 *
 * @return void
 */
function real_estate_ajax_filter_handler() {
	check_ajax_referer( 'real_estate_filter_nonce', 'nonce' );

	/**
	 * Filters the posts by the meta key for the 'apartments' field.
	 *
	 * @param string $where The SQL WHERE clause.
	 *
	 * @return string The filtered WHERE clause.
	 */
	function my_posts_where( $where ) {
		$where = str_replace( "meta_key = 'apartments_$", "meta_key LIKE 'apartments_%", $where );
		return $where;
	}
	add_filter( 'posts_where', 'my_posts_where' );

	$paged = $_POST['pagination-paged'] ? intval( $_POST['pagination-paged'] ) : 1;
	$args  = array(
		'post_type'      => 'real-estate',
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'paged'          => $paged,
		'meta_query'     => array(),
	);

	foreach ( $_POST as $key => $value ) {
		if ( ! empty( $value ) && $key !== 'action' && $key !== 'nonce' && ! str_starts_with( $key, 'pagination' ) ) {
			$value          = sanitize_text_field( $value );
			$compare        = 'LIKE';
			$type           = 'CHAR';
			$relation       = 'AND';
			$taxonomies     = get_taxonomy_for_filter( 'district' );
			$all_taxonomies = array_keys( $taxonomies[0]['choices'] );

			switch ( $key ) {
				case 'district':
					$all_taxonomies   = array();
					$all_taxonomies[] = intval( $value );
					break;
				case 'house_name':
				case 'building_type':
				case 'location_coordinates':
				case 'balcony':
				case 'bathroom':
					$compare = 'LIKE';
					break;
				case 'number_of_floors':
				case 'environmental_friendliness':
				case 'number_of_rooms':
					$value   = intval( $value );
					$compare = '=';
					$type    = 'NUMERIC';
					break;
				default:
					break;
			}

			if ( in_array( $key, array( 'number_of_rooms', 'balcony', 'bathroom' ) ) ) {
				$key = "apartments_$$key";
			}

			$args['tax_query'][] = array(
				'taxonomy' => 'district',
				'field'    => 'id',
				'terms'    => $all_taxonomies,
			);
			if ( 'district' != $key ) {
				$args['meta_query'][] =
				array(
					'relation' => $relation,
					'key'      => $key,
					'value'    => $value,
					'compare'  => $compare,
					'type'     => $type,
				);
			}
		}
	}

	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		?>
		<div>
			<p>
				<?php
				$count_posts = $query->found_posts;
					/* translators: %s: The number of found posts. */
					printf(
						esc_html__(
							/* translators: 1: The number of found posts. */
							_nx( 'Found %s real estate.', 'Found %s real estate.', number_format_i18n( $count_posts ), 'founds title', 'flexi-real-estate' )
						),
						number_format_i18n( $count_posts )
					);
				?>
			</p>
		</div>
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'loop-templates/content', 'real-estate' );
		}
	} else {
		echo '<p>' . __( 'No real estate listings found.', 'flexi-real-estate' ) . '</p>';
	}
	// Display the pagination component.
	$pagination_args         = array(
		'total'   => $query->max_num_pages,
		'current' => $paged,
	);
	$pagination_args['base'] = user_trailingslashit( '/%#%' );
	?>
	<div class="d-flex flex-column align-items-center" id="front-widget-pagination">
		<?php understrap_pagination( $pagination_args ); ?>
	</div>
	<?php
	$output = ob_get_clean();

	wp_reset_postdata();

	wp_send_json_success( $output );
}

/**
 * Handles the AJAX request for filtering the real estate listings.
 *
 * @since 1.0.0
 */
add_action( 'wp_ajax_real_estate_ajax_filter', 'real_estate_ajax_filter_handler' );

/**
 * Handles the AJAX request for filtering the real estate listings when not logged in.
 *
 * @since 1.0.0
 */
add_action( 'wp_ajax_nopriv_real_estate_ajax_filter', 'real_estate_ajax_filter_handler' );

