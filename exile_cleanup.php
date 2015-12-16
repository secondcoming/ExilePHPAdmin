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
	
	// Delete players not logged in for $ProtectionPeriod days with less than 10 total_connections
	$sql = "DELETE FROM player WHERE account_uid IN (SELECT uid FROM account WHERE last_connect_at < NOW() - INTERVAL $ProtectionPeriod DAY)";
	$result = mysqli_query($db_local, $sql);
	
	// Remove empty containers not used in 48 hours
	$sql = "DELETE FROM container WHERE last_updated_at <= NOW() - INTERVAL 48 HOUR AND cargo_items = '[[],[]]' AND cargo_magazines = '[]' AND cargo_weapons = '[]' AND cargo_container = '[]'";
	$result = mysqli_query($db_local, $sql);
	

?>
