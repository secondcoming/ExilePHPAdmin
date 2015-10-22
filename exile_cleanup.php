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
	
	
	// Fix construction protection timestamps before deleting expired items
	include 'fix_construction_protection.php';
	// -------------------------------------------------------
	
	// remove hatchbacks on Chernarus as they get stuck in the terrain
	//$sql = "UPDATE vehicle SET class = 'Exile_Car_SUV_Black' WHERE class like '%hatch%'";
	//$result = mysqli_query($db_local, $sql);	
	
	// delete push bikes
	$sql = "DELETE FROM vehicle WHERE class = 'Exile_Bike_OldBike' OR class = 'Exile_Bike_MountainBike'";
	$result = mysqli_query($db_local, $sql);		
	
	
	// Delete players not logged in for $ProtectionPeriod days with less than 10 total_connections
	$sql = "DELETE FROM player WHERE account_uid IN (SELECT uid FROM account WHERE last_connect_at < NOW() - INTERVAL $ProtectionPeriod DAY)";
	$result = mysqli_query($db_local, $sql);
	
	// Remove vehicles not used in $ProtectionPeriod days
	$sql = "DELETE FROM vehicle WHERE last_updated < now() - interval $ProtectionPeriod DAY";
	$result = mysqli_query($db_local, $sql);
	
	// Remove containers not accessed in over $ProtectionPeriod days
	$sql = "DELETE FROM container WHERE spawned_at < now() - interval $ProtectionPeriod day AND last_accessed < now() - interval $ProtectionPeriod day AND last_accessed <> '0000-00-00 00:00:00'";
	$result = mysqli_query($db_local, $sql);
	
	// Remove constructions not paid for over $ProtectionPeriod days
	$sql = "DELETE FROM construction WHERE maintained_at < NOW() - INTERVAL $ProtectionPeriod DAY";
	$result = mysqli_query($db_local, $sql);	
	
	// Remove territories not mainntained for over $ProtectionPeriod days
	$sql = "DELETE FROM territory WHERE last_payed_at < NOW() - INTERVAL $ProtectionPeriod DAY";
	$result = mysqli_query($db_local, $sql);
	
	$sql = "SELECT * FROM player WHERE is_alive = 0 OR account_uid = 'SomethingWentWrong' OR account_uid = ''";
	$result = mysqli_query($db_local, $sql);
	$count = mysqli_num_rows($result);
	
	$sql = "DELETE FROM player WHERE is_alive = 0 OR account_uid = 'SomethingWentWrong' OR account_uid = ''";
	$result = mysqli_query($db_local, $sql);
	echo "$count Dead players removed from the database\n\n";

	// fix players unable to log in
	$sql = "UPDATE player set damage = 0, hitpoint_head = 0, hitpoint_body = 0, hitpoint_hands = 0, hitpoint_legs = 0 where is_alive = 1 and damage = 1";
	$result = mysqli_query($db_local, $sql);
	
	
	$sql = "UPDATE construction SET is_locked = -1 WHERE pin_code <> '00000' AND is_locked = 0";
	$result = mysqli_query($db_local, $sql);
	echo "All doors locked\n\n";
	


?>
