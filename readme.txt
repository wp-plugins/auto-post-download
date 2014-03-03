=== Auto Post Download ===
Contributors: MicNeo
Tags: auto, download, post, attachment, presspack, mediapack, pack, custom, fields
Requires at least: 3.0.1
Tested up to: 3.8.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provide to your users easy way of downloading posts. 

== Description ==
<p>Auto Post Download plugin provides easy way to generating ZIP files with post content and post's image.</p>
<p>With this plugin you will be able to provide URL for your users to download posts as a ZIP file. You need a press pack? This plugin will make it very easy.</p>
<p>Plugin automatically generates attachment for post while you're publish it. It's totally automatic, you just need to use shortcode (or php function) to print URL.</p>
<p>Attachments are manageable via Wordpress Media Library. You can specify for which categories auto attachments should be created.</p>
<p>You can also define Custom Fields which will be included in generated attachment.</p>

== Usage ==
* Just put `[auto-post-download]` wherever you want to display URL to post's attachment.
* You can also specify post_id - `[auto-post-download postId=1]`
* You can combine shortcode with html, like: `<a href="[auto-post-download]">Download presspack</a>`
* You can also use php function call: `<?php echo apd_downloadUrl(); ?>`

== Installation ==
<p>This section describes how to install the plugin and get it working.</p>
<p>Use the automatic plugin installer of your WordPress admin, or do it manually:</p>
1. Upload the `auto-post-download` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Select desire categories under the 'Settings/Auto Post Download' menu.

== Changelog ==

= 1.1 =
* New feature: Now plugin supports fetching information from custom fields! Just select custom fields from plugin's settings and content of those custom fields will be automaticly added to attachment.
* Bug fix: Wrong check of post's category is now fixed.

= 1.0 =
* First release
