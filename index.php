<?php
/**
 * TwoFactorAuth index page
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */

//------------------------------------------------------
// Include config file
require_once("config.php");

//------------------------------------------------------
// Application base url
$baseUrl = dirname($_SERVER["SCRIPT_NAME"]);

//------------------------------------------------------
// If this page is being called for the first time since the package has been
// deployed on a server and the installation hasn't been performed yet, then redirect
// to the insstallation page
if (!file_exists(USER_SQL_DATABASE_FILE)) {
	$redirectTo = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on")? "https://" : "http://").$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$baseUrl."/install/install.php";
	header("Location: ".$redirectTo,true,302);
}
else {
	//------------------------------------------------------
	// Restore session
	session_name(SESSION_NAME);
	session_start();
	
	// Check the whether we have a currently logged in user
    if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
    	
    	//------------------------------------------------------
        // Retrieve the currently logged user from the session
        $username = $_SESSION["username"];
        
    	echo <<<EOT
		<!DOCTYPE html>
		<html>
		<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>TwoFactorAuth</title>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="css/style.css">
		</head>
		<body>
		<div class="container" style="margin-top: 10px">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
			    <div class="panel panel-default">
					<div class="panel-heading" style="text-align: center">
						<span class="fa fa-user" aria-hidden="true"></span>
						<span class="panel-title"><strong>Logged as {$username}</strong></span>
					<a href="user/logout.php"><span style="font-size: 1.5em" class="fa fa-power-off pull-right"></span></a>
            		</div> 	<!-- End of panel heading -->
		            <ul class="list-group">
EOT;
        
        echo "<li class=\"list-group-item\"><a href=\"user/user.php\">User management</a> <span class=\"fa fa-user pull-right\" aria-hidden=\"true\"></span></li>";
        
        if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] === true) {
            echo "<li class=\"list-group-item\"><a href=\"admin/admin.php\">Administration</a> <span class=\"fa fa-wrench pull-right\" aria-hidden=\"true\"></span></li>";
        }
        
        echo <<<EOT
			  </ul>
	        </div>
		</div> <!-- End of column classes -->
	</div> <!-- End of row -->
	</div> <!-- End of container -->
	</body>
	</html>
EOT;
    }
    else {
        $redirectTo = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on")? "https://" : "http://").$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$baseUrl."/login/login.php";
		header("Location: ".$redirectTo,true,302);
    }
	
}
?>