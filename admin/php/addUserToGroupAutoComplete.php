<?php
require("checkAuth.php");

$array = array();

$createCon = new dbClass();

if ($createCon)
{}
else
{
    $array["typeOf"] = "createObj";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $createCon->errors;

    echo json_encode($array);
    die();
}

$username = $_GET["term"];

$userAC = $createCon->addUserToGroupAutoComplete($username);

echo json_encode($userAC);