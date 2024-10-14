<?php
/**
 * Real Estate Ajax Filter Shortcode function.
 *
 * This function is a WordPress shortcode that renders a Real Estate Ajax Filter Widget.
 * It checks for the existence of the widget class, creates an instance, sets widget settings,
 * and renders the widget with customizable HTML elements.
 *
 * @since 1.0.0
 *
 * @param array $atts An associative array of shortcode attributes.
 *
 * @return string|WP_Error The rendered widget output or a WP_Error object if the widget fails to render.
 */
function real_estate_ajax_filter_shortcode( $atts = array() ) {

	if ( ! class_exists( 'Real_Estate_Ajax_Filter_Widget' ) ) {
		return __( 'Real Estate Ajax Filter Widget not found', 'flexi-real-estate' );
	}

	$filter_widget = new Real_Estate_Ajax_Filter_Widget();
	if ( ! $filter_widget ) {
		return new WP_Error( 'widget_creation_failed', __( 'Failed to create widget instance', 'flexi-real-estate' ) );
	}

	$widget_settings = shortcode_atts(
		array(
			'title'     => __( 'Real Estate Filter', 'flexi-real-estate' ),
			'post_type' => 'real-estate',
		),
		$atts
	);

	ob_start();
	$filter_widget->widget(
		array(
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		),
		$widget_settings
	);

	return apply_filters( 'real_estate_ajax_filter_shortcode_output', ob_get_clean(), $widget_settings );
}

add_shortcode( 'real_estate_ajax_filter', 'real_estate_ajax_filter_shortcode' );
