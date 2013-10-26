<?php
require("checkAuth.php");

$getJson = json_decode($_POST["obj"], true);

$groupId = $getJson["objGroupId"];
$newGroupname = $getJson["objGroupname"];

$changeGroupData = new dbClass();

$changeGroupData->changeGroupData($groupId, $newGroupname);

$array = array();

if (!$changeGroupData)
{
    $array["typeOf"] = "changeGroupData";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $changeGroupData->errors;
}
else
{
    $array["typeOf"] = "changeGroupData";
    $array["isSuccess"] = 1;
    $array["replyMessage"] = $changeGroupData->messages;
}

echo json_encode($array);