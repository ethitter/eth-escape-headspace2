=== ETH Escape HeadSpace2 ===
Contributors: ethitter
Donate link: https://ethitter.com/
Tags: seo, meta tags
Requires at least: 4.4
Tested up to: 4.4
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Output existing HeadSpace2 data without the original plugin. Deactivate HeadSpace2 (no longer maintained) without impactacting legacy content.

== Description ==

Outputs data stored in HeadSpace2 post meta without requiring HeadSpace2. This allows one to remove the now-unmaintained HeadSpace2 plugin without losing the data associated with legacy content.

The HeadSpace2 plugin is no longer maintained and generates many warnings when used with PHP 7. With this in mind, and without judging the original plugin, I created this option to preserve the plugin's output for SEO purposes. The plugin *does not* allow HeadSpace2 data to be edited; it only allows search engines to continue accessing it.

Note that many current SEO plugins also handle migration from HeadSpace2, but since I don't use any such plugins on my network, I created this alternative.

For a full list of the HeadSpace2 data that's supported, see the FAQ.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/eth-escape-headspace` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Can I modify existing data using this plugin? =

No. If you need to update entries, reinstall HeadSpace2 or modify the values directly in the database.

= What HeadSpace2 data is supported? =

Currently the plugin will output:

* custom post/page titles
* meta keywords
* meta descriptions
* custom robots.txt declarations
* JavaScript includes
* Stylesheet includes
* Raw header and footer content

= Why isn't all HeadSpace2 data supported? =

Mostly, I either didn't have time or didn't have a good example of how a particular feature was used. Contributions are welcome. :)

== Changelog ==

= 0.2 =
* Add support for page titles, scripts, stylesheets, and raw header/footer content.

= 0.1 =
* Initial release
