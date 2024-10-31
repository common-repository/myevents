=== MyEvents ===
Contributors: leocacheux
Tags: music, list, playlist
Requires at least: 2.0.0
Tested up to: 2.1.2
Stable tag: trunk

Manage list of events.

== Description ==

This plugin allows you to manage a list of events. They can be displayed in the layout of your theme. You can show only past or future events, 

== Installation ==

1. Unzip the archive in the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Edit your events and some options in the `Options > MyEvents` page of your admin.
4. Edit your theme with those functions. You can limit the size of the list with the first parameter.
* `get_all_events($limit = 100, $date_format = 'd/m/Y')` : list all events, `$limit` shows only the bottom of the list
* `get_past_events($limit = 100, $date_format = 'd/m/Y')` : list only past events, `$limit` shows only the bottom of the list
* `get_future_events($limit = 100, $date_format = 'd/m/Y')` : list only future events, `$limit` shows only the top of the list