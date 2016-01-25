<?php

/**
 * TwoFactorAuth 
 * This page displays the form used to add a user
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
    echo "<h1>FORBIDDEN - This page cannot be called directly</h1>";
    http_response_code(403);
	exit();
}

?>
<div class="panel panel-default">
	<div class="panel-heading" style="text-align: center">
		<span class="fa fa-user-plus supersize1"></span>
		<span class="panel-title"><strong>ADD USER</strong></span>
		<a href="#"><span onclick="$('#overlay').fadeOut()" class="fa fa-close pull-right"></span></a>
    </div> 	<!-- End of panel heading -->

    <form id="addUserForm" action="admin.php" method="post" style="padding: 10px">
    	<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
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
		<div class="checkbox text-center">
              <label><input type="checkbox" name="isAdmin" value="1">Give admin rights</label>
        </div>
		<div class="text-center"><button id="submit" type="submit" name="action" value="addUser" class="btn btn-sm btn-primary">OK <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span></button></div>
	</form>
</div> <!-- End of panel class -->
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