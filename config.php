<?php
/**
 * TwoFactorAuth main configuration file
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */

//========================================================================
// Users database settings
//========================================================================

// Setting the path to the users database file
define('USER_SQL_DATABASE_FILE', dirname(__FILE__).DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.'users.sqlite3');

//========================================================================
// Library settings
//========================================================================

// Setting the path to the lib directory
define('LIB_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR);

// Setting the path for the GoogleAuth library
define('GAUTH_LIB',LIB_DIR.'googleAuthenticator.php');

// Setting the path for the DBManager library
define('DBMANAGER_LIB',LIB_DIR.'dbManager.php');

//========================================================================
// QRCode generation settings
//========================================================================

// Setting the temporary directory to hold generated QR codes
define('QRCODE_TEMP_DIR',dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR);

// Set the title that will be used for the QRCode generated
// This title appears in the Google authenticator App to help identifying what the token relates to
// --> CHANGE THIS TO YOUR OWN MEANINGFUL TITLE
define('QRCODE_TITLE','www.exemple.com');

//========================================================================
// Application & Session settings
//========================================================================

// Set the session name you want to use. If you're integrating TwoFactorAuth
// with your own application session, you might want to set it to the session name
// you're already using
// --> CHANGE THIS TO YOUR OWN MEANINGFUL SESSION NAME
define('SESSION_NAME','2fatkn');

// Redirection mechanism upon successful login :
// If the user has been redirected to the login page from another page AND this other page
// is passed as a GET parameter to the login page, then the user will be redirected back to
// the originating URL
// Otherwise, redirect to the URL specified here
// --> CHANGE THIS TO YOUR OWN MEANINGFUL PAGE
define('AUTH_SUCCEED_REDIRECT_URL','https://www.exemple.com/');
?>