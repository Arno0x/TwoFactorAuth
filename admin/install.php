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
require_once ("../config.php");

//------------------------------------------------------
// If the database file does not exists then we start the creation process
if (!file_exists(USER_SQL_DATABASE_FILE)) {
	
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
	  echo "Table USERS dropped successfully<br>";
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
	  echo "Table created successfully<br>";
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
	if ($dbManager->addUser("admin","admin",$secret,true)) {
		// Create the QRCode as PNG image
	    $randomString = bin2hex(openssl_random_pseudo_bytes (15));
	    $qrcodeimg = QRCODE_TEMP_DIR.$randomString.".png";
	    $gauth->getQRCode("admin",$secret,$qrcodeimg,QRCODE_TITLE);
	    
	    echo "User 'admin' with password 'admin' was successfully created.<br><br>";
	}
	else {
		echo "[ERROR] Could not create the admin account in the database: ".$dbManager->lastErroMsg();
	}
	$dbManager->close();

	//------------------------------------------------------
	require_once("showQRCode.php")	;
	
} else {
	echo "Database already installed. If you want to start the installation process over again, delete the user database file: ".USER_SQL_DATABASE_FILE;
	echo "<br>and then call this page again.";
}
?>