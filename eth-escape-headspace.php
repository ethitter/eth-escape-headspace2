<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
  Plugin Name: ETH Escape HeadSpace2
  Plugin URI: https://ethitter.com/plugins/
  Description: Output existing HeadSpace2 data without the original plugin. Allows HeadSpace2 (no longer maintained) to be deactivated without impactacting legacy content.
  Author: Erick Hitter
  Version: 0.2.1
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

/**
 * Class ETH_Escape_HeadSpace2.
 */
class ETH_Escape_HeadSpace2 {
	/**
	 * Singleton
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Instantiate singleton
	 */
	public static function get_instance() {
		if ( ! is_a( self::$instance, __CLASS__ ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Headspace's string keys.
	 *
	 * @var array
	 */
	private $hs_string_keys = array(
		'_headspace_description',
		'_headspace_metakey',
		'_headspace_raw',
	);

	/**
	 * Headspace's array keys.
	 *
	 * @var array
	 */
	private $hs_array_keys = array(
		'_headspace_scripts',
		'_headspace_stylesheets',
	);

	/**
	 * Headspace's robots.txt keys.
	 *
	 * @var array
	 */
	private $hs_robots_keys = array(
		'_headspace_noindex',
		'_headspace_nofollow',
		'_headspace_noarchive',
		'_headspace_noodp',
		'_headspace_noydir',
	);

	/**
	 * Map Headspace keys.
	 *
	 * @var array
	 */
	private $hs_keys_to_meta_names = array(
		'_headspace_description' => 'description',
		'_headspace_metakey'     => 'keywords',
	);

	/**
	 * Defer plugin hook additions until all plugins are loaded
	 * Allows plugin to defer to HeadSpace2 when active
	 *
	 * @return null
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'maybe_add_hooks' ) );
	}

	/**
	 * Conditionally register plugin's hooks
	 */
	public function maybe_add_hooks() {
		// Defer to HeadSpace2 when active.
		if ( class_exists( 'HeadSpace_Plugin' ) ) {
			return;
		}

		add_filter( 'pre_get_document_title', array( $this, 'filter_pre_get_document_title' ) );
		add_filter( 'wp_title', array( $this, 'filter_wp_title' ), 10, 3 );

		add_action( 'wp_head', array( $this, 'action_wp_head' ) );
		add_action( 'wp_footer', array( $this, 'action_wp_footer' ) );
	}

	/**
	 * Filter page titles in WP 4.1+ themes add_theme_support( 'title-tag' ).
	 *
	 * @param string $title Page title.
	 * @return string
	 */
	public function filter_pre_get_document_title( $title ) {
		$_title = get_post_meta( get_the_ID(), '_headspace_page_title', true );

		if ( ! empty( $_title ) ) {
			$title = esc_html( $_title );
		}

		unset( $_title );

		return $title;
	}

	/**
	 * Filter page titles in themes designed for < WP 4.1 wp_title().
	 *
	 * @param string $title Object title.
	 * @param string $sep   Title separator.
	 * @param string $loc   Separator location.
	 * @return string
	 */
	public function filter_wp_title( $title, $sep, $loc ) {
		$_title = get_post_meta( get_the_ID(), '_headspace_page_title', true );

		if ( ! empty( $_title ) ) {
			$_title = esc_html( $_title );

			if ( 'right' === $loc ) {
				$title = $_title . ' ' . $sep . ' ';
			} else {
				$title = ' ' . $sep . ' ' . $_title;
			}
		}

		unset( $_title );

		return $title;
	}

	/**
	 * Add <head> meta tags
	 */
	public function action_wp_head() {
		// Applies only to individual post objects.
		if ( ! is_singular() ) {
			return;
		}

		// Check for HS data.
		$hs_data = array();

		// Keys that only exist once per post.
		foreach ( array_merge( $this->hs_string_keys, $this->hs_robots_keys ) as $hs_key ) {
			$value = get_post_meta( get_the_ID(), $hs_key, true );

			if ( ! empty( $value ) ) {
				$hs_data[ $hs_key ] = $value;
			}
		}

		// Keys that can exist multiple times per post.
		foreach ( $this->hs_array_keys as $hs_key ) {
			$values = get_post_meta( get_the_ID(), $hs_key, false );

			if ( ! empty( $values ) ) {
				$hs_data[ $hs_key ] = $values;
			}
		}

		// Bail if no HS data exists for this post.
		if ( empty( $hs_data ) ) {
			return;
		}

		// Handle basic, string-containing keys.
		$output = array();

		foreach ( $hs_data as $hs_key => $hs_value ) {
			switch ( $hs_key ) {
				case '_headspace_description':
				case '_headspace_metakey':
					$output[] = '<meta name="' . esc_attr( $this->hs_keys_to_meta_names[ $hs_key ] ) . '" content="' . esc_attr( $hs_value ) . '" />';
					break;

				case '_headspace_scripts':
					foreach ( $hs_value as $_source ) {
						$output[] = '<script type="text/javascript" src="' . esc_url( $_source ) . '"></script>';
					}
					break;

				case '_headspace_stylesheets':
					foreach ( $hs_value as $_source ) {
						$output[] = '<link rel="stylesheet" href="' . esc_url( $_source ) . '" type="text/css" />';
					}
					break;

				default:
					continue 2;
					break;
			}
		}

		// Handle robots key, which is build from several meta keys.
		$robots = array();

		foreach ( $this->hs_robots_keys as $hs_robot_key ) {
			if ( isset( $hs_data[ $hs_robot_key ] ) ) {
				$robots[] = str_replace( '_headspace_', '', $hs_robot_key );
			}
		}

		if ( ! empty( $robots ) ) {
			if ( 1 === count( $robots ) && in_array( 'noindex', $robots ) ) {
				$robots[] = 'follow';
			}

			$robots = implode( ',', $robots );

			$output[] = '<meta name="robots" content="' . esc_attr( $robots ) . '" />' . "\n";
		}

		// Raw output should follow all other output.
		if ( ! empty( $hs_data['_headspace_raw'] ) ) {
			$output[] = $hs_data['_headspace_raw'];
		}

		// Output whatever we've built.
		if ( ! empty( $output ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo "\n<!-- Escape HeadSpace2 by Erick Hitter; https://ethitter.com/plugins/ -->\n" . implode( "\n", $output ) . "\n<!-- Escape HeadSpace2 -->\n";
		}
	}

	/**
	 * Add custom footer content
	 */
	public function action_wp_footer() {
		$output = get_post_meta( get_the_ID(), '_headspace_raw_footer', true );

		if ( ! empty( $output ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $output . "\n";
		}
	}
}

ETH_Escape_HeadSpace2::get_instance();
