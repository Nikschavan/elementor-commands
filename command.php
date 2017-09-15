<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
* WP ClI Commands for Elementor
*/
class Elementor_Commands extends WP_CLI_Command
{
	
	/**
	 * Regenerate the Elementor Page Builder CSS.
	 *
	 * [--network]
	 *      Regenerate CSS of for all the sites in the network.
	 * 
	 * ## EXAMPLES
	 *
	 *  1. wp elementor-commands regenerate-css
	 *      - This will regenerate the CSS files for elementor page builder.
	 * 
	 *  2. wp site list --field=url | xargs -n1 -I % wp --url=% elementor-commands regenerate-css
	 *  	- This will regenerate the CSS files for elementor page builder on all the sites in network.
	 * 
	 * @alias regenerate-css
	*/
	public function regenerate_css( $args, $assoc_args ) {

		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->posts_css_manager->clear_cache();
			WP_CLI::success( 'Regenerated the Elementor CSS' );
		} else {
			WP_CLI::error( 'Elementor is not installed on this site' );
		}
	}

	/**
	 * Regenerate the Elementor Page Builder CSS.
	 *
	 * [--network]
	 *      Regenerate CSS of for all the sites in the network.
	 * 
	 * ## EXAMPLES
	 *
	 *  1. wp elementor-commands search-replace <source-url> <destination-url>
	 *      - This will Replace the URLs from <source-url> to <destination-url>.
	 * 
	 *  2. wp site list --field=url | xargs -n1 -I % wp --url=% elementor-commands search-replace <source-url> <destination-url>
	 *  	- This will Replace the URLs from <source-url> to <destination-url> on all the sites in network.
	 * 
	 * @alias search-replace
	*/
	public function search_replace( $args, $assoc_args ) {

		if ( isset( $args[0] ) ) {
			$from = $args[0];
		}
		if ( isset( $args[1] ) ) {
			$to = $args[1];
		}

		$is_valid_urls = ( filter_var( $from, FILTER_VALIDATE_URL ) && filter_var( $to, FILTER_VALIDATE_URL ) );
		if ( ! $is_valid_urls ) {
			WP_CLI::error( __( 'The `from` and `to` URL\'s must be a valid URL', 'elementor' ) );
		}

		if ( $from === $to ) {
			WP_CLI::error( __( 'The `from` and `to` URL\'s must be different', 'elementor' ) );
		}


		global $wpdb;

		// @codingStandardsIgnoreStart cannot use `$wpdb->prepare` because it remove's the backslashes
		$rows_affected = $wpdb->query(
			"UPDATE {$wpdb->postmeta} " .
			"SET `meta_value` = REPLACE(`meta_value`, '" . str_replace( '/', '\\\/', $from ) . "', '" . str_replace( '/', '\\\/', $to ) . "') " .
			"WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE '[%' ;" ); // meta_value LIKE '[%' are json formatted
		// @codingStandardsIgnoreEnd
		
		WP_CLI::success( 'Replaced URLs for elementor' );
	}
}


WP_CLI::add_command( 'elementor-commands', 'Elementor_Commands' );
