<?php
require("checkAuth.php");

$getJson = json_decode($_POST["obj"], true);

$userId = $getJson["objUserId"];
$newUsername = $getJson["objUsername"];
$newEmail = $getJson["objEmail"];
$newPassword = $getJson["objPassword"];

$changeUserData = new dbClass();

$changeUserData->changeUserData($userId, $newUsername, $newEmail, $newPassword);

$array = array();

if (!$changeUserData)
{
    $array["typeOf"] = "changeUserData";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $changeUserData->errors;
}
else
{
    $array["typeOf"] = "changeUserData";
    $array["isSuccess"] = 1;
    $array["replyMessage"] = $changeUserData->messages;
}

echo json_encode($array);