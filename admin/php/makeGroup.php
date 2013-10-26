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

if (isset($getJson["addgroup"]))
{
    if ($getJson["addgroup"] == 1)
    {
        /*
         * Spara vals i sina egna vars
         */

        $groupname = $getJson["groupname"];

        $addGroup = $createCon->addGroup($groupname);
        if ($addGroup)
        {
            $array["typeOf"] = "addGroup";
            $array["isSuccess"] = 1;
            $array["replyMessage"] = $createCon->messages;
        }
        else
        {
            $array["typeOf"] = "addGroup";
            $array["isSuccess"] = 0;
            $array["replyMessage"] = $createCon->errors;
        }
    }
}

echo json_encode($array);