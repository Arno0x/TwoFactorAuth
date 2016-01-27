<?php
/**
 * TwoFactorAuth User database creation file - This script creates the SQLite3 database
 * and creates the users table. 
 * 
 * This script is run only once on FIRST TIME INSTALL or after the database file has been deleted
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//------------------------------------------------------
// Include config file
require_once ('../config.php');

// Allow included script to be included from this script
define('INCLUSION_ENABLED',true);

//------------------------------------------------------
// If the User DB file already exists, interrupt the installation process
if (file_exists(USER_SQL_DATABASE_FILE)) {
    echo "[<strong>ERROR</strong>] Database already installed. If you want to start the installation process over again, delete the user database file, ";
	echo "and then call this page again.";
	exit();
}

//==========================================
// Proceed with the installation
//==========================================

//------------------------------------------------------
// If any form variable is missing, just display the install form page
if (!isset($_POST["username"]) || !isset($_POST["password"])) {
	require_once("installForm.php");
}
else {
    //------------------------------------------------------
    // Retrieve and store form parameters
    $username = htmlspecialchars($_POST["username"], ENT_QUOTES);
    $password = $_POST["password"];
    
    $message = "";
    
    //------------------------------------------------------
    // On first installation, create some directories
    if (!file_exists(USER_SQL_DATABASE_DIRECTORY)) { mkdir(USER_SQL_DATABASE_DIRECTORY); }
    if (!file_exists(QRCODE_TEMP_DIR)) { mkdir(QRCODE_TEMP_DIR); }

	//------------------------------------------------------
	// Import the DBManager library
	require_once(DBMANAGER_LIB);
	
	// Allow included script to be included from this script
	define('INCLUSION_ENABLED',true);

	//------------------------------------------------------
	// Check for SQLite3 support
	if(!class_exists('SQLite3')) {
	  exit ("SQLite 3 NOT supported");  
	}
	
	//------------------------------------------------------
	// Create and open the database
	try { 
	    $dbManager = new DBManager (USER_SQL_DATABASE_FILE, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
	} catch (Exception $e) {
	    echo "[ERROR] Could not create database. Exception received : " . $e->getMessage();
	    exit();
	}
	
	//------------------------------------------------------
	// Drop the USERS table
	$sql = "DROP TABLE USERS;";
	
	if(!($ret = $dbManager->exec($sql))) {
	} else {
	  $message = $message."[<strong>OK</strong>] Previous USERS table dropped successfully<br>";
	}
	
	//------------------------------------------------------
	// Create the USERS table
	$sql =<<<EOF
	      CREATE TABLE USERS (
	      ID INTEGER PRIMARY KEY NOT NULL,
	      USERNAME       VARCHAR(255) UNIQUE NOT NULL,
	      PASSWORDHASH     VARCHAR(255)    NOT NULL,
	      GAUTHSECRET VARCHAR(255) NOT NULL,
	      ISADMIN TINYINT NOT NULL);
EOF;
	
	if(!($ret=$dbManager->exec($sql))) {
	  echo $dbManager->lastErrorMsg();
	  exit();
	} else {
	  $message = $message."[<strong>OK</strong>] Table created successfully<br>";
	}
	
	//------------------------------------------------------
	// Import the Google Authenticator library
	require_once(GAUTH_LIB);
	
	// Create GoogleAuth object
    $gauth = new GoogleAuthenticator();
    
    // Generate a random secret
	$secret = $gauth->createSecret();
    
    //------------------------------------------------------
	// Create the admin/admin user
	if ($dbManager->addUser($username,$password,$secret,true)) {
		// Create the QRCode as PNG image
	    $randomString = bin2hex(openssl_random_pseudo_bytes (15));
	    $qrcodeimg = QRCODE_TEMP_DIR.$randomString.".png";
	    $gauth->getQRCode($username,$secret,$qrcodeimg,QRCODE_TITLE);
	    $message = $message."[<strong>OK</strong>] User ".$username." successfully created<br>";
	}
	else {
		echo "[<strong>ERROR</strong>] Could not create the account in the database: ".$dbManager->lastErroMsg();
		$dbManager->close();
		exit();
	}
	$dbManager->close();
	
	// Creating a session to persist the authentication
    session_name(SESSION_NAME);
    session_cache_limiter('private_no_expire');
    
    // Session parameters :
    // - Timelife of of the whole browser session
    // - Valid for all path on the domain, for this FQDN only
    // - Ensure Cookies are not available to Javascript
    // - Cookies are sent on https only
    $domain = ($_SERVER['HTTP_HOST'] !== 'localhost') ? $_SERVER['SERVER_NAME'] : false;
    session_set_cookie_params (0, "/", $domain, true, true);

    // Create a session
    session_start();
    
    $_SESSION["authenticated"] = true;
    $_SESSION["username"] = $username;
    $_SESSION["isAdmin"] = true;
	
	echo <<<EOT
    <html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TwoFactorAuth</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
    <div class="container" style="margin-top: 10px">
    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
    	<div class="panel panel-default">
        	<div class="panel-heading" style="text-align: center">
        		<span class="fa fa-thumbs-o-up supersize1"></span>
        		<span class="panel-title"><strong>SUCCESS</strong></span>
            </div> 	<!-- End of panel heading -->
        
            <div class="panel-body">
EOT;
	
	// Echoing all installation messages
	echo $message;
	
	// Showing the QRCode in an overlay div
	echo "<div id=\"overlay\" class=\"blackOut\"><span class=\"boxWrapper\"><div class=\"box\">";
	require_once(LIB_DIR."/showQRCode.php")	;
    echo"</div></span></div>";
    
    //------------------------------------------------------
    // Application base url
    $baseUrl = dirname(dirname($_SERVER["SCRIPT_NAME"]));
    echo <<<EOT
    <br>
    You can now go to the TwoFactorAuth <a href="{$baseUrl}/admin/admin.php">administration page</a>.
	</div>
    </div> <!-- End of panel class -->
    </div> <!-- End of column classes -->
</div> <!-- End of row -->
</div> <!-- End of container -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</body>
</html>
EOT;
}
?>
