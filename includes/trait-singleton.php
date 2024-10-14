<?php
/**
 * Provides a Singleton pattern implementation.
 *
 * The Singleton pattern ensures that a class has only one instance and provides a global point of access to it. This trait can be used to implement the Singleton pattern in any class that needs to have a single instance.
 *
 * @since 1.0.0
 */
trait Singleton {
	/**
	 * Stores the instances of the classes using this trait.
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * Gets the instance of the class using this trait.
	 *
	 * If the instance does not exist, it will be created.
	 *
	 * @return static
	 */
	public static function get_instance() {
		$class = get_called_class();
		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new static();
		}
		return self::$instances[ $class ];
	}

	/**
	 * Prevent instantiation.
	 */
	protected function __construct() {}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}
}
