=== WP Cron per Action ===
Contributors:       Michael Uno, miunosoft
Donate link:        http://en.michaeluno.jp/donate
Tags:               cron, crons, WP Cron, cron job, cron jobs, tool, tools, optimization
Requires at least:  3.4
Tested up to:       4.6.1
Stable tag:         1.0.0
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

Ensures that `wp-cron.php` is loaded per executing action to avoid exceeding the PHP's maximum execution time.

== Description ==
If there is an action scheduled for WP Cron with a heavy routine which consumes time, the rest loaded due actions will have less remained time and more chances to reach the PHP's maximum execution time.

If you constantly keep getting the PHP error running out of execution time with scheduled actions, try this plugin and see if the problem goes away.

== Installation ==

= Install = 

1. Upload **`wp-cron-per-action.php`** and other files compressed in the zip folder to the **`/wp-content/plugins/`** directory.,
2. Activate the plugin through the 'Plugins' menu in WordPress.

= How to Use = 
Just activate the plugin.

== Other Notes ==

== Frequently Asked Questions ==

== Screenshots ==


== Changelog ==

= 1.0.0 = 
- Released initially.
