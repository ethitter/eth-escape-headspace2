<?php
/*
Plugin Name: ETH Escape HeadSpace2
Plugin URI: https://ethitter.com/plugins/
Description: Output existing HeadSpace2 data without the original plugin. Allows HeadSpace2 (no longer maintained) to be deactivated without impactacting legacy content.
Author: Erick Hitter
Version: 0.1
Author URI: https://ethitter.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class ETH_Escape_HeadSpace2 {
	/**
	 * Singleton
	 */
	private static $instance = null;

	/**
	 * Instantiate singleton
	 */
	public static function get_instance() {
		if ( ! is_a( self::$instance, __CLASS__ ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Class properties
	 */
	private $hs_string_keys = array(
		'_headspace_description',
		'_headspace_metakey',
	);

	private $hs_robots_keys = array(
		'_headspace_noindex',
		'_headspace_nofollow',
		'_headspace_noarchive',
		'_headspace_noodp',
		'_headspace_noydir',
	);

	private $hs_keys_to_meta_names = array(
		'_headspace_description' => 'description',
		'_headspace_metakey'     => 'keywords',
	);

	/**
	 * Register plugin's hooks
	 *
	 * @return null
	 */
	private function __construct() {
		add_action( 'wp_head', array( $this, 'action_wp_head' ) );
	}

	/**
	 *
	 */
	public function action_wp_head() {
		// Applies only to individual post objects
		if ( ! is_singular() ) {
			return;
		}

		// Check for HS data
		$hs_data = array();

		foreach ( array_merge( $this->hs_string_keys, $this->hs_robots_keys ) as $hs_key ) {
			$value = get_post_meta( get_the_ID(), $hs_key, true );

			if ( ! empty( $value ) ) {
				$hs_data[ $hs_key ] = $value;
			}
		}

		// Bail if no HS data exists for this post
		if ( empty( $hs_data ) ) {
			return;
		}

		// Build output
		echo "\n<!-- Escape HeadSpace2 by Erick Hitter; ethitter.com -->\n";

		// Handle basic, string-containing keys
		foreach ( $hs_data as $hs_key => $hs_value ) {
			switch( $hs_key ) {
				case '_headspace_description' :
				case '_headspace_metakey' :
					echo '<meta name="' . esc_attr( $this->hs_keys_to_meta_names[ $hs_key ] ) . '" content="' . esc_attr( $hs_value ) . '" />' . "\n";
					break;

				default :
					continue;
					break;
			}
		}

		// Handle robots key, which is build from several meta keys
		$robots = array();

		foreach ( $this->hs_robots_keys as $hs_robot_key ) {
			if ( isset( $hs_data[ $hs_robot_key] ) ) {
				$robots[] = str_replace( '_headspace_', '', $hs_robot_key );
			}
		}

		if ( ! empty( $robots ) ) {
			if ( 1 === count( $robots ) && in_array( 'noindex', $robots ) ) {
				$robots[] = 'follow';
			}

			$robots = implode( ',', $robots );

			echo '<meta name="robots" content="' . esc_attr( $robots ) . '" />' . "\n";
		}

		// Mark end of output
		echo "<!-- Escape HeadSpace2 -->\n";
	}
}

ETH_Escape_HeadSpace2::get_instance();
