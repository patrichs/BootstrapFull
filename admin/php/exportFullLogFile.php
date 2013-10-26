<?php
require("checkAuth.php");

function download_send_headers($filename)
{
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

function array2txt(array &$array)
{
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    $txtArray = array();
    //fputcsv($df, array_keys(reset($array)));
    foreach ($array as $row) {
        $row = $row . "\r\n";
        fwrite($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}

$returnLogs = new dbClass();
$output = $returnLogs->returnFullLogs();

$array = array();

if (!$returnLogs)
{
    $array["typeOf"] = "returnLogs";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $returnLogs->errors;
    echo json_encode($array);
    die();
}
else
{
    download_send_headers("log_" . date("Y-m-d_H:i:s") . ".txt");
    echo array2txt($output);
    die();
}