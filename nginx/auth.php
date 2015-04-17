<?php
/**
 * TwoFactorAuth Auth file for Nginx auth_request module.
 * This script performs the actual check of the authentication token
 * that was generated after a successful login.
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */

/** ========================= DEBUG BLOCK ========================== 

$debugFileName = dirname(__FILE__).DIRECTORY_SEPARATOR."debug.log";
$debugHandle = fopen ($debugFileName ,"a");

foreach ($_SERVER as $key => $value) {
	fwrite ($debugHandle,$key.": ".$value."\n");
}

fwrite ($debugHandle,"END");
fclose($debugHandle);
===================================================================*/

//------------------------------------------------------
// Include config file
require_once ("../config.php");

//====================================================
// Restore an existing session
session_name(SESSION_NAME);
session_start();

//====================================================
// Check if the authentication has been completed
if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
    http_response_code(200);
}
else {
	    // Else return an HTTP 401 status code
	    session_destroy();
		http_response_code(401);
	}
?>
