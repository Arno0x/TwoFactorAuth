<?php

/**
 * TwoFactorAuth 
 * This script can either be called directly or included in another script
 * 
 * 1. If it's called directly, it is supposed to be passed a uniq id corresponding to
 * a temporary QRCode image. It will return a PNG image.
 * 
 * 2. If it's included from another script, it will prepare the display for the QRCode image
 *    and use the $randomString variable prepared in the calling script
 * 
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//------------------------------------------------------
// Include config file
require_once("../config.php");

if (isset($_GET["id"]) && !defined("INCLUSION_ENABLED")) {
    //-----------------------------------------------------
    // Check "id" to avoid path traversal type of attack
    if (preg_match("/(\.|\/)/",$_GET["id"]) === 0) {
        
        //-----------------------------------------------------
        // Sending no-cache headers
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
        header( 'Content-Type: image/png' );
    
        
        $imgFileName = QRCODE_TEMP_DIR.$_GET["id"].".png";
        $image = imagecreatefrompng ($imgFileName);
        ImagePng($image);
        
        imagedestroy($image);
        
        // Delete the temporary image file
        unlink($imgFileName);
    } else {
        echo "What you think you're doing ??";
    }
}
else {
    //------------------------------------------------------
    // Application base url
    $baseUrl = dirname(dirname($_SERVER["SCRIPT_NAME"]));
    echo <<<OUT
    <a href="#"><span onclick="$('#overlay').fadeOut()" class="fa fa-close pull-right"></span></a>
    <br>
    Scan the following QR Code with your Google Authenticator app:<br>
    <img src="{$baseUrl}/lib/showQRCode.php?id={$randomString}">
OUT;
}
?>