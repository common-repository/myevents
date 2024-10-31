<?php

class Event
{
	var $id;
	var $name;
	var $location;
	var $date;

	function Event()
	{
		$this->id = null;
		$this->name = null;
		$this->location = null;
		$this->date = null;
	}
	
	function initFromDb($elem)
	{
		if (isset($this))
		{
			$this->id = $elem->id;
			$this->name = $elem->name;
			$this->location = $elem->location;
			$this->date = $elem->event_date;
		}
	}
	
	function save()
	{
		global $wpdb;
		if ($this->name <> null && $this->name <> '')
		{
			if ($this->id == null) {
				$sql = "INSERT INTO {$wpdb->events} (name,location,event_date) VALUES ('{$this->name}', '{$this->location}', '{$this->date}')";
			} else {
				$sql = "UPDATE {$wpdb->events} SET name='{$this->name}', location='{$this->location}', event_date='{$this->date}' WHERE id={$this->id}";
			}
			$wpdb->query($sql);
		}
	}

	// Static methods
	function getAll($limit = 100)
	{
		global $wpdb;
		
		$ret = array();
		
		$sql = "SELECT * FROM (SELECT * FROM {$wpdb->events} ORDER BY event_date DESC LIMIT $limit) AS t ORDER BY event_date";

		$result = $wpdb->get_results($sql);
		
		foreach($result as $elem)
		{
			$pl = new Event();
			$pl->initFromDb($elem);
			$ret[] = $pl;
		}
		
		return $ret;
	}
	
	function getPast($limit = 100)
	{
		global $wpdb;
		
		$ret = array();
		
		$sql = "SELECT * FROM (SELECT * FROM {$wpdb->events} WHERE event_date < NOW() ORDER BY event_date DESC LIMIT $limit) AS t ORDER BY event_date";

		$result = $wpdb->get_results($sql);
		
		foreach($result as $elem)
		{
			$pl = new Event();
			$pl->initFromDb($elem);
			$ret[] = $pl;
		}
		
		return $ret;
	}

	function getFuture($limit = 100)
	{
		global $wpdb;
		
		$ret = array();
		
		$sql = "SELECT * FROM {$wpdb->events} WHERE event_date >= NOW() ORDER BY event_date LIMIT $limit";

		$result = $wpdb->get_results($sql);
		
		foreach($result as $elem)
		{
			$pl = new Event();
			$pl->initFromDb($elem);
			$ret[] = $pl;
		}
		
		return $ret;
	}
	
	function getFromIndex($id)
	{
		global $wpdb;
		
		$sql = "SELECT * FROM {$wpdb->events} WHERE id = $id";
		$result = $wpdb->get_row($sql);
		
		$pl = new Event();
		$pl->initFromDb($result);
		
		return $pl;
	}
		
	function delete($id)
	{
		global $wpdb;
			
		$sql = "DELETE FROM {$wpdb->events} WHERE id = $id";
		$wpdb->query($sql);
	}
}

?>