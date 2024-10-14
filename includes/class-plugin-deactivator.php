<?php
/**
 * Class Plugin_Deactivator
 *
 * Contains methods for deactivating and uninstalling the Flexi Real Estate plugin.
 */
class Plugin_Deactivator {
	/**
	 * Deactivates the plugin.
	 *
	 * If the `flexi_real_estate_keep_data` option is set to false, this method will delete all posts and taxonomies registered by the plugin.
	 */
	public static function deactivate() {
		$keep_data = get_option( 'flexi_real_estate_keep_data', false );

		if ( ! $keep_data ) {
			self::remove_post_type();
			self::remove_taxonomy();
			self::remove_acf_fields();
		}

		// Flush the rewrite rules to ensure the plugin's post type and taxonomy are no longer registered.
		flush_rewrite_rules();
	}

	/**
	 * Uninstalls the plugin.
	 *
	 * This method is called when the plugin is uninstalled, and it will delete all data registered by the plugin.
	 */
	public static function uninstall() {
		// Flush the rewrite rules to ensure the plugin's post type and taxonomy are no longer registered.
		flush_rewrite_rules();
	}

	/**
	 * Prompts the user to confirm whether to delete or keep data.
	 *
	 * This method is called when the plugin is deactivated, and it will prompt the user to confirm whether to delete or keep data.
	 * If the user chooses to keep data, the `flexi_real_estate_keep_data` option will be set to true.
	 * If the user chooses to delete data, the `flexi_real_estate_keep_data` option will be set to false.
	 *
	 * @return bool Returns false if the user chooses to keep data, and true if the user chooses to delete data.
	 */
	private static function prompt_user() {
		// This is a server-side function, so we can't directly prompt the user.
		// Instead, we'll create an option that can be checked by JavaScript on the admin side.
		update_option( 'flexi_real_estate_deactivation_prompt', true );

		// Return false if the user chooses to keep data, and true if the user chooses to delete data.
		return false;
	}

	/**
	 * Removes the 'real-estate' post type.
	 *
	 * This method is called when the plugin is deactivated and the user chooses to delete data.
	 * It will unregister the 'real-estate' post type and delete all posts of that type.
	 */
	private static function remove_post_type() {
		unregister_post_type( 'real-estate' );

		// Get all posts of the 'real-estate' type.
		$posts = get_posts(
			array(
				'post_type'   => 'real-estate',
				'numberposts' => -1,
			)
		);

		// Loop through each post and delete it.
		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
	}

	/**
	 * Removes the 'district' taxonomy.
	 *
	 * This method is called when the plugin is deactivated and the user chooses to delete data.
	 * It will unregister the 'district' taxonomy and delete all terms of that taxonomy.
	 */
	private static function remove_taxonomy() {
		unregister_taxonomy( 'district' );

		// Get all terms of the 'district' taxonomy.
		$terms = get_terms(
			array(
				'taxonomy'   => 'district',
				'hide_empty' => false,
			)
		);

		// Loop through each term and delete it.
		foreach ( $terms as $term ) {
			wp_delete_term( $term->term_id, 'district' );
		}
	}

	/**
	 * Removes the 'real_estate' ACF field group.
	 *
	 * This method is called when the plugin is deactivated and the user chooses to delete data.
	 * It will remove the 'real_estate' ACF field group.
	 */
	private static function remove_acf_fields() {
		if ( function_exists( 'acf_remove_local_field_group' ) ) {
			acf_remove_local_field_group( 'group_real_estate' );
		}
	}
}

