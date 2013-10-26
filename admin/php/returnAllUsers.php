<?php
require("checkAuth.php");

$array = array();

$createCon = new dbClass();
if ($createCon)
{}
else
{
    $array["typeOf"] = "returnAllUsers";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $createCon->errors;

    echo json_encode($array);
    die();
}

$output = $createCon->returnAllUsers();

echo $output;