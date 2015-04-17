<?php
/**
 * TwoFactorAuth user logout page - This scripts logs the user out and destroy the PHP Sessio
 * as well as all objets attached to it.
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//------------------------------------------------------
// Include config file
require_once("../config.php");

//------------------------------------------------------
// Destroy session for current user
session_name(SESSION_NAME);
session_start();
session_unset();
session_destroy();

echo "OK - logged out";
?>
<!DOCTYPE html>
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
				<span class="panel-title"><strong>LOGGED OUT</strong></span>
            </div> 	<!-- End of panel heading -->
        </div>
	</div> <!-- End of column classes -->
</div> <!-- End of row -->
</div> <!-- End of container -->
</body>
</html>