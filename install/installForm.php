<?php

/**
 * TwoFactorAuth 
 * This page displays the form used to create the first user upon installation
 * 
 * This file is not supposed to be called directly but only included from the install.php page
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
 //----------------------------------------------
 // Check that this page was not called directly - it should only be included from another script
if (!defined("INCLUSION_ENABLED")) {
    echo "<h1>FORBIDDEN - This page cannot be called directly</h1>";
    http_response_code(403);
	exit();
}
?>
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
        		<span class="fa fa-cogs supersize1"></span>
        		<span class="panel-title"><strong>INSTALLATION</strong></span>
            </div> 	<!-- End of panel heading -->
        
            <div class="panel-body">
            This script will finalize the installation of TwoFactorAuth on your server. What it does :<br>
            <ul>
                <li>Create some directories</li>
                <li>Create the users database</li>
                <li>Create the first user with admin rights</li>
            </ul>
            <span class="fa fa-exclamation-triangle"></span> Current settings from the config.php :<br>
            <ul>
                <li> SESSION_NAME : <strong><?php echo htmlspecialchars(SESSION_NAME, ENT_QUOTES); ?></strong></li>
                <li> QRCODE_TITLE : <strong><?php echo htmlspecialchars(QRCODE_TITLE, ENT_QUOTES); ?></strong></li>
                <li> AUTH_SUCCEED_REDIRECT_URL : <strong><?php echo htmlspecialchars(AUTH_SUCCEED_REDIRECT_URL, ENT_QUOTES); ?></strong></li>
            </ul>
            If these settings do not match your expectations, please modify the config.php before proceeding.<br>
            <br>
            Please enter details for the first user (will have admin rights) :<br>
            <br>
            <form id="addUserForm" action="install.php" method="post">
        		<div id="inputgroup1" class="input-group">
        			<span class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
        			<input type="text" class="form-control" id="username" name="username" placeholder="Username" autofocus>
        		</div>
        		<div id="userNameFeedback" class="text-center feedback"></div>
        		<br>
        		<div id="inputgroup2" class="input-group">
        			<span class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
        			<input type="password" class="form-control" id="password" name="password" placeholder="Password">
        		</div>
        		<div id="passwordFeedback" class="text-center feedback"></div>
        		<br>
        		<div id="inputgroup3" class="input-group">
        			<span class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
        			<input type="password" class="form-control" id="password2" name="password2" placeholder="Confirm password">
        		</div>
        		<div id="password2Feedback" class="text-center feedback"></div>
        		<br>
        		<div class="text-center"><button id="submit" type="submit" name="action" value="addUser" class="btn btn-sm btn-primary">OK <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span></button></div>
        	</form>
        	</div>
        </div> <!-- End of panel class -->
	</div> <!-- End of column classes -->
</div> <!-- End of row -->
</div> <!-- End of container -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
// Form submission callback, performs some checks on the user input
// then POST information to the admin PHP page
$(document).ready(function(){
	// Form submission callback, performs some checks on the user input
	// then POST information to the backend PHP page
	$("#addUserForm").submit(function(event) {
	
		// Get form parameters attributes and perform basic checks, even if all these will be checked server side as well
		var username = $("#username").val();
		var password = $("#password").val();
		var password2 = $("#password2").val();
		var error = false;

        if (username === '') {
			error = true;	
			$("#inputgroup1").addClass("has-error");
			$("#userNameFeedback").show();
			$("#userNameFeedback").text("Username is empty");
		}
		
		if (password.length < 6) {
			error = true;
			$("#inputgroup2").addClass("has-error");
			$("#passwordFeedback").show();
			$("#passwordFeedback").html("Password must be at least 6 characters");
		}
		
		if (password !== password2) {
		    error = true;
		    $("#inputgroup3").addClass("has-error");
			$("#password2Feedback").show();
			$("#password2Feedback").html("Password confirmation doesn't match");
		}
		
		if (error) {
			event.preventDefault();
		}
		else {
		    $("#submit").html("Wait... <span class=\"fa fa-spinner fa-spin\"></span>");
		    return;
		}
    });
});
</script>
</body>
</html>
