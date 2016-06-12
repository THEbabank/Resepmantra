<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  E-Mail: N/A
  Website: www.maianscriptworld.co.uk
  This File: Database Connection File
  Written by David Ian Bennett
----------------------------------------------*/

$database = array();

//========================================================================================================
// NOTE: EDIT YOUR SQL CONNECTION INFORMATION BELOW. THE VARIABLES ARE ARRAY VARIABLES AND MUST NOT
// BE CHANGED. IE: $database['username']. DO NOT CHANGE THESE NAMES. ONLY THE VALUES SHOULD BE CHANGED
//========================================================================================================

//------------------------------------------------------
// HOST
// This is usually localhost or your server ip address
// Example: $database['host'] = 'localhost';
//------------------------------------------------------

$database['host']          = 'mysql.idhostinger.com';

//----------------------------------------------
// USERNAME
// Username assigned to database
// Example: $database['username'] = 'david';
//----------------------------------------------

$database['username']      = 'u837459223_resep';

//----------------------------------------------
// PASSWORD
// Password assigned to database
// Example: $database['password'] = 'abc1234';
//----------------------------------------------

$database['password']      = 'jSmgb7kNWg';

//----------------------------------------------
// DATABASE NAME
// Name of Database that holds tables
// Example: $database['database'] = 'recipe';
//----------------------------------------------

$database['database']      = 'u837459223_resep';

//----------------------------------------------
// TABLE PREFIX
// For people with only 1 database
// Example: $database['prefix'] = 'mr_';
// DO NOT comment this line out. It is important
//  to the script. Leave as default if not sure
//----------------------------------------------

$database['prefix']        = 'mr_';

//----------------------------------------------
// COOKIE SANITATION
// Choose secret key for cookie and cookie name.
// The longer and more complex the better..
// Random characters or phrase for key
//----------------------------------------------

$database['cookieName']    = 'mr_cookie27';
$database['cookieKey']     = 'hfgfyf[]f[9874hg36g88sgshgyghtythfdt00kfte27';

//================================
// DO NOT EDIT BELOW THIS LINE
//================================

$connect = @mysql_connect($database['host'],$database['username'],$database['password']);
if (!$connect) {
	die ('MySQL Error!!<br><br>Connection to the database has failed, this is the reason:<br /><br />'.mysql_error().'<br><br>Check your connection information.');
}
@mysql_select_db($database['database'], $connect) or die (mysql_error());

?>
