<?php
include 'database.php';

$filePath = 'C:\\xampp\\htdocs\\logs\\';

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

//$SafeZoneArray = ['2998.0603,18175.479,175,Folia','23334.605,24188.938,175,Silderas','14600,16797.2,175,Airport Mafia','23442.2,17737.1,175,Almyra','9263.3,12631.3,175,North Zaros'];
$SafeZoneArray = ['6325,7807,175,Stary Sobor','11666,3205,175,Otmel','4073,11677,175,Bash','11462,11348,175,Klen','12013,12653,175,NEAF'];


$time = date('Y-m-d G:i:s');
$msg = "\n\n";
$msg .= "======================================================\n";
$msg .= "Starting clearing safe zone: $time\n";
$msg .= "======================================================\n";


// Unlock Vehicles parked in safezones
$sql = "SELECT * FROM vehicle WHERE is_locked = -1 AND spawned_at < NOW() - INTERVAL 2 HOUR AND last_updated < NOW() - INTERVAL 2 HOUR";
$result = mysqli_query($db_local, $sql);
$VehicleCount = 0;


while($row = mysqli_fetch_object($result))
{
	$VehicleID = $row->id;
	$VehicleClass = $row->class;
	$VehicleX = $row->position_x;
	$VehicleY = $row->position_y;
	$VehicleOwnerUID = $row->account_uid;
	$IsLocked = -1;
	$VehicleLastUpdated = $row->last_updated;

	
	foreach ($SafeZoneArray as $coords)
	{
		$SafeZoneCoords = explode(",", $coords);
		$SafeZoneX = $SafeZoneCoords[0];
		$SafeZoneY = $SafeZoneCoords[1];
		$SafeZoneRadius = $SafeZoneCoords[2];
		$SafeZoneName = $SafeZoneCoords[3];
		
		if ((($VehicleX-$SafeZoneX)**2 + ($VehicleY-$SafeZoneY)**2 <= $SafeZoneRadius**2) && $IsLocked == -1)     // inside safezone
		{
			$sql2 = "SELECT name FROM account WHERE uid = '$VehicleOwnerUID'";
			$result2 = mysqli_query($db_local, $sql2);
			$row2 = mysqli_fetch_object($result2);
			$OwnerName = $row2->name;
			
			$sql2 = "DELETE FROM vehicle WHERE id = '$VehicleID'";
			echo "<hr>$sql2<hr>";
			//$result2 = mysqli_query($db_local, $sql2);
			$IsLocked = 0;
			$VehicleCount = $VehicleCount + 1;
			$msg .=  "$VehicleClass ($VehicleID) parked in the $SafeZoneName safezone by $OwnerName ($VehicleOwnerUID) at $VehicleLastUpdated has been deleted\n";			
		}			
	}
}
if($VehicleCount == 0)
{
	$msg .= "No vehicles to delete\n";
}
else
{
	$msg .= "$VehicleCount vehicles deleted\n";
	
}

$msg .= "======================================================\n\n";
echo $msg;
LogChanges($msg,$filePath.'SafeZone.log');
exit();


function LogChanges($text,$filename)
{
  // open log file

  echo '<hr>'.$filename.'<hr>';
  $fh = fopen($filename, "a") or die("Could not open log file.");
  fwrite($fh, "$text") or die("Could not write file!");
  fclose($fh);
}
?>
