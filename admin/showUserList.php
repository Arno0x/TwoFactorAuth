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
    foreach ($userList as $userName => $isAdmin) {
        echo "<tr>";
        echo "<td style=\"vertical-align: middle;\">".$userName."</td>";
        echo "<td style=\"vertical-align: middle;\" class=\"text-center\">".(($isAdmin === 0)? "No" : "Yes")."</td>";
        echo "<td class=\"text-center\">";
        echo "<form id=\"userAction\" action=\"admin.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"csrf_token\" value=\"".$token."\">";
        echo "<input type=\"hidden\" name=\"username\" value=\"".$userName."\">";
        echo "<button type=\"submit\" name=\"action\" value=\"chgPwdForm\" class=\"btn btn-primary\"><span class=\"fa fa-refresh\"></span> <span class=\"fa fa-lock\"></span> Change password</button>\n";
        echo "<button type=\"submit\" name=\"action\" value=\"showQRCode\" class=\"btn btn-primary\"><span class=\"fa fa-qrcode\"></span> Show QR code</button>\n";
        echo "<button onclick=\"return confirmGAScrt('".$userName."');\"type=\"submit\" name=\"action\" value=\"renewGAuthSecret\" class=\"btn btn-primary \"><span class=\"fa fa-refresh\"></span> <span class=\"fa fa-key\"></span> Renew Secret</button>\n";
        echo "<button onclick=\"return confirmDelete('".$userName."');\" type=\"submit\" name=\"action\" value=\"deleteUser\" class=\"btn btn-danger pull-right\"><span class=\"fa fa-trash-o\"></span></button>";
        echo "</form>";
        echo "</td></tr>";
    }
}
?>