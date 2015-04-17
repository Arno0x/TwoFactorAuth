<?php

/**
 * TwoFactorAuth user listing page - This script displays the list of users.
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

//--------------------------------------------------------
// Get the list of users and their admin
if (!($userList = $dbManager->getUserList())) {
    $message = "[ERROR] Could not get users from the database";
}
else {
    //--------------------------------------------------------
    // Create the list of users as a table content
    foreach ($userList as $key => $isAdmin) {
        echo "<tr>";
        echo "<td style=\"vertical-align: middle;\">".$key."</td>";
        echo "<td style=\"vertical-align: middle;\" class=\"text-center\">".(($isAdmin === 0)? "No" : "Yes")."</td>";
        echo "<td class=\"text-center\">";
        echo "<form id=\"userAction\" action=\"admin.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"username\" value=\"".$key."\">";
        echo "<button type=\"submit\" name=\"action\" value=\"chgPwdForm\" class=\"btn btn-primary\"><span class=\"fa fa-refresh\"></span> <span class=\"fa fa-lock\"></span> Change password</button>\n";
        echo "<button type=\"submit\" name=\"action\" value=\"showQRCode\" class=\"btn btn-primary\"><span class=\"fa fa-barcode\"></span> Show QR code</button>\n";
        echo "<button onclick=\"return confirmGAScrt('".$key."');\"type=\"submit\" name=\"action\" value=\"renewGAuthSecret\" class=\"btn btn-primary \"><span class=\"fa fa-refresh\"></span> <span class=\"fa fa-key\"></span> Renew Secret</button>\n";
        echo "<button onclick=\"return confirmDelete('".$key."');\" type=\"submit\" name=\"action\" value=\"deleteUser\" class=\"btn btn-danger pull-right\"><span class=\"fa fa-trash-o\"></span></button>";
        echo "</form>";
        echo "</td></tr>";
    }
}
?>