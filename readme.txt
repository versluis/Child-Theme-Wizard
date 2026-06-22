=== Child Theme Wizard ===
Contributors: versluis
Donate link: https://patreon.com/versluis
Tags: child theme, generator, creator, one click, starter
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Creates a child theme from any installed parent theme with just one click.

== Description ==

The Child Theme Wizard lets you create a new child theme without the need for additional tools, right from within the WordPress admin interface. Once activated you can find it under Tools - Child Theme Wizard.

Specify a parent theme, customise options such as title and description and click Create Child Theme. Upon success you will find your new theme under Appearance - Themes.

You have the option to include GPL License Terms if you wish. The Wizard will automatically create a thumbnail too.

To find out more about child themes and why they are useful, please read the [WordPress Developer Docs on Child Themes](https://developer.wordpress.org/themes/advanced-topics/child-themes/).

== Installation ==

1. Either: Upload the entire folder `child-theme-wizard` to the `/wp-content/plugins/` directory.
1. Or: download the ZIP file, then head over to Plugins - Add New - Upload Plugin, then browse to your file.
1. Or: from Plugins - Add New, search for "child theme wizard" to find this plugin and hit Install.
1. Then: Activate the plugin through the Plugins menu in WordPress.
1. You can find it under Tools - Child Theme Wizard.

== Screenshots ==

1. Create a Child Theme with just one click.
2. The wizard completed successfully.
3. Your new child theme appears under Appearance - Themes with a thumbnail.

== Changelog ==

= 1.5 =
* Security: added CSRF nonce protection to the creation form
* Security: parent theme selection now validated server-side against installed themes
* Security: all HTML output is now properly escaped
* Fixed: incorrect admin URL argument that broke the "Try again" link
* Fixed: missing closing </form> tag
* Fixed: thumbnail copy now uses the filesystem path instead of an HTTP URL, fixing failures on hosts with allow_url_fopen disabled
* Fixed: uninstall.php guard order corrected per WordPress standards
* Replaced direct PHP file functions (fopen, fwrite, fclose, mkdir, copy) with WP_Filesystem API
* Added PHP 8.2 return type declarations
* Removed unused ctw_testing() function
* Form layout updated to use WordPress standard form-table markup
* Tested with WordPress 7.0

= 1.4 =
* Defined the previously undefined variable $parent_style (thanks, Marcin!)
* Updated links to WordPress Dev Docs

= 1.3 =
* Tested compatibility with WordPress 5.1
* Updated link to Codex
* Added version query as suggested in Codex as of 2019

= 1.2 =
* Corrected a spelling mistake (thanks, Cory!)

= 1.1 =
* The parent theme stylesheet is now enqueued via functions.php

= 1.0 =
* Initial release
