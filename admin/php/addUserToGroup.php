<?php
require("checkAuth.php");

$array = array();
$getJson = json_decode($_POST["obj"], true);

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

/*
 * If posted command is "addgroup"
 */

if (isset($getJson["addusertogroup"]))
{
    if ($getJson["addusertogroup"] == 1)
    {
        /*
         * Spara vals i sina egna vars
         */

        $username = $getJson["username"];
        $groupid = $getJson["groupid"];

        $addGroup = $createCon->addUserToGroup($username, $groupid);
        if ($addGroup)
        {
            $array["typeOf"] = "addUserToGroup";
            $array["isSuccess"] = 1;
            $array["replyMessage"] = $createCon->messages;
        }
        else
        {
            $array["typeOf"] = "addUserToGroup";
            $array["isSuccess"] = 0;
            $array["replyMessage"] = $createCon->errors;
        }
    }
}

echo json_encode($array);