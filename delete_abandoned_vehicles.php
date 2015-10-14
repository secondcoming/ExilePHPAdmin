<?php

	echo "================================\nDatabase Cleanup Started:\n================================\n";
	
	include 'database.php';
	$filePath = 'C:\\xampp\\htdocs\\logs\\';
	
	// -------------------------------------------------------

	$time = date('Y-m-d G:i:s');
	$msg = "\n\n";
	$msg .= "======================================================\n";
	$msg .= "Delete abandoned vehicles: $time\n";
	$msg .= "======================================================\n";	
	
	
	// delete Vehicles not used in 48 hours and not parked in a territory
	$sql = "SELECT * FROM vehicle WHERE last_updated < NOW() - INTERVAL 48 HOUR";
	$result = mysqli_query($db_local, $sql);
	$vehicleCount = 0;
	while($row = mysqli_fetch_object($result))
	{
		$VehicleID = $row->id;	
		$VehicleClass = $row->class;
		$VehicleX = $row->position_x;
		$VehicleY = $row->position_y;
		$VehicleOwnerUID = $row->account_uid;
		$IsLocked = -1;
		$VehicleLastUpdated = $row->last_updated;

		
		// Create array of territories
		$territoryArray = [];
		
		$sql3 = "SELECT position_x, position_y, name FROM territory ORDER BY name";
		$result3 = mysqli_query($db_local, $sql3);
		while($row3 = mysqli_fetch_object($result3))
		{
			$position_x = $row3->position_x;
			$position_y = $row3->position_y;
			$territoryName = $row3->name;
			array_push($territoryArray,$position_x.','.$position_y.',100,'.$territoryName);
		}

		$InTerritoryRange = 0;
		
		foreach ($territoryArray as $coords)
		{
			$territoryCoords = explode(",", $coords);
			$territoryX = $territoryCoords[0];
			$territoryY = $territoryCoords[1];
			$territoryRadius = $territoryCoords[2];
			$territoryName = $territoryCoords[3];
			
			if ((($VehicleX-$territoryX)**2 + ($VehicleY-$territoryY)**2 <= $territoryRadius**2))     // inside territory
			{
				$InTerritoryRange = 1;
			}			
		}
		if($InTerritoryRange == 0) // not in range of a territory
		{
			$vehicleCount = $vehicleCount + 1;
			$sql2 = "SELECT name FROM account WHERE uid = '$VehicleOwnerUID'";
			$result2 = mysqli_query($db_local, $sql2);
			$row2 = mysqli_fetch_object($result2);
			$OwnerName = $row2->name;
			
			//$sql2 = "UPDATE vehicle SET is_locked = 0, damage = 0, fuel = 1 WHERE id = '$VehicleID'";
			$sql2 = "DELETE FROM vehicle WHERE id = '$VehicleID'";
			$result2 = mysqli_query($db_local, $sql2);
			$IsLocked = 0;
			//echo "\n$vehicleCount - $VehicleClass abandoned by $OwnerName has been deleted<br>\n";	
			$msg .=  "$VehicleClass ($VehicleID) abandoned by $OwnerName ($VehicleOwnerUID) at $VehicleLastUpdated has been deleted\n";				
		}
	}
	$msg .= "\n=========================================\n$vehicleCount vehicles deleted\n=========================================";

	echo $msg;
	LogChanges($msg,$filePath.'Abandoned.log');
	exit();

	function LogChanges($text,$filename)
	{
	  // open log file

	  //echo '<hr>'.$filename.'<hr>';
	  $fh = fopen($filename, "a") or die("Could not open log file.");
	  fwrite($fh, "$text") or die("Could not write file!");
	  fclose($fh);
	}
?>
