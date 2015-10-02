<?php

	echo "================================\nDatabase Cleanup Started:\n================================\n";
	
	include 'database.php';

	// Delete players not logged in for 7 days with less than 10 total_connections
	$sql = "DELETE FROM player WHERE account_uid IN (SELECT uid FROM account WHERE last_connect_at < NOW() - INTERVAL 7 DAY AND total_connections < 10)";
	$result = mysqli_query($db_local, $sql);
	
	// Remove containers not accessed in over 7 days
	$sql = "DELETE FROM container WHERE spawned_at < now() - interval 7 day AND last_accessed < now() - interval 7 day AND last_accessed <> '0000-00-00 00:00:00'";
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
