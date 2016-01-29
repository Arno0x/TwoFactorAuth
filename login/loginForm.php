<?php
/**
 * TwoFactorAuth - This page displays the login form
 * 
 * This file is not supposed to be called directly but only included from the admin.php page
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//----------------------------------------------
// Check that this page was not called directly - it should only be included from another script
if (!defined("INCLUSION_ENABLED")) {
    http_response_code(403);
    echo "<h1>FORBIDDEN - This page cannot be called directly</h1>";
    exit();
}

$safe_from = '';

if (isset($_GET['from']))
{
	$safe_from = '?from=' . htmlspecialchars(urlencode(stripslashes($_GET['from'])), ENT_QUOTES);
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
<style type="text/css">
body {
	margin: 0;
	padding: 0;
	background-repeat: no-repeat;
	background: #000000; /* Old browsers */
	background: linear-gradient(top, #000000 0%,#090A44 100%); /* W3C */
	background: -moz-linear-gradient(top, #000000 0%, #090A44 100%) no-repeat; /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#000000), color-stop(100%,#090A44)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, #000000 0%,#090A44 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, #000000 0%,#090A44 100%); /* Opera11.10+ */
	background: -ms-linear-gradient(top, #000000 0%,#090A44 100%); /* IE10+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#000000', endColorstr='#090A44',GradientType=0 ); /* IE6-9 */
}

html {
	height: 100%;
}
.message {
    padding: 7px;
}
</style>
</head>
<body>
<div class="container" style="margin-top: 10px">
<div class="row">
	<div class="col-sm-5 col-sm-offset-3">
	    <div class="panel panel-default">
			<div class="panel-heading" style="text-align: center">
				<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>
				<span class="panel-title"><strong>LOGIN</strong></span>
            </div> 	<!-- End of panel heading -->
		
		    <form id="connectionForm" action="login.php<?php echo $safe_from; ?>" method="post" style="padding: 10px">
				<div id="inputgroup1" class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
					<input type="text" class="form-control" placeholder="Username" id="username" name="username" autocomplete="off" autofocus>
				</div>
				<div id="usernameFeedback" class="text-center feedback"></div>
				<br>
				<div id="inputgroup2" class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
					<input type="password" placeholder="Password" class="form-control" id="password" name="password">
				</div>
				<div id="passwordFeedback" class="text-center feedback"></div>
				<br>
				<div id="inputgroup3" class="input-group">
					<span class="input-group-addon"><span class="fa fa-key"></span></span>
					<input type="text" placeholder="Token" class="form-control" id="token" name="token" autocomplete="off">
				</div>
				<div id="tokenFeedback" class="text-center feedback"></div>
				<br>
				<div class="text-center"><button id="submit" type="submit" class="btn btn-sm btn-primary">Login <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span></button></div>
			</form>
			<?php
			    if (isset($error)) {
			        echo "<div class='message'>".$error."</div>";
			    }
			?>
        </div> <!-- End of panel class -->
	</div> <!-- End of column classes -->
</div> <!-- End of row -->
</div> <!-- End of container -->
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- Latest compiled JQuery lib -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
$(document).ready(function() {
    
	// Form submission validation, performs some checks on the user input
	$("#connectionForm").submit(function(event) {
	   	// Get form parameters attributes and perform basic checks, even if all these will be checked server side as well
		var username = $("#username").val();
		var password = $("#password").val();
		var token = $("#token").val();
		var error = false;

        if (username === '') {
			error = true;	
			$("#inputgroup1").addClass("has-error");
			$("#usernameFeedback").show();
			$("#usernameFeedback").text("Username is empty");
		}
		
		if (password.length < 6) {
			error = true;
			$("#inputgroup2").addClass("has-error");
			$("#passwordFeedback").show();
			$("#passwordFeedback").html("Password must be at least 6 characters long");
		}
		
		if (token.length != 6) {
			error = true;
			$("#inputgroup3").addClass("has-error");
			$("#tokenFeedback").show();
			$("#tokenFeedback").html("Token must be exactly 6 digits long");
		}
		
		if (error) {
			event.preventDefault();
		}
		else return;
	});
});
</script>
</body>
</html>
