<?php
/**
 * TwoFactorAuth Administration page - This script provides all management actions on the user database
 * such as adding/deleting users, as well as modifying their attributes.
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */

//------------------------------------------------------
// Include config file
require_once("../config.php");

// Allow included scripts to be included from this script
define('INCLUSION_ENABLED',true);
	
//------------------------------------------------------
// Restore session
session_name(SESSION_NAME);
session_start();

// Check whether the currently logged in user is admin or not
if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
	echo "<h1>FORBIDDEN - You must be logged on with admin rights to access this page</h1>";
	http_response_code(403);
	exit();
}

//-----------------------------------------------------
// Import required libraries
require_once(DBMANAGER_LIB);
require_once(NOCSRF_LIB);

//------------------------------------------------------
// Main processing
try {
    $dbManager = new DBManager(USER_SQL_DATABASE_FILE);
    
    //------------------------------------------------------
    // Check if an action was requested on the admin page
    if (isset($_POST["action"])) {
    	//---------------------------------------------
    	// Check for a CSRF attempt
    	if (NoCSRF::check('csrf_token', $_POST, false, 60*10, false) === false) {
    		echo "<h1>CSRF attempt detected</h1>";
			http_response_code(403);
			exit();
    	}
    	
    	// Generate a new CSRF token to use in form hidden field
		$token = NoCSRF::generate('csrf_token');

	// Santizing userName
	$username = $userName = '';

	if (isset($_POST['username']))
	{
		$username = $userName = htmlspecialchars($_POST['username'], ENT_QUOTES);
	}
    	
		//---------------------------------------------
		// Parse all possible actions
		switch($_POST["action"]) {
			// Delete all users table
			case "deleteDatabase":
					$dbManager->deleteAllUsers();
				break;
			
			// Delete a user
			case "deleteUser":
				if ($userName) {
					if(!$dbManager->deleteUser($userName)) {
						$message = "[ERROR] Could not delete username ".$userName;
					}
				}
				break;

            // Show the add User form
			case "addUserForm":
			        $overlay = "addUserForm.php";
			    break;
			
			// Add a user to the system
			case "addUser":
			        if ($userName && isset($_POST["password"])) {
			            
			            require_once(GAUTH_LIB);
			            
			            // Create GoogleAuth object
                    	$gauth = new GoogleAuthenticator();
                        
                        isset($_POST['isAdmin']) ? $isAdmin = 1 : $isAdmin = 0;
                        
                        // Generate a random secret
                        $secret = $gauth->createSecret();
                        
                        // Add user to the database
                        if ($dbManager->addUser($userName,$_POST["password"],$secret,$isAdmin)) {
                        	// Create the QRCode as PNG image
                            $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                            $qrcodeimg = QRCODE_TEMP_DIR.$randomString.".png";
                            $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);
                            $overlay = LIB_DIR."/showQRCode.php";
                        }
			        }
			    break;
			    
			// Show the change password form for the selected user
			case "chgPwdForm":
			    if ($userName) {
			        $overlay = "changePasswordForm.php";
				}
			    break;
			    
			// Show the change password form for the selected user
			case "changePassword":
			    if ($userName && isset($_POST["password"])) {
			    	if($dbManager->updatePassword($userName,$_POST["password"])) {
			    	    $message = "[SUCCESS] Password successfully changed for user ".$userName;
			    	}
			    	else {
			    	    $message = "[ERROR] Could not change password for user ".$userName.". Impossible to write into the user database";
			    	}
				}
			    break;
			
			// Show the QRCode, for current GAuth secret, for selected user
			case "showQRCode":
			    if ($userName) {
			        require_once(GAUTH_LIB);
			        
			        // Create GoogleAuth object
    	            $gauth = new GoogleAuthenticator();
			        
			        if (($secret = $dbManager->getGauthSecret($userName))) {
    			        // Create the QRCode as PNG image
                        $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                        $qrcodeimg = QRCODE_TEMP_DIR.$randomString.".png";
                        $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);
                        
                        $overlay = LIB_DIR."/showQRCode.php";
			        }
				}
			    break;
			    
			// Renew the GAuth secret  for selected user and show the corresponding QRCode
			case "renewGAuthSecret":
			    if ($userName) {
			    	require_once(GAUTH_LIB);
			        
			        // Create GoogleAuth object
    	            $gauth = new GoogleAuthenticator();
    	            $secret = $gauth->createSecret();
    	            
			        if (($dbManager->updateGauthSecret($userName,$secret))) {
    			        // Create the QRCode as PNG image
                        $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                        $qrcodeimg = QRCODE_TEMP_DIR.$randomString.".png";
                        $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);
                        
                        $overlay = LIB_DIR."showQRCode.php";
			        }
				}
			    break;
		} 
	}
	else {
		// Generate CSRF token to use in form hidden field
		$token = NoCSRF::generate('csrf_token');
	}
} catch (Exception $e) {
    	echo "<h1>ERROR - Impossible to open the user database</h1>";
    	exit();
	}
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
				<span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
				<span class="panel-title"><strong>MANAGE USERS</strong></span>
            </div> 	<!-- End of panel heading -->
            <br>
            <table class="table table-striped table-bordered table-condensed table-hover">
                <caption class="text-center">
                    USER DATABASE
                    <form style="display: inline" id="deleteDatabase" action="admin.php" method="post">
	                    <button type="submit" name="action" value="deleteDatabase" class="btn btn-xs btn-danger pull-right"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                    </form>
                </caption>
                <thead>
                  <tr>
                    <th class="text-center">USERNAME</th>
                    <th class="text-center">ADMIN</th>
                    <th class="text-center">ACTIONS</th>
                  </tr>
                </thead>
                <tbody>
                <?php require_once("showUserList.php"); $dbManager->close(); ?>
                </tbody>
            </table>
            <br>
            <div class="text-center">
	            <form action="admin.php" method="post">
	            	<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
	            	<button type="submit" name="action" value="addUserForm" class="btn btn-success">
	            	<span class="fa fa-user-plus supersize1"></span> Add user</button>
	            </form>
	        </div>
            <br>
            <?php if (isset($message)) echo "<div class='message'>".$message."</div>";	?>

        </div>
	</div> <!-- End of column classes -->
</div> <!-- End of row -->
</div> <!-- End of container -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<?php
	if (isset($overlay)) {
        echo "<div id=\"overlay\" class=\"blackOut\"><span class=\"boxWrapper\"><div class=\"box\">";
        require_once($overlay);
        echo"</div></span></div>";
    }
?>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- Latest compiled JQuery lib -->
<script>
function confirmDelete(username) {
	return (confirm("You're about to delete user "+username+".\nAre you SURE ?"));
}

function confirmGAScrt(username) {
	return (confirm("You're about to renew the secret for user "+username+".\nAre you SURE ?"));
}

$(document).ready(function(){

    $("#deleteDatabase").submit(function(event) {
        if(!confirm("DELETE ALL USERS FROM DATABASE - ARE YOU SURE ?")) {
        	event.preventDefault();
        }
    });
});
</script>
</body>
</html>
