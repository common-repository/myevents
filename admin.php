<?php

require_once(dirname(__FILE__).'/Event.php');

function edit_date( $date = null ) {
	global $wp_locale;

	echo "<fieldset>";

	$time_adj = time() + (get_option( 'gmt_offset' ) * 3600 );
	$jj = ($date<>null) ? mysql2date( 'd', $date ) : gmdate( 'd', $time_adj );
	$mm = ($date<>null) ? mysql2date( 'm', $date ) : gmdate( 'm', $time_adj );
	$aa = ($date<>null) ? mysql2date( 'Y', $date ) : gmdate( 'Y', $time_adj );

	echo "<select name=\"mm\">\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		echo "\t\t\t<option value=\"$i\"";
		if ( $i == $mm )
			echo ' selected="selected"';
		echo '>' . $wp_locale->get_month( $i ) . "</option>\n";
	}
?>
</select>
<input type="text" id="jj" name="jj" value="<?php echo $jj; ?>" size="2" maxlength="2" />
<input type="text" id="aa" name="aa" value="<?php echo $aa ?>" size="4" maxlength="5" />
</fieldset>
	<?php
}

if (is_plugin_page()) :

if (isset($_POST['action']))
{
	if ( $_POST['action'] == 'options' ) {
		$myevents->settings['pattern'] = $_POST['pattern'];
		$myevents->save_options();
	}

	if ( $_POST['action'] == 'add' && isset($_POST['name']) && $_POST['name'] <> '' ) {
		$item = new Event();
		$item->name = $_POST['name'];
		$item->location = $_POST['location'];
		$item->date = $_POST['aa'] . '-' . $_POST['mm'] . '-' . $_POST['jj'];
		$item->save();
	}
	
	if ( $_POST['action'] == 'edit' && isset($_POST['name']) && $_POST['name'] <> '' ) {
		$item = Event::getFromIndex($_POST['itemid']);
		$item->name = $_POST['name'];
		$item->location = $_POST['location'];
		$item->date = $_POST['aa'] . '-' . $_POST['mm'] . '-' . $_POST['jj'];
		$item->save();
	}
}

if (isset($_GET['delitem']))
{
	Event::delete($_GET['delitem']);
}

?>
<div class="wrap">
<h2>MyEvents Options</h2>

<p>Create and edit events.</p>

<h3>General options</h3>

<form name="generaloptions" method="post">
<input type="hidden" name="action" value="options" />
<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr>
		<td width="33%" valign="top" scope="row">Pattern</td>
		<td><input name="pattern" type="text" id="pattern" value="<?php echo $myevents->settings['pattern']; ?>" /></td>
	</tr>
</table>
<br/><code>%name%, %location% and %date% will be replaced by values</code>
<br/><br/><input type="submit" name="Submit" value="Edit options" />
</form>

<br/>

<h3>Add/Edit events</h3>

<?php
	$events = Event::getAll();
	
	echo "<table><tr>\n";
	echo "<th style=\"width:180px\">Name</th>";
	echo "<th style=\"width:180px\">Location</th>";
	echo "<th style=\"width:220px\">Date</th>";
	echo "<th style=\"width:50px\"></th></tr>\n";
	echo "</table>\n";
	
	foreach ($events as $item)
	{
		echo "<form name=\"edit\" method=\"post\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"edit\" />\n";
		echo "<input type=\"hidden\" name=\"itemid\" value=\"{$item->id}\" />\n";
		echo "<table><tr>\n";
		echo "<td style=\"width:180px\"><input name=\"name\" value=\"{$item->name}\" /></td>\n";
		echo "<td style=\"width:180px\"><input name=\"location\" value=\"{$item->location}\" /></td>\n";
		echo "<td style=\"width:220px\">"; edit_date($item->date); echo "</td>\n";
		echo "<td style=\"width:50px\"><input type=\"submit\" value=\"Edit\" /></td>\n";
		echo "<td style=\"width:50px\"><a href=\"".$_SERVER['PHP_SELF']."?page=myevents/admin.php&delitem={$item->id}\">Delete</a></td>\n";
		echo "</table>\n";
		echo "</form>\n";
	}
	
	echo "</table>\n";
	
	echo "<form name=\"add\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\" />\n";
	echo "<table><tr>\n";
	echo "<td style=\"width:180px\"><input name=\"name\" /></td>\n";
	echo "<td style=\"width:180px\"><input name=\"location\" /></td>\n";
	echo "<td style=\"width:220px\">";
	
	edit_date();
	
	echo "</td>\n";
	echo "<td style=\"width:50px\"><input type=\"submit\" value=\"Add\" /></td>\n";
	echo "</table>\n";
	echo "</form>\n";
?>
</div>
<?php
endif;
?>