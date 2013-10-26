<?php
/*
 * Event IDs examples:
 * 0: An administrator has added a new user.
 * 1: An administrator has modified user.
 * 2: An administrator has deleted user.
 * 3: An administrator has added a new group.
 * 4: An administrator has modified group.
 * 5: An administrator has deleted group.
 * 6: An administrator has added a user to a group.
 * etc.
 */

require("checkAuth.php");

$getJson = json_decode($_POST["obj"], true);

$eventId = $getJson["objEventId"];
$eventTitle = $getJson["objEventTitle"];
$eventDesc = $getJson["objEventDesc"];

$LogWriter = new dbClass();

$LogWriter->writeToEventLog($eventId, $eventTitle, $eventDesc);

$array = array();

if (!$LogWriter)
{
    $array["typeOf"] = "LogWriter";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $LogWriter->errors;
}
else
{
    $array["typeOf"] = "LogWriter";
    $array["isSuccess"] = 1;
    $array["replyMessage"] = $LogWriter->messages;
}

echo json_encode($array);