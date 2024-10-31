<?php
/*
Plugin Name: MyEvents
Plugin URI: http://www.mog-soft.org
Description: Allow user to create and display events
Author: Leo Cacheux
Version: 1.0
Author URI: http://leo.cacheux.net
*/
/*  Copyright 2007  Leo Cacheux  (email : leo@cacheux.net)
**
**  This program is free software; you can redistribute it and/or modify
**  it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
**  This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
**  along with this program; if not, write to the Free Software
**  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( ! class_exists( 'MyEvents' ) ) :

require_once(dirname(__FILE__).'/Event.php');

class MyEvents
{
	var $settings = array();
	var $table_version = 1;
	var $added_tables = false;

	function MyEvents()
	{
		if (isset($this))
		{
			$this->settings = get_settings('myevents');
			$this->register_tables();

			add_action('admin_menu', array(&$this, 'admin_menu'));

			if ($this->settings['table_version'] != $this->table_version) {
				$this->make_tables();
				$this->added_tables = true;
				update_option('myevents', $this->settings);
			}
		}
	}

	function register_tables()
	{
		global $wpdb;
		$wpdb->events = "{$wpdb->prefix}events";
	}

	function make_tables() {
		global $wpdb;
		
		if(!include_once(ABSPATH . 'wp-admin/upgrade-functions.php'))
			die(_e('There is was error adding the required tables to the database. ', 'MyPlaylist'));
		
		$sql = "CREATE TABLE {$wpdb->events}
				( id INTEGER NOT NULL AUTO_INCREMENT,
					name VARCHAR(128) NOT NULL,
					location VARCHAR(128) NOT NULL,
					event_date DATE,
					UNIQUE KEY id (id)
				) TYPE=MyISAM";
		dbDelta($sql);
			
		$this->settings['table_version'] = $this->table_version;
		$this->settings['pattern'] = "<strong>%name%</strong> @ %location% - <em>%date%</em>";
		
		$this->save_options();
	}
	
	function save_options()
	{
		update_option('myevents', $this->settings);
	}
	
	function admin_menu()
	{
		if (function_exists('add_options_page')) {
			add_options_page('MyEvents', 'MyEvents', 8, "options-general.php?page=myevents/admin.php");
		}
	}
}

endif;

$myevents = new MyEvents();

function display_events($list, $date_format = 'd/m/Y')
{
	global $myevents;
	$text =  "<ul>\n";
	foreach ($list as $item) {
		if ($date_format == '') {
			$date = mysql2date(get_option('time_format'), $item->date);
		} else {
			$date = mysql2date($date_format, $item->date);
		}
		
		$text .= "<li>".str_replace(
					array('%name%', '%location%', '%date%'),
					array($item->name, $item->location, $date),
					$myevents->settings['pattern'])."</li>\n";
	}
	$text .= "</ul>\n";
	
	return $text;
}

function get_past_events($limit = 100, $date_format = 'd/m/Y')
{
	echo display_events(Event::getPast($limit), $date_format);
}

function get_future_events($limit = 100, $date_format = 'd/m/Y')
{
	echo display_events(Event::getFuture($limit), $date_format);
}

function get_all_events($limit = 100, $date_format = 'd/m/Y')
{
	echo display_events(Event::getAll($limit), $date_format);
}

?>