<?php

	echo "\n================================Database Cleanup Started\n================================\n";
	
	include 'database.php';

	// Do db backup
	// -------------------------------------------------------	 

	 
	// if mysqldump is on the system path you do not need to specify the full path
	// simply use "mysqldump --add-drop-table ..." in this case
	$date = date("Y-m-d_H-i-s");
	$dumpfname = 'C:\\xampp\\htdocs\\db\\'.$dbname.'_'.$date.'.sql';
	$command = "C:\\xampp\\mysql\\bin\\mysqldump --add-drop-table --host=$dbhost --user=$dbuser --password=$dbpass  exile > $dumpfname"; 
	system($command);
	
	echo "\nbackup $dumpfname made\n\n================================\n";

?>
