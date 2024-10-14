<?php
/**
 * Abstract class for registering post types.
 *
 * This class provides a basic implementation for registering a post type.
 * It uses the Singleton pattern to ensure that only one instance of the class is created.
 * The class has two abstract methods, set_post_type() and set_args(), which must be implemented by child classes.
 * The register_post_type() method registers the post type with WordPress.
 *
 * @package Flexi Real Estate
 */
abstract class Abstract_Post_Type {
	use Singleton;

	/**
	 * The post type name.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * The post type arguments.
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * Constructor.
	 *
	 * Registers the post type with WordPress.
	 */
	protected function __construct() {
		$this->register_post_type();
	}

	/**
	 * Sets the post type name.
	 *
	 * Must be implemented by child classes.
	 */
	abstract protected function set_post_type();

	/**
	 * Sets the post type arguments.
	 *
	 * Must be implemented by child classes.
	 */
	abstract protected function set_args();

	/**
	 * Registers the post type with WordPress.
	 */
	protected function register_post_type() {
		$this->set_post_type();
		$this->set_args();
		register_post_type( $this->post_type, $this->args );
	}
}

