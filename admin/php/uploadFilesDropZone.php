<?php

/*
 * WARNING: WHEN DEALING WITH FILES PLEASE NEVER MAKE IT PUBLIC UNLESS YOU KNOW WHAT YOU ARE DOING.
 * OTHER THAN SQL INJECTIONS IT IS THE SECOND MOST POPULAR WAY TO GAIN ACCESS TO INFORMATION/TAKE OVER CONTROL OF YOUR SERVER
 * IF YOU INTEND FOR THIS TO BE PUBLIC FACING, PLEASE READ http://blog.insicdesigns.com/2009/01/secure-file-upload-in-php-web-applications/
 * AND IMPLEMENT EACH STEP INTO THIS SCRIPT. ALSO CONSIDER WHETHER YOUR SERVER IS WINDOWS OR LINUX BASED.
 * (THEY DEAL WITH FILES AND PERMISSIONS DIFFERENTLY)
 *
 * Thank you,
 * Patrich
 */

$ds          = DIRECTORY_SEPARATOR;
$storeFolder = '../uploads'; //move one step down from the php folder and store it in the uploads folder (php folder should be 1 step above root)

if (!empty($_FILES))
{
    $tempFile = $_FILES['file']['tmp_name'];

    $targetPath = dirname( __FILE__ ) . $ds . $storeFolder . $ds;
    $targetFile = $targetPath . $_FILES['file']['name'];

    move_uploaded_file($tempFile, $targetFile);
}
?>