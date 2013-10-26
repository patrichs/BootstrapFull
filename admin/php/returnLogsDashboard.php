<?php
require("checkAuth.php");

$limit = $_POST["objLimit"];

$returnLogs = new dbClass();
$output = $returnLogs->returnLogsDashboard($limit);

$array = array();

if (!$returnLogs)
{
    $array["typeOf"] = "returnLogs";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $returnLogs->errors;
    echo json_encode($array);
    die();
}

echo json_encode($output);