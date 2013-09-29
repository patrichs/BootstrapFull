<?php
include ("dbClass.php");

$userId = $_POST["userid"];
$newUsername = $_POST["newUsername"];
$newEmail = $_POST["newEmail"];
$newPassword = $_POST["newPassword"];

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