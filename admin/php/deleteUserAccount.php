<?php
include ("dbClass.php");

$getJson = json_decode($_POST["obj"], true);

$userId = $getJson["objUserId"];

$deleteUser = new dbClass();

$deleteUser->adminDeleteUser($userId);

$array = array();

if (!$deleteUser)
{
    $array["typeOf"] = "deleteUserAccount";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $deleteUser->errors;
}
else
{
    $array["typeOf"] = "deleteUserAccount";
    $array["isSuccess"] = 1;
    $array["replyMessage"] = $deleteUser->messages;
}

echo json_encode($array);