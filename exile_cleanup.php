<?php

	// Set the protection period
	$ProtectionPeriod = 7;
	
	echo "================================\nDatabase Cleanup Started:\n================================\n";

	include 'database.php';

	// Do db backup
	// -------------------------------------------------------	 

	 
	// if mysqldump is on the system path you do not need to specify the full path
	// simply use "mysqldump --add-drop-table ..." in this case
	$date = date("Y-m-d_H-i-s");
	$dumpfname = 'C:\\xampp\\htdocs\\db\\'.$dbname.'_'.$date.'.sql';
	echo "<hr>$dumpfname<hr>";
	$command = "C:\\xampp\\mysql\\bin\\mysqldump --add-drop-table --host=$dbhost --user=$dbuser --password=$dbpass  exile > $dumpfname"; 
	system($command);
	
	echo "backup $dumpfname made\n\n";

	// delete push bikes
	$sql = "DELETE FROM vehicle WHERE class = 'Exile_Bike_OldBike' OR class = 'Exile_Bike_MountainBike'";
	$result = mysqli_query($db_local, $sql);
	
	// Remove empty containers not used in 48 hours
	$sql = "DELETE FROM container WHERE last_updated_at <= NOW() - INTERVAL 48 HOUR AND cargo_items = '[[],[]]' AND cargo_magazines = '[]' AND cargo_weapons = '[]' AND cargo_container = '[]'";
	$result = mysqli_query($db_local, $sql);

	// Remove containers not used in 48 hours not in a territory
	$sql = "DELETE FROM container WHERE last_updated_at <= NOW() - INTERVAL 48 HOUR AND territory_id IS NULL";
	$result = mysqli_query($db_local, $sql);

	// Remove old player history
	$sql = "DELETE FROM player_history WHERE died_at <= NOW() - INTERVAL 24 HOUR";
	$result = mysqli_query($db_local, $sql);
	
	// Remove constructions outside a territory and older than 48 hours
	$sql = "DELETE FROM construction WHERE territory_id IS NULL AND last_updated_at < NOW() - INTERVAL 2 DAY";
	$result = mysqli_query($db_local, $sql);	
		

?>
