<?php
	// Updates the construction table so that the maintained date matches the last time the protection was paid
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	set_time_limit(300);

	$time_start = microtime(true);
	
	echo "<hr>Construction Fix<hr>";
	include 'database.php';


	// Create array of territories
	$TerritoryArray = [];
	
	$sql = "SELECT * FROM territory ORDER BY name";
	$result = mysqli_query($db_local, $sql);
	while($row = mysqli_fetch_object($result))
	{
		$position_x = $row->position_x;
		$position_y = $row->position_y;
		$territoryName = $row->name;
		$last_payed_at = $row->last_payed_at;
		$build_rights = str_replace('"','',$row->build_rights);
		$build_rights = str_replace('[','',$build_rights);
		$build_rights = str_replace(']','',$build_rights);
		$build_rights = str_replace(',','|',$build_rights);

		$radius = $row->radius;
		array_push($TerritoryArray,$position_x.','.$position_y.','.$radius.','.$territoryName.','.$build_rights.','.$last_payed_at);
	}

	foreach ($TerritoryArray as $coords)
	{
		$TerritoryCoords = explode(",", $coords);
		$TerritoryX = $TerritoryCoords[0];
		$TerritoryY = $TerritoryCoords[1];
		$TerritoryRadius = $TerritoryCoords[2];
		$TerritoryName = $TerritoryCoords[3];
		$TerritoryLastPayed = $TerritoryCoords[5];
		
		$TerritoryBuildRights = explode('|' , $TerritoryCoords[4]);
		$BuildRightsCount = count($TerritoryBuildRights);
		echo "<hr>Checking Territory $TerritoryName ($TerritoryRadius metre radius at $TerritoryX,$TerritoryY)<hr>";
		foreach ($TerritoryBuildRights as $account_uid)
		{
			$sql4 = "SELECT name FROM account WHERE uid = '$account_uid'";
			$result4 = mysqli_query($db_local, $sql4);
			$row4 = mysqli_fetch_object($result4);
			$ConstructionBy = $row4->name;
			
			$sql4 = "SELECT * FROM construction WHERE account_uid = '$account_uid' AND maintained_at <> '$TerritoryLastPayed'";
			$result4 = mysqli_query($db_local, $sql4);
			while($row4 = mysqli_fetch_object($result4))
			{
				$ConstructionID = $row4->id;
				$ConstructionClass = $row4->class;
				$ConstructionX = $row4->position_x;
				$ConstructionY = $row4->position_y;
				$ConstructionMaintainedAt = $row4->maintained_at;
				$ConstructionSpawnedAt = $row4->spawned_at;
				
				// Check if the construction is in the radius of the territory
				if ((($ConstructionX-$TerritoryX)**2 + ($ConstructionY-$TerritoryY)**2 <= $TerritoryRadius**2) && $TerritoryLastPayed > $ConstructionSpawnedAt)     // inside territory
				{
					// Update the construction.maintained_at = territory.last_payed_at
					$sql5 = "UPDATE construction SET maintained_at = '$TerritoryLastPayed' WHERE id = '$ConstructionID'";
					$result5 = mysqli_query($db_local, $sql5);
					echo "Territory: $TerritoryName  $ConstructionClass built by $ConstructionBy updated to $TerritoryLastPayed (was $ConstructionMaintainedAt)<br>";
				}				
			}
	
		}
		
	
	}


	echo "<hr>DONE!<hr>";
	
	$time_end = microtime(true);

	//dividing with 60 will give the execution time in minutes other wise seconds
	$execution_time = ($time_end - $time_start)/60;

	//execution time of the script
	echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
?>
