# ExilePHPAdmin
a few php scripts to automate housekeeping and backup of the mysql database for Exilemod


they can be called from a Windows batch file (check the example .bat files to see how)

To use the vehicle clear down you need to add the field last_updated to the vehicle table. This is updated every time a vehicle is driven or the inventory is accessed.

https://github.com/secondcoming/ExilePHPAdmin/blob/master/add_vehicle_last_updated.sql
