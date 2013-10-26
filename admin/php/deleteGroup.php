<?php
require("checkAuth.php");

$getJson = json_decode($_POST["obj"], true);

$groupId = $getJson["objGroupId"];

$deleteGroup = new dbClass();

$deleteGroup->adminDeleteGroup($groupId);

$array = array();

if (!$deleteGroup)
{
    $array["typeOf"] = "deleteGroup";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $deleteGroup->errors;
}
else
{
    $array["typeOf"] = "deleteGroup";
    $array["isSuccess"] = 1;
    $array["replyMessage"] = $deleteGroup->messages;
}

echo json_encode($array);