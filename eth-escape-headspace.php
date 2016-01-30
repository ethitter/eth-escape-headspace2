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
	 * Register actions and filters
	 *
	 * @return null
	 */
	private function __construct() {
		//
	}
}

ETH_Escape_HeadSpace2::get_instance();
