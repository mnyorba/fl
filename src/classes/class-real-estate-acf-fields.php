<?php
/**
 * Class Real_Estate_ACF_Fields
 *
 * Registers ACF fields for the `real-estate` post type.
 *
 * @package Flexi Real Estate
 */
class Real_Estate_ACF_Fields {
	use Singleton;

	/**
	 * Constructor
	 *
	 * Registers the ACF fields.
	 */
	protected function __construct() {
		self::register_acf_fields();
	}

	/**
	 * Registers the ACF fields.
	 */
	public function register_acf_fields() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			$group_title = __( 'Real Estate Object Details', 'flexi-real-estate' );
			acf_add_local_field_group(
				array(
					'key'      => 'group_real_estate_object',
					'title'    => $group_title,
					'fields'   => array(
						array(
							'key'      => 'field_house_name',
							'label'    => __( 'House Name', 'flexi-real-estate' ),
							'name'     => 'house_name',
							'type'     => 'text',
							'required' => 1,
						),
						array(
							'key'   => 'field_location_coordinates',
							'label' => __( 'Location Coordinates', 'flexi-real-estate' ),
							'name'  => 'location_coordinates',
							'type'  => 'text',
						),
						array(
							'key'      => 'field_number_of_floors',
							'label'    => __( 'Number of Floors', 'flexi-real-estate' ),
							'name'     => 'number_of_floors',
							'type'     => 'select',
							'choices'  => array_merge(
								array( '0' => __( 'No selected', 'flexi-real-estate' ) ),
								array_combine( range( 1, 20 ), range( 1, 20 ) ),
							),
							'required' => 1,
						),
						array(
							'key'      => 'field_building_type',
							'label'    => __( 'Building Type', 'flexi-real-estate' ),
							'name'     => 'building_type',
							'type'     => 'radio',
							'choices'  => array(
								'panel'      => __( 'Panel', 'flexi-real-estate' ),
								'brick'      => __( 'Brick', 'flexi-real-estate' ),
								'foam_block' => __( 'Foam Block', 'flexi-real-estate' ),
							),
							'required' => 1,
						),
						array(
							'key'     => 'field_environmental_friendliness',
							'label'   => __( 'Environmental Friendliness', 'flexi-real-estate' ),
							'name'    => 'environmental_friendliness',
							'type'    => 'select',
							'choices' => array_merge(
								array( '0' => __( 'No selected', 'flexi-real-estate' ) ),
								array_combine( range( 1, 5 ), range( 1, 5 ) ),
							),
						),
						array(
							'key'   => 'field_image',
							'label' => __( 'Image', 'flexi-real-estate' ),
							'name'  => 'image',
							'type'  => 'image',
						),
						array(
							'key'        => 'field_apartments',
							'label'      => __( 'Apartments', 'flexi-real-estate' ),
							'name'       => 'apartments',
							'type'       => 'repeater',
							'sub_fields' => array(
								array(
									'key'      => 'field_apartment_area',
									'label'    => __( 'Area', 'flexi-real-estate' ),
									'name'     => 'area',
									'type'     => 'text',
									'required' => 1,
								),
								array(
									'key'      => 'field_number_of_rooms',
									'label'    => __( 'Number of Rooms', 'flexi-real-estate' ),
									'name'     => 'number_of_rooms',
									'type'     => 'radio',
									'choices'  => array_combine( range( 1, 10 ), range( 1, 10 ) ),
									'required' => 1,
								),
								array(
									'key'      => 'field_apartment_balcony',
									'label'    => __( 'Balcony', 'flexi-real-estate' ),
									'name'     => 'balcony',
									'type'     => 'radio',
									'choices'  => array(
										'yes' => __( 'Yes', 'flexi-real-estate' ),
										'no'  => __( 'No', 'flexi-real-estate' ),
									),
									'required' => 1,
								),
								array(
									'key'      => 'field_apartment_bathroom',
									'label'    => __( 'Bathroom', 'flexi-real-estate' ),
									'name'     => 'bathroom',
									'type'     => 'radio',
									'choices'  => array(
										'yes' => __( 'Yes', 'flexi-real-estate' ),
										'no'  => __( 'No', 'flexi-real-estate' ),
									),
									'required' => 1,
								),
								array(
									'key'   => 'field_apartment_image',
									'label' => __( 'Room Image', 'flexi-real-estate' ),
									'name'  => 'apartment_image',
									'type'  => 'image',
								),
							),
						),
					),
					'location' => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'real-estate',
							),
						),
					),
				)
			);
		}
	}
}
